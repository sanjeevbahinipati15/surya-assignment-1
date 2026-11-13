<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Product Master</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .order-summary-box {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
        }
    </style>
</head>
<body>

    <?php
    if($errors->any()) {
        echo '<div class="alert alert-danger" role="alert" style="margin: 20px;">';
        foreach ($errors->all() as $error) {
            echo '<p>' . $error . '</p>';
        }
        echo '</div>';
    }
    if (session('success')) {
        echo '<div class="alert alert-success" role="alert" style="margin: 20px">' . session('success') . '</div>';
    }
    if (session('error')) {
        echo '<div class="alert alert-danger" role="alert" style="margin: 20px">' . session('error') . '</div>';
    }
    ?>
    <div class="container mt-5" style="font-size: 30px; font-weight: bold; margin-bottom: 20px;">
        Product Master Form
    </div>
    <form action="{{ route('products.store') }}" method="POST" class="mt-5" enctype="multipart/form-data">
        @csrf
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="name">Product Title</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="code">Product code</label>
            <input type="text" name="code" id="code" value="{{ old('code') }}" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="price">Product Price</label>
            <input type="number" name="price" id="price" value="{{ old('price') }}" class="form-control" required>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="selling_price">Product Selling Price</label>
            <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" class="form-control" required>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="quantity">Product Quantity</label>
            <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" class="form-control" required>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="minimum_order_quantity">Minimum Order Quantity</label>
            <input type="number" name="minimum_order_quantity" id="minimum_order_quantity" class="form-control" required>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="max_order_quantity">Max order quantity</label>
            <input type="number" name="max_order_quantity" id="max_order_quantity" class="form-control" max="5" required>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="image">Product Image</label>
            <input type="file" name="image" id="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary mt-3">Add Product</button>
    </form>


    <div class="container mt-5 mb-5">
        <div class="row">
            <!-- Product List Section -->
            <div class="col-md-7">
                <h2>Product List</h2>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Price</th>
                            <th>Selling Price</th>
                            <th>Stock</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->code }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $product->selling_price }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>{{ $product->minimum_order_quantity }}</td>
                                <td>{{ $product->max_order_quantity }}</td>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="60">
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="addToOrderSummary({{ $product->id }}, '{{ $product->name }}', {{ $product->selling_price ?? $product->price }}, {{ $product->minimum_order_quantity ?? 1 }}, {{ min($product->max_order_quantity ?? 5, $product->quantity) }}, {{ $product->quantity }})">Add</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $products->links() }}
            </div>

            <!-- Order Summary & Customer Details Section -->
            <div class="col-md-5">
                <div class="order-summary-box">
                    <h4>Order Summary</h4>
                    <table class="table mt-3" id="summary-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic items will be added here -->
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Total:</h5>
                        <h5 id="order-total">0.00</h5>
                    </div>

                    <!-- Checkout Form with Customer Details -->
                    <form action="{{ route('cart.checkout') }}" method="POST" id="checkout-form">
                        @csrf
                        <input type="hidden" name="cart_data" id="cart_data">

                        <h5 class="mt-4 mb-3">Customer Details</h5>
                        <div class="form-group mb-2">
                            <label for="customer_name">Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control form-control-sm" required oninput="validateForm()">
                        </div>
                        <div class="form-group mb-2">
                            <label for="customer_email">Email</label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control form-control-sm" required oninput="validateForm()">
                        </div>
                        <div class="form-group mb-3">
                            <label for="customer_phone">Phone No</label>
                            <input type="text" name="customer_phone" id="customer_phone" class="form-control form-control-sm" required oninput="validateForm()">
                        </div>

                        <button type="submit" id="buy-button" class="btn btn-success w-100" disabled>Buy</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Purchase History Section -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h2>Purchase History</h2>
                <table class="table table-bordered table-striped mt-3">
                    <thead>
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
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->customer_email }}</td>
                                <td>{{ $order->customer_phone }}</td>
                                <td>{{ $order->product->name ?? 'Product Deleted' }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>${{ number_format($order->order_amount, 2) }}</td>
                                <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No purchase history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
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

            for (let productId in orderItems) {
                let item = orderItems[productId];
                let subtotal = item.unit * item.qty;
                grandTotal += subtotal;

                itemsArray.push(item);

                let row = `<tr>
                    <td>${item.name}</td>
                    <td>${item.unit.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-outline-secondary btn-sm p-0 px-1" onclick="changeQty(${productId}, -1)">-</button>
                        <span class="mx-1">${item.qty}</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm p-0 px-1" onclick="changeQty(${productId}, 1)">+</button>
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
