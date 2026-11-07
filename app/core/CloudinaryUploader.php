<?php

class CloudinaryUploader
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;

    public function __construct()
    {
        $env = parse_ini_file(__DIR__ . '/../../.env');

        if ($env === false) {
            throw new RuntimeException('Cannot load environment file');
        }

        $this->cloudName = $env['CLOUDINARY_CLOUD_NAME'] ?? '';
        $this->apiKey = $env['CLOUDINARY_API_KEY'] ?? '';
        $this->apiSecret = $env['CLOUDINARY_API_SECRET'] ?? '';

        if (
            $this->cloudName === '' ||
            $this->apiKey === '' ||
            $this->apiSecret === ''
        ) {
            throw new RuntimeException('Cloudinary credentials are missing');
        }
    }

    public function upload(
        string $filePath,
        string $folder = 'ecommerce/categories'
    ): array {

        $timestamp = time();

        $params = [
            'folder' => $folder,
            'timestamp' => $timestamp
        ];

        // Cloudinary signed upload signature
        $signatureString =
            'folder=' . $folder .
            '&timestamp=' . $timestamp .
            $this->apiSecret;

        $signature = sha1($signatureString);

        $url = 'https://api.cloudinary.com/v1_1/' .
            rawurlencode($this->cloudName) .
            '/image/upload';

        $postData = [
            'file' => new CURLFile($filePath),
            'api_key' => $this->apiKey,
            'timestamp' => $timestamp,
            'folder' => $folder,
            'signature' => $signature
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
           

            throw new RuntimeException(
                'Cloudinary upload failed: ' . $error
            );
        }

        $httpCode = curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );

      

        $result = json_decode($response, true);

        if (
            $httpCode !== 200 ||
            !is_array($result) ||
            empty($result['secure_url']) ||
            empty($result['public_id'])
        ) {
            throw new RuntimeException(
                'Cloudinary rejected the upload'
            );
        }

        return [
            'image_url' => $result['secure_url'],
            'public_id' => $result['public_id']
        ];
    }
}