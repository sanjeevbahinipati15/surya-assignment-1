<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Master & POS Dashboard</title>
    <!-- Bootstrap CSS CDN for clean styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border: none; margin-bottom: 20px; }
        .order-summary-box { background: #fff; }
    </style>
</head>
<body>

    <!-- Navbar / Header -->
    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <span class="navbar-brand mb-0 h1">📦 Product Master & Order Dashboard</span>
        </div>
    </nav>

    <div class="container my-4">

        <!-- Alert Notifications -->
        @if($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="row">
            <!-- Left Column: Product Form & Product List -->
            <div class="col-lg-7">

                <!-- Product Master Form Card -->
                <div class="card p-4">
                    <h4 class="mb-3 text-primary">Add New Product</h4>
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Product Title</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Product Code</label>
                                <input type="text" name="code" id="code" value="{{ old('code') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Regular Price</label>
                                <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="selling_price" class="form-label">Selling Price</label>
                                <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="quantity" class="form-label">Stock Qty</label>
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="minimum_order_quantity" class="form-label">Min Qty</label>
                                <input type="number" name="minimum_order_quantity" id="minimum_order_quantity" value="{{ old('minimum_order_quantity', 1) }}" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="max_order_quantity" class="form-label">Max Qty (Max 5)</label>
                                <input type="number" name="max_order_quantity" id="max_order_quantity" value="{{ old('max_order_quantity', 5) }}" class="form-control" max="5" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" name="image" id="image" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Add Product</button>
                    </form>
                </div>

                <!-- Product List Table Card -->
                <div class="card p-4">
                    <h4 class="mb-3">Product List</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="45" class="rounded">
                                            @else
                                                <span class="text-muted small">No Image</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $product->name }}</strong><br>
                                            <small class="text-muted">{{ $product->code }}</small>
                                        </td>
                                        <td>{{ number_format($product->selling_price ?? $product->price, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addToOrderSummary({{ $product->id }}, '{{ $product->name }}', {{ $product->selling_price ?? $product->price }}, {{ $product->minimum_order_quantity ?? 1 }}, {{ min($product->max_order_quantity ?? 5, $product->quantity) }}, {{ $product->quantity }})">Add</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $products->links() }}
                    </div>
                </div>

            </div>

            <!-- Right Column: Order Summary & Checkout -->
            <div class="col-lg-5">
                <div class="card p-4 order-summary-box sticky-top" style="top: 20px;">
                    <h4 class="mb-3">Order Summary</h4>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle" id="summary-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Cart is empty</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Total:</h5>
                        <h5 class="text-success"><span id="order-total">0.00</span></h5>
                    </div>

                    <!-- Checkout Form -->
                    <form action="{{ route('cart.checkout') }}" method="POST" id="checkout-form">
                        @csrf
                        <input type="hidden" name="cart_data" id="cart_data">

                        <h5 class="mt-4 mb-3 text-secondary">Customer Details</h5>
                        <div class="mb-2">
                            <label for="customer_name" class="form-label small">Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control form-control-sm" required oninput="validateForm()">
                        </div>
                        <div class="mb-2">
                            <label for="customer_email" class="form-label small">Email</label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control form-control-sm" required oninput="validateForm()">
                        </div>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label small">Phone No</label>
                            <input type="text" name="customer_phone" id="customer_phone" class="form-control form-control-sm" required oninput="validateForm()">
                        </div>

                        <button type="submit" id="buy-button" class="btn btn-success w-100 py-2" disabled>Place Order</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Purchase History Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card p-4">
                    <h4 class="mb-3">Purchase History</h4>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>{{ $order->customer_email }}</td>
                                        <td>{{ $order->customer_phone }}</td>
                                        <td>{{ $order->product->name ?? 'Product Deleted' }}</td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>{{ number_format($order->order_amount, 2) }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No purchase history found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- JavaScript for Cart & UI Interaction -->
    <script>
        let orderItems = {};

        function addToOrderSummary(productId, name, unitPrice, minQty, maxQty, stockQty) {
            if (stockQty <= 0) {
                alert('Product is out of stock!');
                return;
            }

            if (!orderItems[productId]) {
                if (minQty > stockQty) {
                    alert('Minimum order quantity exceeds available stock!');
                    return;
                }
                orderItems[productId] = {
                    product_id: productId,
                    name: name,
                    unit: unitPrice,
                    qty: minQty,
                    min: minQty,
                    max: Math.min(maxQty, stockQty),
                    stock: stockQty
                };
            } else {
                if (orderItems[productId].qty < orderItems[productId].max) {
                    orderItems[productId].qty += 1;
                } else {
                    alert('Cannot exceed maximum order quantity or available stock limit!');
                }
            }
            renderOrderSummary();
        }

        function changeQty(productId, delta) {
            if (orderItems[productId]) {
                let newQty = orderItems[productId].qty + delta;
                if (newQty >= orderItems[productId].min && newQty <= orderItems[productId].max) {
                    orderItems[productId].qty = newQty;
                } else if (newQty < orderItems[productId].min) {
                    delete orderItems[productId];
                }
                renderOrderSummary();
            }
        }

        function renderOrderSummary() {
            let tbody = document.querySelector('#summary-table tbody');
            tbody.innerHTML = '';
            let grandTotal = 0;
            let itemsArray = [];

            if (Object.keys(orderItems).length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">Cart is empty</td></tr>`;
                document.getElementById('order-total').innerText = '0.00';
                document.getElementById('cart_data').value = '';
                validateForm();
                return;
            }

            for (let productId in orderItems) {
                let item = orderItems[productId];
                let subtotal = item.unit * item.qty;
                grandTotal += subtotal;
                itemsArray.push(item);

                let row = `<tr>
                    <td>${item.name}</td>
                    <td>${item.unit.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0" onclick="changeQty(${productId}, -1)">-</button>
                        <span class="mx-1">${item.qty}</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0" onclick="changeQty(${productId}, 1)">+</button>
                    </td>
                    <td>${subtotal.toFixed(2)}</td>
                </tr>`;
                tbody.innerHTML += row;
            }

            document.getElementById('order-total').innerText = grandTotal.toFixed(2);
            document.getElementById('cart_data').value = JSON.stringify(itemsArray);
            validateForm();
        }

        function validateForm() {
            let name = document.getElementById('customer_name').value.trim();
            let email = document.getElementById('customer_email').value.trim();
            let phone = document.getElementById('customer_phone').value.trim();
            let buyButton = document.getElementById('buy-button');
            let hasItems = Object.keys(orderItems).length > 0;

            if (name !== '' && email !== '' && phone !== '' && hasItems) {
                buyButton.removeAttribute('disabled');
            } else {
                buyButton.setAttribute('disabled', 'true');
            }
        }
    </script>
</body>
</html>
