<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\Api\TransactionResource;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Product ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Product name",
 *         example="Coca Cola"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Product description",
 *         example="Refreshing cola drink"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="decimal",
 *         description="Product price",
 *         example=3.990
 *     ),
 *     @OA\Property(
 *         property="quantity_available",
 *         type="integer",
 *         description="Available quantity",
 *         example=50
 *     ),
 *     @OA\Property(
 *         property="is_available",
 *         type="boolean",
 *         description="Product availability",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="Transaction",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Transaction ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="User ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="product_id",
 *         type="integer",
 *         description="Product ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="unit_price",
 *         type="number",
 *         format="decimal",
 *         description="Unit price at time of purchase",
 *         example=3.990
 *     ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         description="Quantity purchased",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="total_price",
 *         type="number",
 *         format="decimal",
 *         description="Total price",
 *         example=7.980
 *     ),
 *     @OA\Property(
 *         property="amount_paid",
 *         type="number",
 *         format="decimal",
 *         description="Amount paid by customer",
 *         example=10.000
 *     ),
 *     @OA\Property(
 *         property="change_given",
 *         type="number",
 *         format="decimal",
 *         description="Change given to customer",
 *         example=2.020
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"completed", "pending", "failed"},
 *         description="Transaction status",
 *         example="completed"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp"
 *     )
 * )
 */
class ProductsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="List products",
     *     description="Get a paginated list of products with optional filtering and sorting",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search products by name",
     *         required=false,
     *         @OA\Schema(type="string", example="Cola")
     *     ),
     *     @OA\Parameter(
     *         name="available_only",
     *         in="query",
     *         description="Filter only available products (quantity > 0)",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"name", "price", "quantity_available", "created_at"},
     *             example="name"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="direction",
     *         in="query",
     *         description="Sort direction",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             example="asc"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (default: 20, max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=20)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="current_page",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Product")
     *                 ),
     *                 @OA\Property(
     *                     property="total",
     *                     type="integer",
     *                     example=25
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::query();

            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%');
                });
            }

            if ($request->boolean('available_only')) {
                $query->where('quantity_available', '>', 0);
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $direction = $request->get('direction', 'desc');
            
            $allowedSorts = ['name', 'price', 'quantity_available', 'created_at'];
            if (!in_array($sortBy, $allowedSorts)) {
                $sortBy = 'created_at';
            }
            
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'desc';
            }
            
            $query->orderBy($sortBy, $direction);

            $perPage = min(max($request->get('per_page', 20), 1), 100);
            $products = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => ProductResource::collection($products)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve products.'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Get product",
     *     description="Get detailed information about a specific product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Product"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Product not found"
     *             )
     *         )
     *     )
     * )
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create product",
     *     description="Create a new product (requires admin privileges)",
     *     tags={"Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","quantity_available"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 maxLength=255,
     *                 description="Product name",
     *                 example="Coca Cola"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 maxLength=1000,
     *                 description="Product description",
     *                 example="Refreshing cola drink"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0.001,
     *                 maximum=99999.999,
     *                 description="Product price",
     *                 example=3.990
     *             ),
     *             @OA\Property(
     *                 property="quantity_available",
     *                 type="integer",
     *                 minimum=0,
     *                 maximum=999999,
     *                 description="Available quantity",
     *                 example=50
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Product created successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Product"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Access forbidden"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation failed"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "name": {"The name field is required."},
     *                     "price": {"The price must be at least 0.001."}
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:products,name',
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0.001|max:999999.999',
                'quantity_available' => 'required|integer|min:0|max:999999',
            ], [
                'name.required' => 'Product name is required.',
                'name.unique' => 'A product with this name already exists.',
                'price.min' => 'Price must be at least 0.001.',
                'price.max' => 'Price cannot exceed 99999.999.',
                'quantity_available.min' => 'Quantity cannot be negative.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $product = Product::create($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => new ProductResource($product)
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create product.'
            ], 500);
        }
    }

    /**
     * Update product
     * 
     * Update product information (requires admin privileges)
     * 
     * @authenticated
     * 
     * @urlParam product integer required Product ID. Example: 1
     * @bodyParam name string optional Product name (max 255 chars). Example: Coca Cola
     * @bodyParam description string optional Product description. Example: Refreshing cola drink
     * @bodyParam price number optional Product price (0.001-99999.999). Example: 3.990
     * @bodyParam quantity_available integer optional Available quantity (0-999999). Example: 50
     * 
     * @response 200 {
     *   "status": "success",
     *   "message": "Product updated successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Coca Cola",
     *     "description": "Refreshing cola drink",
     *     "price": 3.990,
     *     "quantity_available": 50,
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     * 
     * @response 403 {
     *   "status": "error",
     *   "message": "Access forbidden"
     * }
     * 
     * @response 404 {
     *   "status": "error",
     *   "message": "Product not found"
     * }
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorizeAdmin();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255|unique:products,name,' . $product->id,
                'description' => 'nullable|string|max:1000',
                'price' => 'sometimes|required|numeric|min:0.001|max:99999.999',
                'quantity_available' => 'sometimes|required|integer|min:0|max:999999',
            ], [
                'name.unique' => 'A product with this name already exists.',
                'price.min' => 'Price must be at least 0.001.',
                'price.max' => 'Price cannot exceed 99999.999.',
                'quantity_available.min' => 'Quantity cannot be negative.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $product->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully',
                'data' => $product
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update product.'
            ], 500);
        }
    }

    /**
     * Delete product
     * 
     * Delete a product (requires admin privileges)
     * 
     * @authenticated
     * 
     * @urlParam product integer required Product ID. Example: 1
     * 
     * @response 200 {
     *   "status": "success",
     *   "message": "Product deleted successfully"
     * }
     * 
     * @response 403 {
     *   "status": "error",
     *   "message": "Access forbidden"
     * }
     * 
     * @response 404 {
     *   "status": "error",
     *   "message": "Product not found"
     * }
     * 
     * @response 409 {
     *   "status": "error",
     *   "message": "Cannot delete product. This product has 5 transaction(s) associated with it."
     * }
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorizeAdmin();

        try {
            $transactionCount = Transaction::where('product_id', $product->id)->count();
            
            if ($transactionCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete product. This product has ' . $transactionCount . ' transaction(s) associated with it.'
                ], 409);
            }

            $product->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete product.'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/products/{id}/purchase",
     *     summary="Purchase product",
     *     description="Process a product purchase transaction",
     *     tags={"Products"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity","amount_paid"},
     *             @OA\Property(
     *                 property="quantity",
     *                 type="integer",
     *                 minimum=1,
     *                 description="Quantity to purchase",
     *                 example=2
     *             ),
     *             @OA\Property(
     *                 property="amount_paid",
     *                 type="number",
     *                 format="decimal",
     *                 minimum=0.001,
     *                 description="Amount paid by customer",
     *                 example=10.000
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Purchase processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Purchase successful"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="transaction",
     *                     ref="#/components/schemas/Transaction"
     *                 ),
     *                 @OA\Property(
     *                     property="change_given",
     *                     type="number",
     *                     format="decimal",
     *                     example=2.020
     *                 ),
     *                 @OA\Property(
     *                     property="product_remaining",
     *                     type="integer",
     *                     example=48
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Product not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation failed"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "quantity": {"The quantity must be at least 1."},
     *                     "amount_paid": {"The amount paid must be at least 0.001."}
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function purchase(Request $request, Product $product): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1|max:' . $product->quantity_available,
                'amount_paid' => 'required|numeric|min:0.001',
            ], [
                'quantity.max' => 'Cannot purchase more than ' . $product->quantity_available . ' items.',
                'amount_paid.min' => 'Amount paid must be at least 0.001.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();
            $quantity = $validated['quantity'];
            $amountPaid = $validated['amount_paid'];

            $totalPrice = $product->price * $quantity;

            if ($amountPaid < $totalPrice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Amount paid is insufficient',
                    'required_amount' => $totalPrice,
                    'shortfall' => $totalPrice - $amountPaid
                ], 422);
            }

            $change = $amountPaid - $totalPrice;

            DB::beginTransaction();
            try {
                $product->decreaseQuantity($quantity);

                $transaction = Transaction::create([
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

                return response()->json([
                    'status' => 'success',
                    'message' => 'Purchase successful',
                    'data' => [
                        'transaction' => new TransactionResource($transaction),
                        'change_given' => $change,
                        'product_remaining' => $product->fresh()->quantity_available
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/products/statistics",
     *     summary="Product statistics",
     *     description="Get product and sales statistics",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="total_products",
     *                     type="integer",
     *                     description="Total number of products",
     *                     example=25
     *                 ),
     *                 @OA\Property(
     *                     property="available_products",
     *                     type="integer",
     *                     description="Products with quantity > 0",
     *                     example=20
     *                 ),
     *                 @OA\Property(
     *                     property="out_of_stock_products",
     *                     type="integer",
     *                     description="Products with quantity = 0",
     *                     example=5
     *                 ),
     *                 @OA\Property(
     *                     property="total_transactions",
     *                     type="integer",
     *                     description="Total number of transactions",
     *                     example=150
     *                 ),
     *                 @OA\Property(
     *                     property="today_revenue",
     *                     type="number",
     *                     format="decimal",
     *                     description="Revenue from today's sales",
     *                     example=450.750
     *                 ),
     *                 @OA\Property(
     *                     property="total_revenue",
     *                     type="number",
     *                     format="decimal",
     *                     description="Total revenue from all sales",
     *                     example=12500.000
     *                 ),
     *                 @OA\Property(
     *                     property="average_transaction_value",
     *                     type="number",
     *                     format="decimal",
     *                     description="Average transaction value",
     *                     example=83.333
     *                 ),
     *                 @OA\Property(
     *                     property="top_selling_products",
     *                     type="array",
     *                     description="Top 5 selling products",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="product",
     *                             ref="#/components/schemas/Product"
     *                         ),
     *                         @OA\Property(
     *                             property="total_sold",
     *                             type="integer",
     *                             description="Total quantity sold",
     *                             example=45
     *                         ),
     *                         @OA\Property(
     *                             property="revenue",
     *                             type="number",
     *                             format="decimal",
     *                             description="Revenue from this product",
     *                             example=179.550
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function statistics(): JsonResponse
    {
        try {
            $today = now()->startOfDay();
            
            $stats = [
                'total_products' => Product::count(),
                'available_products' => Product::where('quantity_available', '>', 0)->count(),
                'out_of_stock_products' => Product::where('quantity_available', '=', 0)->count(),
                'total_transactions' => Transaction::count(),
                'today_revenue' => Transaction::whereDate('created_at', today())->sum('total_price'),
                'total_revenue' => Transaction::where('status', 'completed')->sum('total_price'),
                'average_transaction_value' => Transaction::where('status', 'completed')->avg('total_price'),
                'top_selling_products' => Transaction::select('product_id')
                    ->selectRaw('SUM(quantity) as total_sold, SUM(total_price) as revenue')
                    ->where('status', 'completed')
                    ->groupBy('product_id')
                    ->orderBy('total_sold', 'desc')
                    ->limit(5)
                    ->with('product:id,name,price')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'product' => $item->product,
                            'total_sold' => $item->total_sold,
                            'revenue' => $item->revenue,
                        ];
                    }),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve statistics.'
            ], 500);
        }
    }

    /**
     * Authorize admin access
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function authorizeAdmin(): void
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Access forbidden. Admin privileges required.');
        }
    }
}
