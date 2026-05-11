@extends('layouts.app')

@section('title', 'Purchase ' . $product->name . ' - Vending Machine')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Product
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 w-24 h-24 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-cube text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Purchase {{ $product->name }}</h1>
            <div class="text-xl text-gray-600">Complete your purchase below</div>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="purchaseForm" method="POST" action="{{ route('products.process-purchase', $product) }}" class="space-y-6">
            @csrf
            
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">
                    <i class="fas fa-info-circle mr-2"></i>Product Information
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-600">Product:</span>
                        <p class="font-medium">{{ $product->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Unit Price:</span>
                        <p class="font-medium">${{ number_format($product->price, 3) }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Available:</span>
                        <p class="font-medium">{{ $product->quantity_available }} units</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Status:</span>
                        <p class="font-medium text-green-600">In Stock</p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-blue-700">
                    <i class="fas fa-shopping-cart mr-2"></i>Purchase Details
                </h3>
                
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity
                    </label>
                    <input type="number" 
                           id="quantity" 
                           name="quantity" 
                           min="1" 
                           max="{{ $product->quantity_available }}"
                           value="{{ $quantity ?? 1 }}"
                           required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Maximum available: {{ $product->quantity_available }} units</p>
                    @if($quantity && $quantity > $product->quantity_available)
                        <p class="text-sm text-yellow-600 mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Requested quantity ({{ $quantity }}) exceeds available stock. Adjusted to maximum available.
                        </p>
                    @endif
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center p-4 bg-white rounded-lg border">
                        <span class="text-gray-600">Total Price:</span>
                        <span class="text-2xl font-bold text-blue-600" id="totalPrice">
                            ${{ number_format($product->price, 3) }}
                        </span>
                    </div>
                </div>

                <div>
                    <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-2">
                        Amount Paid
                    </label>
                    <input type="number" 
                           id="amount_paid" 
                           name="amount_paid" 
                           min="{{ $product->price }}"
                           step="0.001"
                           placeholder="0.000"
                           required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Enter the amount you will pay</p>
                </div>

                <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-medium">Change to receive:</span>
                        <span class="text-xl font-bold text-green-600" id="changeAmount">
                            $0.000
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('products.show', $product) }}" 
                   class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 text-center font-medium">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" 
                        class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-medium">
                    <i class="fas fa-check mr-2"></i>Complete Purchase
                </button>
            </div>
        </form>
    </div>
</div>

<input type="hidden" id="unitPrice" value="{{ $product->price }}">

@push('scripts')
<script src="{{ asset('js/validation.js') }}"></script>
<script>
const unitPrice = {{ $product->price }};
const maxQuantity = {{ $product->quantity_available }};
const initialQuantity = {{ $quantity ?? 1 }};

function validateQuantity() {
    const quantityInput = document.getElementById('quantity');
    let value = parseInt(quantityInput.value) || 1;
    
    // Ensure quantity is within bounds
    if (value < 1) {
        value = 1;
    } else if (value > maxQuantity) {
        value = maxQuantity;
        showQuantityWarning();
    }
    
    quantityInput.value = value;
    calculateTotal();
    updateMinAmountPaid();
}

function calculateTotal() {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const total = unitPrice * quantity;
    document.getElementById('totalPrice').textContent = '$' + total.toFixed(3);
    
    const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
    const change = Math.max(0, amountPaid - total);
    document.getElementById('changeAmount').textContent = '$' + change.toFixed(3);
}

function updateMinAmountPaid() {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const minAmount = unitPrice * quantity;
    document.getElementById('amount_paid').min = minAmount.toFixed(3);
    document.getElementById('amount_paid').placeholder = minAmount.toFixed(3);
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

// Event listeners
document.getElementById('quantity').addEventListener('input', validateQuantity);
document.getElementById('quantity').addEventListener('change', validateQuantity);
document.getElementById('amount_paid').addEventListener('input', calculateTotal);

// Initialize with the passed quantity
document.getElementById('quantity').value = initialQuantity;
validateQuantity();
</script>
@endpush
@endsection
