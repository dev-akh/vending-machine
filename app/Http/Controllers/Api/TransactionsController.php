<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Transactions",
 *     description="Transaction management endpoints"
 * )
 */
class TransactionsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/transactions",
     *     summary="Get all transactions",
     *     description="Get paginated list of all transactions (admin only)",
     *     tags={"Transactions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page (default: 20, max 100)",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by transaction status",
     *         @OA\Schema(type="string", enum={"completed", "pending", "failed"}, example="completed")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter by user ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="Filter by product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter transactions from date (Y-m-d)",
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter transactions to date (Y-m-d)",
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field",
     *         @OA\Schema(type="string", enum={"created_at", "total_price", "status"}, example="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="direction",
     *         in="query",
     *         description="Sort direction",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transactions retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TransactionResource")
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
     *         response=403,
     *         description="Forbidden - Admin access required",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Admin access required"
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Transaction::with(['user', 'product']);

            // Filter by status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            // Filter by user ID
            if ($request->has('user_id') && !empty($request->user_id)) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by product ID
            if ($request->has('product_id') && !empty($request->product_id)) {
                $query->where('product_id', $request->product_id);
            }

            // Filter by date range
            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $direction = $request->get('direction', 'desc');
            
            // Validate sort field
            $allowedSorts = ['created_at', 'total_price', 'status', 'quantity'];
            if (!in_array($sortBy, $allowedSorts)) {
                $sortBy = 'created_at';
            }
            
            // Validate direction
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'desc';
            }
            
            $query->orderBy($sortBy, $direction);

            // Pagination with validation
            $perPage = min(max($request->get('per_page', 20), 1), 100);
            $transactions = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => TransactionResource::collection($transactions)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve transactions.'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/transactions/{id}",
     *     summary="Get transaction",
     *     description="Get detailed information about a specific transaction (admin only)",
     *     tags={"Transactions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Transaction ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/TransactionResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transaction not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Transaction not found"
     *             )
     *         )
     *     )
     *     )
     * )
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load(['user', 'product']);
        
        return response()->json([
            'status' => 'success',
            'data' => new TransactionResource($transaction)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/transactions/statistics",
     *     summary="Get transaction statistics",
     *     description="Get comprehensive transaction statistics (admin only)",
     *     tags={"Transactions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter statistics from date (Y-m-d)",
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter statistics to date (Y-m-d)",
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
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
     *                     property="total_transactions",
     *                     type="integer",
     *                     example=150
     *                 ),
     *                 @OA\Property(
     *                     property="total_revenue",
     *                     type="number",
     *                     format="decimal",
     *                     example=1250.750
     *                 ),
     *                 @OA\Property(
     *                     property="average_transaction_value",
     *                     type="number",
     *                     format="decimal",
     *                     example=8.338
     *                 ),
     *                 @OA\Property(
     *                     property="completed_transactions",
     *                     type="integer",
     *                     example=145
     *                 ),
     *                 @OA\Property(
     *                     property="pending_transactions",
     *                     type="integer",
     *                     example=3
     *                 ),
     *                 @OA\Property(
     *                     property="failed_transactions",
     *                     type="integer",
     *                     example=2
     *                 ),
     *                 @OA\Property(
     *                     property="top_products",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="product_id", type="integer", example=1),
     *                         @OA\Property(property="product_name", type="string", example="Coca Cola"),
     *                         @OA\Property(property="total_sold", type="integer", example=50),
     *                         @OA\Property(property="revenue", type="number", format="decimal", example=199.500)
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $query = Transaction::query();

            // Apply date filters if provided
            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Basic statistics
            $totalTransactions = $query->count();
            $totalRevenue = $query->sum('total_price');
            $averageTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

            // Status breakdown
            $completedTransactions = $query->where('status', 'completed')->count();
            $pendingTransactions = $query->where('status', 'pending')->count();
            $failedTransactions = $query->where('status', 'failed')->count();

            // Top products
            $topProducts = Transaction::select('product_id')
                ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(total_price) as revenue')
                ->with('product:id,name')
                ->groupBy('product_id')
                ->orderBy('total_sold', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Unknown',
                        'total_sold' => $item->total_sold,
                        'revenue' => (float) number_format($item->revenue, 3, '.', ''),
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_transactions' => $totalTransactions,
                    'total_revenue' => (float) number_format($totalRevenue, 3, '.', ''),
                    'average_transaction_value' => (float) number_format($averageTransactionValue, 3, '.', ''),
                    'completed_transactions' => $completedTransactions,
                    'pending_transactions' => $pendingTransactions,
                    'failed_transactions' => $failedTransactions,
                    'top_products' => $topProducts,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve transaction statistics.'
            ], 500);
        }
    }
}
