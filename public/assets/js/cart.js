
const cartItems = document.getElementById("cart-items");
const cartTotal = document.getElementById("cart-total");
const message = document.getElementById("message");

async function loadCart() {
    try {
        const response = await fetch("index.php?api=get-cart");
        const data = await response.json();

        if (response.status === 401) {
    window.location.href = "index.php?page=login";
    return;
}

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to load cart");
        }

        cartItems.innerHTML = "";

        const items = data.items || [];

        if (items.length === 0) {
            cartItems.innerHTML = `
                <tr><td colspan="6">Your cart is empty.</td></tr>
            `;
            cartTotal.textContent = "0.00";
            return;
        }

        let total = 0;

        items.forEach(item => {
            const price = Number(item.price);
            const quantity = Number(item.quantity);
            const subtotal = price * quantity;

            total += subtotal;

            const row = document.createElement("tr");

            // Product name
            const nameCell = document.createElement("td");
            nameCell.textContent = item.name;

            // Size and color
            const variantCell = document.createElement("td");
            variantCell.textContent = item.size + " / " + item.color;

            // Price
            const priceCell = document.createElement("td");
            priceCell.textContent = "$" + price.toFixed(2);

            // Quantity input
            const quantityCell = document.createElement("td");
            const input = document.createElement("input");
            input.type = "number";
            input.min = "1";
            input.value = quantity;
            input.className = "form-control";
            input.style.width = "85px";
            quantityCell.appendChild(input);

            // Subtotal
            const subtotalCell = document.createElement("td");
            subtotalCell.textContent = "$" + subtotal.toFixed(2);

            // Action buttons
            const actionCell = document.createElement("td");

            const updateButton = document.createElement("button");
            updateButton.className = "btn btn-primary btn-sm me-2";
            updateButton.textContent = "Update";

            updateButton.onclick = () => {
                updateQuantity(item.cart_item_id, input.value);
            };

            const removeButton = document.createElement("button");
            removeButton.className = "btn btn-danger btn-sm";
            removeButton.textContent = "Remove";

            removeButton.onclick = () => {
                removeItem(item.cart_item_id);
            };

            actionCell.append(updateButton, removeButton);

            row.append(
                nameCell,
                variantCell,
                priceCell,
                quantityCell,
                subtotalCell,
                actionCell
            );

            cartItems.appendChild(row);
        });

        cartTotal.textContent = total.toFixed(2);

    } catch (error) {
        message.textContent = error.message;
    }
}

// Update quantity
async function updateQuantity(cartItemId, quantity) {
    quantity = Number(quantity);

    if (!Number.isInteger(quantity) || quantity < 1) {
        message.textContent = "Enter a valid quantity.";
        return;
    }

    try {
        const response = await fetch(
            "index.php?api=update-cart-item&id=" + cartItemId,
            {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ quantity: quantity })
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Update failed");
        }

        message.textContent = "Cart updated successfully.";
        loadCart();

    } catch (error) {
        message.textContent = error.message;
    }
}

// Remove item
async function removeItem(cartItemId) {
    try {
        const response = await fetch(
            "index.php?api=remove-cart-item&id=" + cartItemId,
            { method: "DELETE" }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Remove failed");
        }

        message.textContent = "Item removed successfully.";
        loadCart();

    } catch (error) {
        message.textContent = error.message;
    }
}

loadCart();
