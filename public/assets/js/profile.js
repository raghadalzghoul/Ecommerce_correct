
const profile = document.getElementById("profile");
const message = document.getElementById("message");
const logoutButton = document.getElementById("logout-btn");

// Get user information
async function loadProfile() {
    try {
        const response = await fetch("index.php?api=me");

        const data = await response.json();

        if (response.status === 401) {
            window.location.href = "index.php?page=login";
            return;
        }

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to load profile");
        }

        const user = data.user;

        profile.innerHTML = `
            <p><strong>Name:</strong> <span id="user-name"></span></p>
            <p><strong>Email:</strong> <span id="user-email"></span></p>
            <p><strong>Phone:</strong> <span id="user-phone"></span></p>
            <p><strong>Role:</strong> <span id="user-role"></span></p>
        `;

        document.getElementById("user-name").textContent = user.name || "";
        document.getElementById("user-email").textContent = user.email || "";
        document.getElementById("user-phone").textContent = user.phone || "Not provided";
        document.getElementById("user-role").textContent = user.role || "";

        logoutButton.disabled = false;

    } catch (error) {
        message.textContent = error.message;
    }
}

// Logout
logoutButton.onclick = async function () {
    logoutButton.disabled = true;

    try {
        const response = await fetch("index.php?api=logout", {
            method: "POST"
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Logout failed");
        }

        window.location.href = "index.php?page=login";

    } catch (error) {
        message.textContent = error.message;
        logoutButton.disabled = false;
    }
};

loadProfile();
