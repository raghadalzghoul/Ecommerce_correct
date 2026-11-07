
const params = new URLSearchParams(window.location.search);
const orderId = params.get("id");

const receipt = document.getElementById("receipt");

async function loadReceipt() {

    if (!orderId || !/^\d+$/.test(orderId)) {
        receipt.textContent = "Invalid order ID.";
        return;
    }

    try {
        const response = await fetch(
            "index.php?api=order-details&id=" + orderId
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Order not found");
        }

        const order = data.order;

        receipt.innerHTML = `
            <h4 class="text-success">Order Details</h4>

            <p><strong>Order ID:</strong>
                <span id="order-id"></span>
            </p>

            <p><strong>Status:</strong>
                <span id="order-status"></span>
            </p>

            <p><strong>Payment Method:</strong>
                Cash on Delivery
            </p>

            <p><strong>Shipping Address:</strong>
                <span id="order-address"></span>
            </p>

            <hr>

            <h4>Total: $<span id="order-total"></span></h4>
        `;

        document.getElementById("order-id").textContent = order.order_id;
        document.getElementById("order-status").textContent = order.status;
        document.getElementById("order-address").textContent = order.shipping_address;
        document.getElementById("order-total").textContent = order.total_amount;

    } catch (error) {
        receipt.textContent = error.message;
    }
}

loadReceipt();
