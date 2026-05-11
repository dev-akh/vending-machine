@extends('layouts.app')

@section('title', 'Admin Dashboard - Vending Machine')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-tachometer-alt mr-2"></i>Admin Dashboard
        </h1>
        <p class="text-gray-600 mt-2">Manage your vending machine system</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\Product::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Today's Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format(App\Models\Transaction::whereDate('created_at', today())->sum('total_price'), 3) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-shopping-cart text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\Transaction::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\User::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-bolt mr-2"></i>Quick Actions
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.products.create') }}" 
                   class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 text-center">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </a>
                <a href="{{ route('products.index') }}" 
                   class="bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 text-center">
                    <i class="fas fa-list mr-2"></i>View Products
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line mr-2"></i>System Status
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Database Status:</span>
                    <span class="text-green-600 font-medium">
                        <i class="fas fa-check-circle mr-1"></i>Connected
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">System Time:</span>
                    <span class="text-gray-900 font-medium">{{ now()->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Laravel Version:</span>
                    <span class="text-gray-900 font-medium">{{ app()->version() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-history mr-2"></i>Recent Transactions
            </h2>
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unit Price
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantity
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $recentTransactions = App\Models\Transaction::with(['user', 'product'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    @forelse($recentTransactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $transaction->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->product->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($transaction->unit_price, 3) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($transaction->total_price, 3) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No transactions found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
