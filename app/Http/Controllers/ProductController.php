<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = Product::select('products.*', 'categories.name as category_name')
            ->join('categories', 'products.category_id', '=', 'categories.id');


        $searchTerm = $request->query('searchByName');

        $query->whereHas('category', function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        })->where('products.name', 'like', '%' . $searchTerm . '%');

        $total_data = $query->count();

        $per_page = $request->query('per_page', 10);
        if (!is_numeric($per_page) || $per_page < 1) $per_page = 10;
        if ($per_page > 100) $per_page = 100;

        $total_page = ceil($total_data / $per_page);

        $current_page = $request->input('page', 1);
        if (!is_numeric($current_page) || $current_page < 1) $current_page = 1;
        if ($current_page > $total_page) $current_page = $total_page;


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
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
