
async function loadDashboard() {
    const message = document.getElementById("message");

    try {
        const response = await fetch(
            "index.php?api=admin-dashboard"
        );

        const data = await response.json();

        if (response.status === 401) {
            window.location.href = "index.php?page=login";
            return;
        }

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Access denied");
        }

        const stats = data.statistics;

        document.getElementById("revenue").textContent =
            Number(stats.total_revenue).toFixed(2);

        document.getElementById("orders").textContent =
            stats.orders_count;

        document.getElementById("products").textContent =
            stats.product_count;

        document.getElementById("customers").textContent =
            stats.customers_count;

    } catch (error) {
        message.textContent = error.message;
        message.className = "alert alert-danger";
    }
}

loadDashboard();
