<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $response = [
            'status' => 200,
            'message' => null,
            'data' => null
        ];

        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'asc');

        $query = Transaction::join('products', 'products.id', '=', 'transactions.product_id');


        $searchTerm = $request->query('searchByName');

        $query->whereHas('product', function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        })->orWhereHas('product.category', function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        });

        $acceptSort = ['product_name', 'created_at', 'id', 'qty'];
        if (collect($acceptSort)->contains($sortBy)) {
            if ($sortBy == 'product_name') $sortBy = 'products.name';
            $query->orderBy($sortBy, $sortOrder);
        }

        $total_data = $query->count();

        $per_page = $request->query('per_page', 10);
        if (!is_numeric($per_page) || $per_page < 1) $per_page = 10;
        if ($per_page > 100) $per_page = 100;

        $total_page = ceil($total_data / $per_page);

        $current_page = $request->input('page', 1);
        if (!is_numeric($current_page) || $current_page < 1) $current_page = 1;
        if ($current_page > $total_page) $current_page = $total_page;



        // $response['data'] = $data;

        $items = $query->paginate($per_page, ['*'], 'page', $current_page)->withQueryString();


        $response['data'] = $items;

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
