
const ordersTable = document.getElementById("orders-table");
const message = document.getElementById("message");

const detailsSection = document.getElementById("order-details");
const orderItems = document.getElementById("order-items");

function showMessage(text, type = "success") {
    message.textContent = text;
    message.className = "alert alert-" + type;
}

function setText(id, value) {
    document.getElementById(id).textContent = value ?? "";
}

// Load all orders
async function loadOrders() {
    try {
        const response = await fetch("index.php?api=admin-orders");
        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to load orders");
        }

        ordersTable.innerHTML = "";

        const orders = data.orders;

        if (orders.length === 0) {
            ordersTable.innerHTML =
                '<tr><td colspan="6">No orders found.</td></tr>';
            return;
        }

        orders.forEach(order => {
            const row = document.createElement("tr");

            const values = [
                order.order_id,
                order.user_id,
                Number(order.total_amount).toFixed(2),
                order.status,
                order.created_at
            ];

            values.forEach(value => {
                const cell = document.createElement("td");
                cell.textContent = value ?? "";
                row.appendChild(cell);
            });

            const actions = document.createElement("td");

            const viewButton = document.createElement("button");
            viewButton.className = "btn btn-outline-dark btn-sm me-2 mb-1";
            viewButton.textContent = "View";
            viewButton.onclick = () => viewOrder(order.order_id);

            actions.appendChild(viewButton);

            // Update status one step at a time
            const nextStatus = {
                pending: "processing",
                processing: "shipped",
                shipped: "delivered"
            };

            if (nextStatus[order.status]) {
                const updateButton = document.createElement("button");
                updateButton.className = "btn btn-primary btn-sm me-2 mb-1";
                updateButton.textContent = "Mark " + nextStatus[order.status];

                updateButton.onclick = () =>
                    updateStatus(order.order_id, nextStatus[order.status]);

                actions.appendChild(updateButton);
            }

            // Cancel only pending or processing orders
            if (order.status === "pending" || order.status === "processing") {
                const cancelButton = document.createElement("button");
                cancelButton.className = "btn btn-danger btn-sm mb-1";
                cancelButton.textContent = "Cancel";

                cancelButton.onclick = () => cancelOrder(order.order_id);

                actions.appendChild(cancelButton);
            }

            row.appendChild(actions);
            ordersTable.appendChild(row);
        });

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

// View order details
async function viewOrder(orderId) {
    try {
        const response = await fetch(
            "index.php?api=admin-order-details&id=" + orderId
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to load order details");
        }

        const order = data.order;
        const items = data.items ?? order.items ?? [];

        setText("detail-id", order.order_id);
        setText("detail-customer", order.user_id);
        setText("detail-address", order.shipping_address);
        setText("detail-payment", order.payment_method);
        setText("detail-status", order.status);
        setText("detail-total", Number(order.total_amount).toFixed(2));

        orderItems.innerHTML = "";

        items.forEach(item => {
            const row = document.createElement("tr");

            const price = Number(item.unit_price);
            const quantity = Number(item.quantity);

            const values = [
                item.name ?? item.product_name ?? item.variant_id,
                item.size,
                item.color,
                quantity,
                price.toFixed(2),
                (price * quantity).toFixed(2)
            ];

            values.forEach(value => {
                const cell = document.createElement("td");
                cell.textContent = value ?? "";
                row.appendChild(cell);
            });

            orderItems.appendChild(row);
        });

        detailsSection.hidden = false;
        detailsSection.scrollIntoView({ behavior: "smooth" });

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

// Update order status
async function updateStatus(orderId, newStatus) {
    if (!confirm("Change order status to " + newStatus + "?")) {
        return;
    }

    try {
        const response = await fetch(
            "index.php?api=admin-update-order-status&id=" + orderId,
            {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ status: newStatus })
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to update status");
        }

        showMessage("Order status updated successfully!");
        detailsSection.hidden = true;
        await loadOrders();

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

// Cancel order
async function cancelOrder(orderId) {
    if (!confirm("Are you sure you want to cancel this order?")) {
        return;
    }

    try {
        const response = await fetch(
            "index.php?api=admin-cancel-order&id=" + orderId,
            {
                method: "PATCH"
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to cancel order");
        }

        showMessage("Order cancelled successfully!");
        detailsSection.hidden = true;
        await loadOrders();

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

loadOrders();
