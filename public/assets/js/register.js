
const form = document.getElementById("register-form");
const message = document.getElementById("message");

form.onsubmit = async function (event) {
    event.preventDefault();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const password = document.getElementById("password").value;

    try {
        const response = await fetch("index.php?api=register", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                name: name,
                email: email,
                phone: phone,
                password: password
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Registration failed");
        }

        alert("Account created successfully!");

        window.location.href = "index.php?page=login";

    } catch (error) {
        message.textContent = error.message;
        message.className = "text-danger mt-3";
    }
};
