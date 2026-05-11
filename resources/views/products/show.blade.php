@extends('layouts.app')

@section('title', $product->name . ' - Vending Machine')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-48 flex items-center justify-center">
            <i class="fas fa-cube text-white text-6xl"></i>
        </div>
        
        <div class="p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    <div class="text-3xl font-bold text-blue-600">
                        ${{ number_format($product->price, 3) }}
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-4 py-2 rounded-full text-lg font-medium
                        {{ $product->quantity_available > 10 ? 'bg-green-100 text-green-800' : 
                           ($product->quantity_available > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $product->quantity_available }} in stock
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">
                        <i class="fas fa-info-circle mr-2"></i>Product Details
                    </h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Product ID:</dt>
                            <dd class="font-medium">#{{ $product->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Name:</dt>
                            <dd class="font-medium">{{ $product->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Price:</dt>
                            <dd class="font-medium">${{ number_format($product->price, 3) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Available:</dt>
                            <dd class="font-medium">{{ $product->quantity_available }} units</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Status:</dt>
                            <dd class="font-medium">
                                @if($product->quantity_available > 0)
                                    <span class="text-green-600">Available</span>
                                @else
                                    <span class="text-red-600">Out of Stock</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-blue-700">
                        <i class="fas fa-shopping-cart mr-2"></i>Purchase Options
                    </h3>
                    @if($product->quantity_available > 0)
                        <form id="purchaseForm" class="space-y-4">
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantity
                                </label>
                                <input type="number" 
                                       id="quantity" 
                                       name="quantity" 
                                       min="1" 
                                       max="{{ $product->quantity_available }}"
                                       value="1"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-sm text-gray-500 mt-1">Maximum available: {{ $product->quantity_available }} units</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Price
                                </label>
                                <div class="text-2xl font-bold text-blue-600" id="totalPrice">
                                    ${{ number_format($product->price, 3) }}
                                </div>
                            </div>
                            <button type="button" 
                                    onclick="proceedToPurchase()"
                                    class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-medium">
                                <i class="fas fa-credit-card mr-2"></i>Proceed to Purchase
                            </button>
                        </form>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-times-circle text-4xl text-red-500 mb-3"></i>
                            <p class="text-red-600 font-medium">This product is currently out of stock.</p>
                        </div>
                    @endif
                </div>
            </div>

            @if(auth()->user()->isAdmin())
                <div class="border-t pt-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-700">
                            <i class="fas fa-cog mr-2"></i>Admin Actions
                        </h3>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                                <i class="fas fa-edit mr-2"></i>Edit Product
                            </a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                    <i class="fas fa-trash mr-2"></i>Delete Product
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
const unitPrice = {{ $product->price }};
const maxQuantity = {{ $product->quantity_available }};

function validateQuantity() {
    const quantityInput = document.getElementById('quantity');
    let value = parseInt(quantityInput.value) || 1;
    
    // Ensure quantity is within bounds
    if (value < 1) {
        value = 1;
    } else if (value > maxQuantity) {
        value = maxQuantity;
        // Show warning message
        showQuantityWarning();
    }
    
    quantityInput.value = value;
    updateTotalPrice();
}

function updateTotalPrice() {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const total = unitPrice * quantity;
    document.getElementById('totalPrice').textContent = '$' + total.toFixed(3);
}

function showQuantityWarning() {
    const warning = document.createElement('div');
    warning.className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 px-3 py-2 rounded mt-2 text-sm';
    warning.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Maximum available quantity is ' + maxQuantity + ' units';
    
    // Remove existing warning if any
    const existingWarning = document.querySelector('.bg-yellow-100');
    if (existingWarning) {
        existingWarning.remove();
    }
    
    // Add new warning
    document.getElementById('quantity').parentNode.appendChild(warning);
    
    // Remove warning after 3 seconds
    setTimeout(() => {
        if (warning.parentNode) {
            warning.remove();
        }
    }, 3000);
}

function proceedToPurchase() {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    
    // Validate quantity before proceeding
    if (quantity < 1) {
        alert('Please select at least 1 item.');
        return;
    }
    
    if (quantity > maxQuantity) {
        alert('Only ' + maxQuantity + ' items available. Please adjust your quantity.');
        return;
    }
    
    // Redirect to purchase page with quantity parameter
    window.location.href = '{{ route('products.purchase', $product) }}?quantity=' + quantity;
}

// Event listeners
document.getElementById('quantity').addEventListener('input', validateQuantity);
document.getElementById('quantity').addEventListener('change', validateQuantity);

// Initialize
validateQuantity();
</script>
@endpush
@endsection
