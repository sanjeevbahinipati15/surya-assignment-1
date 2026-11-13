<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Orders;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::latest()->paginate(10);

        $orders = Orders::with('product')->latest()->get();
        return view('home', compact('products', 'orders'));
    }

    public function store(Request $request)
    {
        // $validatedData = request()->validate([
        //     'name' => 'required|string|max:255',
        //     'code' => 'required|string|max:255|unique:products,code',
        //     'price' => 'required|numeric|min:0',
        //     'selling_price' => 'nullable|numeric|min:0|lte:price',
        //     'quantity' => 'required|integer|min:0',
        //     'image' => 'nullable|image|max:2048',
        //     'minimum_order_quantity' => 'nullable|integer|min:1',
        //     'max_order_quantity' => 'nullable|integer|min:1|max:5',

        // ]);

        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:products,code',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0|lte:price',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'minimum_order_quantity' => 'nullable|integer|min:1',
            'max_order_quantity' => 'nullable|integer|min:1|max:5',
        ])->validate();

        if (request()->hasFile('image')) {
            $imagePath = request()->file('image')->store('product_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $validatedData['discount'] = $validatedData['price'] - ($validatedData['selling_price'] ?? $validatedData['price']);
        $validatedData['discount_in_percentage'] = $validatedData['price'] > 0 ? ($validatedData['discount'] / $validatedData['price']) * 100 : 0;
        $validatedData['status'] = 'active';

        // Create the product using the validated data
        $product = Product::create($validatedData);

        // return response()->json([
        //     'message' => 'Product created successfully',
        //     //'product' => $product,
        // ], 201);

        return redirect()->back()->with('success', 'Product created successfully!');

    }

    public function buy(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $minQty = $product->minimum_order_quantity ?? 1;
        $maxQty = min($product->max_order_quantity ?? $product->quantity, $product->quantity);

        Validator::make(request()->all(), [
            'quantity' => "required|integer|min:$minQty|max:$maxQty",
        ])->validate();

        if ($product->status !== 'active') {
            return redirect()->back()->with('error', 'Product is not available for purchase.');
        }

        if ($product->quantity <= 0) {
            return redirect()->back()->with('error', 'Product is out of stock.');
        }


        $quantity = $request->input('quantity');
        $unitPrice = $product->selling_price ?? $product->price;
        $orderAmount = $unitPrice * $quantity;

        Orders::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'order_amount' => $orderAmount,
        ]);

        $product->decrement('quantity');

        // You can also add logic to handle the purchase, such as creating an order record, etc.

        return redirect()->back()->with('success', 'Product purchased successfully!');
    }

    public function checkout(Request $request)
    {

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'cart_data' => 'required',
        ]);
        // Decode the JSON cart data sent from the form
        $items = json_decode($request->input('cart_data', '[]'), true);

        if (empty($items)) {
            return redirect()->back()->with('error', 'Your order summary is empty.');
        }

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            if (!$product || $product->quantity < $item['qty']) {
                return redirect()->back()->with('error', 'Insufficient stock for one or more products.');
            }

            $orderAmount = $item['unit'] * $item['qty'];

            // Save into the orders table
            Orders::create([
                'product_id' => $product->id,
                'quantity' => $item['qty'],
                'order_amount' => $orderAmount,
                'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_phone' => $request->input('customer_phone'),
            ]);

            // Decrement product stock by the exact ordered quantity
            $product->decrement('quantity', $item['qty']);
        }

        return redirect()->back()->with('success', 'Orders placed and saved to database successfully!');
    }
}
