
document.getElementById("login-form").onsubmit = async function (event) {

    event.preventDefault();

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    const response = await fetch("index.php?api=login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            email: email,
            password: password
        })
    });

    const data = await response.json();

    if (response.ok && data.success) {
        window.location.href = "index.php?page=home";
    } else {
        document.getElementById("message").textContent =
            data.message || "Login failed";
    }
};
