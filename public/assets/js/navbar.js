
const logoutButton = document.getElementById("navbar-logout");

if (logoutButton) {
    logoutButton.onclick = async function () {
        try {
            const response = await fetch("index.php?api=logout", {
                method: "POST"
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                alert(data.message || "Logout failed");
                return;
            }

            window.location.href = "index.php?page=login";

        } catch (error) {
            alert("Something went wrong. Please try again.");
        }
    };
}
