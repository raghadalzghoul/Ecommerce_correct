
const form = document.getElementById("checkout-form");
const message = document.getElementById("message");
const button = document.getElementById("place-order-btn");

form.onsubmit = async function (event) {
    event.preventDefault();

    const address = document.getElementById("shipping-address").value.trim();

    // Check shipping address
    if (!address) {
        message.textContent = "Please enter your shipping address.";
        return;
    }

    button.disabled = true;
    message.textContent = "";
    message.className = "mt-3";

    try {
        // Send order to backend
        const response = await fetch("index.php?api=place-order", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                shipping_address: address,
                payment_method: "cod"
            })
        });

        const data = await response.json();

        console.log("Place Order Response:", data);

        // Check response
        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to place order");
        }

        // Get order ID
        const orderId = data.order_id ?? data.order?.order_id;

        // Check if order ID exists
        if (!orderId) {
            message.textContent =
                "Order submitted, but order ID is missing. Please check your orders.";

            message.className = "alert alert-warning mt-3";

            // Keep button disabled to avoid duplicate orders
            return;
        }

        // Go to receipt page
        window.location.href =
            "index.php?page=receipt&id=" + encodeURIComponent(orderId);

    } catch (error) {
        message.textContent = error.message;
        message.className = "alert alert-danger mt-3";
        button.disabled = false;
    }
};
