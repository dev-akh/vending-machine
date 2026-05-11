@extends('layouts.app')

@section('title', 'Create Product - Vending Machine')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-plus text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Product</h1>
            <div class="text-xl text-gray-600">Add a new product to the vending machine</div>
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

        <form id="productForm" method="POST" action="{{ route('admin.products.store') }}" class="space-y-6">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Product Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       required
                       maxlength="255"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., Coke, Pepsi, Water">
                <p class="text-sm text-gray-500 mt-1">Enter the name of the product</p>
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                    Price (USD) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           value="{{ old('price') }}"
                           required
                           min="0.001"
                           step="0.001"
                           class="w-full pl-8 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0.000">
                </div>
                <p class="text-sm text-gray-500 mt-1">Set the price in USD (up to 3 decimal places)</p>
            </div>

            <div>
                <label for="quantity_available" class="block text-sm font-medium text-gray-700 mb-2">
                    Available Quantity <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="quantity_available" 
                       name="quantity_available" 
                       value="{{ old('quantity_available', 0) }}"
                       required
                       min="0"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="0">
                <p class="text-sm text-gray-500 mt-1">Number of units available for purchase</p>
            </div>

            <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-700 mb-2">
                    <i class="fas fa-info-circle mr-1"></i>Product Preview
                </h3>
                <div class="bg-white rounded p-4 border border-blue-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-900" id="previewName">Product Name</p>
                            <p class="text-sm text-gray-500">ID: Auto-generated</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-600" id="previewPrice">$0.000</p>
                            <p class="text-sm text-gray-500" id="previewQuantity">0 available</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('products.index') }}" 
                   class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 text-center font-medium">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" 
                        class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-save mr-2"></i>Create Product
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/validation.js') }}"></script>
<script>
function updatePreview() {
    const name = document.getElementById('name').value || 'Product Name';
    const price = parseFloat(document.getElementById('price').value) || 0;
    const quantity = parseInt(document.getElementById('quantity_available').value) || 0;
    
    document.getElementById('previewName').textContent = name;
    document.getElementById('previewPrice').textContent = '$' + price.toFixed(3);
    document.getElementById('previewQuantity').textContent = quantity + ' available';
}

document.getElementById('name').addEventListener('input', updatePreview);
document.getElementById('price').addEventListener('input', updatePreview);
document.getElementById('quantity_available').addEventListener('input', updatePreview);

updatePreview();
</script>
@endpush
@endsection
