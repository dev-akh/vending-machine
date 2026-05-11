<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sort_by')) {
            $direction = $request->get('direction', 'asc');
            switch ($request->sort_by) {
                case 'name':
                    $query->orderByName($direction);
                    break;
                case 'price':
                    $query->orderByPrice($direction);
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('products.create');
    }

    public function store(ProductRequest $request)
    {
        $this->authorizeAdmin();

        Product::create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorizeAdmin();
        return view('products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->authorizeAdmin();

        $product->update($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeAdmin();

        // Check if product has existing transactions
        $transactionCount = \App\Models\Transaction::where('product_id', $product->id)->count();
        
        if ($transactionCount > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Cannot delete product. This product has ' . $transactionCount . ' transaction(s) associated with it. Products with sales history cannot be deleted.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function purchase(Product $product, Request $request)
    {
        // Get quantity from URL parameter, default to 1
        $quantity = $request->get('quantity', 1);
        
        // Validate quantity
        if ($quantity < 1) {
            $quantity = 1;
        } elseif ($quantity > $product->quantity_available) {
            $quantity = $product->quantity_available;
        }
        
        return view('products.purchase', compact('product', 'quantity'));
    }

    public function processPurchase(PurchaseRequest $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $validated = $request->validated();
        $quantity = $validated['quantity'];
        $amountPaid = $validated['amount_paid'];

        $totalPrice = $product->price * $quantity;
        $change = $amountPaid - $totalPrice;

        DB::beginTransaction();
        try {
            $product->decreaseQuantity($quantity);

            Transaction::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'unit_price' => $product->price,
                'quantity' => $quantity,
                'total_price' => $totalPrice,
                'amount_paid' => $amountPaid,
                'change_given' => $change,
                'status' => 'completed',
            ]);

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', "Purchase successful! Change: $" . number_format($change, 3));

        } catch (\Exception $e) {
            DB::rollback();
            throw ValidationException::withMessages([
                'purchase' => 'Purchase failed. Please try again.',
            ]);
        }
    }

    private function authorizeAdmin()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
