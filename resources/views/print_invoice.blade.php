<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->id }} - Surya International</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #fff; color: #000; font-size: 14px; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container my-5" style="max-width: 800px;">
        <!-- Header & Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Surya International</h3>
                <small class="text-muted">POS & Order Invoice</small>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-primary btn-sm">Print Again</button>
                <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">Back to Home</a>
            </div>
        </div>

        <hr>

        <!-- Order & Customer Meta Info -->
        <div class="row mb-4">
            <div class="col-6">
                <h6 class="text-muted">Customer Details:</h6>
                <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
            </div>
            <div class="col-6 text-end">
                <h6 class="text-muted">Order Info:</h6>
                <p class="mb-1"><strong>Order ID:</strong> #{{ $order->id }}</p>
                <p class="mb-1"><strong>Date:</strong> {{ $order->created_at->format('d-m-Y H:i') }}</p>
            </div>
        </div>

        <!-- Purchased Items Table -->
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name ?? 'Product Deleted' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                    <td class="text-end fw-bold text-success">{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer Note -->
        <div class="text-center mt-5 text-muted small">
            <p class="mb-0">Thank you for your business with Surya International!</p>
        </div>
    </div>

</body>
</html>
