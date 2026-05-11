@extends('layouts.app')

@section('title', 'Products - Vending Machine')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-box mr-2"></i>Products
        </h1>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Add Product
            </a>
        @endif
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <div class="relative">
                    <input type="text" 
                           id="liveSearch" 
                           placeholder="Search products... (Ctrl+K)" 
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 pl-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <div id="searchSpinner" class="absolute right-3 top-3 hidden">
                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                    </div>
                </div>
                {{-- <div id="searchResults" class="text-sm text-gray-600 mt-1"></div> --}}
            </div>
            <div class="flex gap-2">
                <select id="sortBy" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="name" {{ (request('sort_by') ?: 'name') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                    <option value="quantity" {{ request('sort_by') == 'quantity' ? 'selected' : '' }}>Stock</option>
                </select>
                <select id="sortDirection" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="asc" {{ (request('direction') ?: 'asc') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
                <button id="clearFilters" 
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-all duration-200"
                        title="Clear all filters (Esc or Ctrl+Enter)">
                    <i class="fas fa-times mr-2"></i>Clear
                    <span class="text-xs opacity-75 ml-1">(Esc)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow" 
                 data-product-id="{{ $product->id }}"
                 data-product-name="{{ Str::lower($product->name) }}"
                 data-product-price="{{ $product->price }}"
                 data-product-quantity="{{ $product->quantity_available }}">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-32 flex items-center justify-center">
                    <i class="fas fa-cube text-white text-4xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">{{ $product->name }}</h3>
                    <div class="text-2xl font-bold text-blue-600 mb-4">
                        ${{ number_format($product->price, 3) }}
                    </div>
                    <div class="mb-4">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                            {{ $product->quantity_available > 10 ? 'bg-green-100 text-green-800' : 
                               ($product->quantity_available > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $product->quantity_available }} available
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('products.show', $product) }}" 
                           class="flex-1 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-center">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        @if($product->quantity_available > 0)
                            <a href="{{ route('products.purchase', $product) }}" 
                               class="flex-1 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-center">
                                <i class="fas fa-shopping-cart mr-1"></i>Buy
                            </a>
                        @else
                            <button disabled class="flex-1 bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed">
                                <i class="fas fa-times mr-1"></i>Out of Stock
                            </button>
                        @endif
                    </div>
                    @if(auth()->user()->isAdmin())
                        @php
                            $transactionCount = \App\Models\Transaction::where('product_id', $product->id)->count();
                        @endphp
                        <div class="mt-3 pt-3 border-t">
                            <div class="flex gap-2 mb-2">
                                <a href="{{ route('admin.products.edit', $product) }}" 
                                   class="flex-1 bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700 text-center text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                @if($transactionCount > 0)
                                    <button disabled class="flex-1 bg-gray-400 text-white px-3 py-1 rounded cursor-not-allowed text-sm" 
                                            title="Cannot delete: Product has {{ $transactionCount }} transaction(s)">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex-1 bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                            @if($transactionCount > 0)
                                <div class="text-xs text-yellow-600 bg-yellow-50 px-2 py-1 rounded">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Has {{ $transactionCount }} sale(s) - Cannot delete
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No products found.</p>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.products.create') }}" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Add First Product
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
// Live Search Functionality
class LiveSearch {
    constructor() {
        this.searchInput = document.getElementById('liveSearch');
        this.sortBy = document.getElementById('sortBy');
        this.sortDirection = document.getElementById('sortDirection');
        this.clearButton = document.getElementById('clearFilters');
        this.productsGrid = document.getElementById('productsGrid');
        this.searchResults = document.getElementById('searchResults');
        this.searchSpinner = document.getElementById('searchSpinner');
        this.allProducts = [];
        
        this.init();
    }
    
    init() {
        // Store all products data
        this.allProducts = Array.from(document.querySelectorAll('[data-product-id]')).map(el => ({
            element: el,
            id: el.dataset.productId,
            name: el.dataset.productName,
            price: parseFloat(el.dataset.productPrice),
            quantity: parseInt(el.dataset.productQuantity)
        }));
        
        // Set default sorting if not already set
        if (!this.sortBy.value) {
            this.sortBy.value = 'name';
        }
        if (!this.sortDirection.value) {
            this.sortDirection.value = 'asc';
        }
        
        // Event listeners
        this.searchInput.addEventListener('input', this.debounce(this.handleSearch.bind(this), 300));
        this.sortBy.addEventListener('change', this.handleSort.bind(this));
        this.sortDirection.addEventListener('change', this.handleSort.bind(this));
        this.clearButton.addEventListener('click', this.clearFilters.bind(this));
        
        // Initial render with default sorting
        this.filterAndSortProducts(this.searchInput.value.toLowerCase().trim());
        this.updateSearchResults(this.searchInput.value.toLowerCase().trim());
    }
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    handleSearch() {
        const searchTerm = this.searchInput.value.toLowerCase().trim();
        this.showSpinner();
        
        // Use requestAnimationFrame for smoother performance
        requestAnimationFrame(() => {
            this.filterAndSortProducts(searchTerm);
            this.hideSpinner();
            this.updateSearchResults(searchTerm);
            this.updateURL(searchTerm);
        });
    }
    
    handleSort() {
        const sortBy = this.sortBy.value;
        const direction = this.sortDirection.value;
        
        // Immediate visual feedback
        this.showSortAnimation();
        
        requestAnimationFrame(() => {
            this.filterAndSortProducts(this.searchInput.value.toLowerCase().trim());
            this.updateURL(this.searchInput.value.toLowerCase().trim());
        });
    }
    
    filterAndSortProducts(searchTerm) {
        // Step 1: Filter products
        let filtered = this.allProducts;
        
        if (searchTerm) {
            // Optimized filtering with early exit
            filtered = this.allProducts.filter(product => {
                // Check name first (most common search)
                if (product.name.includes(searchTerm)) return true;
                // Then check price and quantity
                return product.price.toString().includes(searchTerm) || 
                       product.quantity.toString().includes(searchTerm);
            });
        }
        
        // Step 2: Sort if needed
        const sortBy = this.sortBy.value;
        const direction = this.sortDirection.value;
        
        if (sortBy && filtered.length > 0) {
            // Use optimized sorting with localeCompare for strings
            filtered.sort((a, b) => {
                let aVal, bVal;
                
                switch(sortBy) {
                    case 'name':
                        // Use localeCompare for better string comparison
                        return direction === 'desc' 
                            ? b.name.localeCompare(a.name)
                            : a.name.localeCompare(b.name);
                    case 'price':
                        aVal = a.price;
                        bVal = b.price;
                        break;
                    case 'quantity':
                        aVal = a.quantity;
                        bVal = b.quantity;
                        break;
                    default:
                        return 0;
                }
                
                // Numeric comparison
                if (direction === 'desc') {
                    return bVal - aVal;
                } else {
                    return aVal - bVal;
                }
            });
        }
        
        this.currentProducts = filtered;
        this.renderProducts();
    }
    
    showSortAnimation() {
        const sortSelect = this.sortBy;
        sortSelect.classList.add('ring-2', 'ring-blue-500');
        
        setTimeout(() => {
            sortSelect.classList.remove('ring-2', 'ring-blue-500');
        }, 200);
    }
    
    renderProducts() {
        const products = this.currentProducts || this.allProducts;
        
        // Hide all products first
        this.allProducts.forEach(product => {
            product.element.style.display = 'none';
        });
        
        // Show filtered/sorted products
        products.forEach(product => {
            product.element.style.display = '';
        });
        
        // Update empty state
        this.updateEmptyState(products.length);
    }
    
    updateEmptyState(count) {
        let emptyState = document.querySelector('.empty-state');
        
        if (count === 0) {
            if (!emptyState) {
                emptyState = document.createElement('div');
                emptyState.className = 'empty-state col-span-full text-center py-12';
                emptyState.innerHTML = `
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No products found matching your search.</p>
                    <button onclick="document.getElementById('clearFilters').click()" class="mt-4 text-blue-600 hover:text-blue-800">
                        Clear filters
                    </button>
                `;
                this.productsGrid.appendChild(emptyState);
            }
        } else if (emptyState) {
            emptyState.remove();
        }
    }
    
    updateSearchResults(searchTerm) {
        const count = this.currentProducts ? this.currentProducts.length : this.allProducts.length;
        const total = this.allProducts.length;
        
        if (searchTerm) {
            this.searchResults.innerHTML = `Found ${count} of ${total} products`;
            this.searchResults.className = 'text-sm text-blue-600 mt-1';
        } else {
            this.searchResults.innerHTML = `Showing ${total} products`;
            this.searchResults.className = 'text-sm text-gray-600 mt-1';
        }
    }
    
    clearFilters() {
        // Clear search input but set default sorting
        this.searchInput.value = '';
        this.sortBy.value = 'name';  // Default sort by name
        this.sortDirection.value = 'asc';  // Default direction
        
        // Visual feedback
        this.showClearAnimation();
        
        // Reset to original state with default sorting
        this.currentProducts = null;
        this.filterAndSortProducts('');  // Apply default sorting
        this.updateSearchResults('');
        
        // Update URL with default sorting
        this.updateURL('');
        
        // Focus back to search input
        this.searchInput.focus();
    }
    
    showClearAnimation() {
        const button = this.clearButton;
        button.classList.add('scale-95');
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Cleared!';
        
        setTimeout(() => {
            button.classList.remove('scale-95');
            button.innerHTML = '<i class="fas fa-times mr-2"></i>Clear';
        }, 300);
    }
    
    updateURL(searchTerm) {
        const url = new URL(window.location);
        const params = new URLSearchParams();
        
        // Always include sorting parameters if they're not defaults
        if (this.sortBy.value && this.sortBy.value !== 'name') {
            params.set('sort_by', this.sortBy.value);
        }
        if (this.sortDirection.value && this.sortDirection.value !== 'asc') {
            params.set('direction', this.sortDirection.value);
        }
        
        // Include search term if it exists
        if (searchTerm) {
            params.set('search', searchTerm);
        }
        
        // Set URL parameters or clear if empty
        if (params.toString()) {
            url.search = params.toString();
        } else {
            url.search = '';
        }
        
        // Update URL without page reload
        window.history.replaceState({}, '', url);
    }
    
    showSpinner() {
        this.searchSpinner.classList.remove('hidden');
    }
    
    hideSpinner() {
        this.searchSpinner.classList.add('hidden');
    }
}

// Initialize live search when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const liveSearchInstance = new LiveSearch();
    
    // Store instance on the search input for global access
    document.getElementById('liveSearch')._liveSearchInstance = liveSearchInstance;
});

// Handle URL parameters for initial state
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const search = urlParams.get('search');
    const sortBy = urlParams.get('sort_by') || 'name';  // Default to name
    const direction = urlParams.get('direction') || 'asc';  // Default to asc
    
    if (search) {
        document.getElementById('liveSearch').value = search;
    }
    if (sortBy) {
        document.getElementById('sortBy').value = sortBy;
    }
    if (direction) {
        document.getElementById('sortDirection').value = direction;
    }
    
    // Always trigger initial search/sort to ensure proper state
    setTimeout(() => {
        const liveSearch = document.getElementById('liveSearch');
        if (liveSearch && liveSearch._liveSearchInstance) {
            liveSearch._liveSearchInstance.filterAndSortProducts(search || '');
            liveSearch._liveSearchInstance.updateSearchResults(search || '');
        }
    }, 100);
});

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape key clears filters
    if (e.key === 'Escape') {
        const clearButton = document.getElementById('clearFilters');
        if (clearButton) {
            clearButton.click();
        }
    }
    
    // Ctrl/Cmd + K focuses search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('liveSearch');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    // Ctrl/Cmd + Enter clears filters
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        const clearButton = document.getElementById('clearFilters');
        if (clearButton) {
            clearButton.click();
        }
    }
});
</script>
@endpush
@endsection
