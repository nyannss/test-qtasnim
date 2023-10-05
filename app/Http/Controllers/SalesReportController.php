<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $start_date = $request->input('start_date', now()->subDays(7));
        $end_date = $request->input('end_date', now());

        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();

        try {
            $query = Transaction::join('products', 'transactions.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->whereBetween('transactions.created_at', [$start_date, $end_date])
                ->select('categories.id as category_id', 'categories.name as category_name', 'categories.label as category_label',  DB::raw('SUM(transactions.qty) as total_qty'))
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('total_qty', 'desc');
            $data = $query->get();
        } catch (\Throwable $th) {
            response()->json([
                'status' => 500,
                'message' => 'Internal Server Error',
                'data' => null,
            ], 500);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Data successfully fetched',
            'data' => $data,
        ], 200);
    }
}
