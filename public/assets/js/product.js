
const params = new URLSearchParams(window.location.search);
const productId = params.get("id");

const container = document.getElementById("product-container");
const message = document.getElementById("message");

async function loadProduct() {
    try {
        // 1. Get product
        const response = await fetch(
            `index.php?api=product&id=${productId}`
        );

        const data = await response.json();
        const product = data.product;

        if (!response.ok || !data.success) {
            throw new Error("Product not found");
        }

        // 2. Get product variants
        const variantResponse = await fetch(
            `index.php?api=product-variants&id=${productId}`
        );

        const variantData = await variantResponse.json();

        if (!variantResponse.ok || !variantData.success) {
            throw new Error("Failed to load variants");
        }

        const variants = variantData.variants;

        // 3. Create variant options
        let options = "";

        variants.forEach(variant => {
            if (variant.quantity > 0) {
                options += `
                    <option value="${variant.variant_id}">
                        ${variant.size} - ${variant.color}
                        (Stock: ${variant.quantity})
                    </option>
                `;
            }
        });

        // 4. Display product
        container.innerHTML = `
            <div class="col-md-6">
                <img
                    src="${product.image_url || 'https://placehold.co/500x400?text=No+Image'}"
                    class="img-fluid rounded border"
                    alt="Product Image"
                >
            </div>

            <div class="col-md-6">
                <h2 id="product-name"></h2>
                <h4 class="my-3" id="product-price"></h4>
                <p id="product-description"></p>

                <label class="form-label">Size / Color</label>
                <select id="variant" class="form-select mb-3">
                    ${options}
                </select>

                <label class="form-label">Quantity</label>
                <input
                    type="number"
                    id="quantity"
                    class="form-control mb-3"
                    value="1"
                    min="1"
                >

                <button
                    id="add-to-cart"
                    class="btn btn-dark"
                    ${options === "" ? "disabled" : ""}
                >
                    ${options === "" ? "Out of Stock" : "Add to Cart"}
                </button>
            </div>
        `;

        document.getElementById("product-name").textContent = product.name;
        document.getElementById("product-price").textContent = "$" + product.price;
        document.getElementById("product-description").textContent = product.description;

        // 5. Add to cart
        document.getElementById("add-to-cart").onclick = async function () {
            const variantId = document.getElementById("variant").value;
            const quantity = Number(
                document.getElementById("quantity").value
            );

            const selected = variants.find(
                variant => variant.variant_id == variantId
            );

            if (
                !selected ||
                !Number.isInteger(quantity) ||
                quantity < 1 ||
                quantity > selected.quantity
            ) {
                message.textContent = "Invalid quantity";
                return;
            }

            try {
                const cartResponse = await fetch(
                    "index.php?api=add-to-cart",
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            variant_id: Number(variantId),
                            quantity: quantity
                        })
                    }
                );

                const cartData = await cartResponse.json();

                if (cartResponse.ok && cartData.success) {
                    message.textContent = "Added to cart successfully!";
                } else {
                    message.textContent = cartData.message || "Failed to add to cart";
                }

            } catch (error) {
                message.textContent = "Something went wrong";
            }
        };

    } catch (error) {
        container.textContent = error.message;
    }
}

loadProduct();
