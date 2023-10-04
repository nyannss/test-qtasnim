<?php

namespace App\Http\Controllers;

use App\Models\Product;
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

        $query = Transaction::select('products.*', 'transactions.*', 'products.id as product_id', 'categories.name as category_name')
            ->join('products', 'products.id', '=', 'transactions.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.id');


        $searchTerm = $request->query('searchByName');

        $query->whereHas('product', function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        })->orWhereHas('product.category', function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        });

        $acceptSort = ['product_name', 'created_at', 'id', 'qty'];
        if (collect($acceptSort)->contains($sortBy)) {
            switch ($sortBy) {
                case 'product_name':
                    $sortBy = 'products.name';
                    break;
                case 'created_at':
                    $sortBy = 'transactions.created_at';
                    break;
                case 'id':
                    $sortBy = 'transactions.id';
                    break;
            }


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

    public function show($id): JsonResponse
    {
        try {
            $tr = Transaction::with('product.category')->find($id);
            if (!$tr)
                throw new \Exception('Transaction data not found', 404);


            return response()->json([
                'code' => 200,
                'message' => 'Transaction data fetched',
                'data' => $tr
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => $e->getCode() ?? '500',
                'message' => $e->getMessage() ?? 'Internal server error',
                'data' => null
            ], $e->getCode());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id', // Validasi bahwa product_id ada dalam tabel products
                'qty' => 'required|integer|min:1', // Validasi bahwa qty adalah bilangan bulat positif
            ]);

            $product = Product::findOrFail($request->input('product_id'));
            $qty = $request->input('qty');

            if ($qty > $product->stock) {
                throw new \Exception('Quantity exceeds available stock');
            }

            if ($qty > $product->stock) {
                throw new \Exception('Quantity exceeds available stock');
            }

            $tr = new Transaction;
            $tr->product_id = $product->id;
            $tr->qty = $qty;

            $product->qty_sold += $qty;
            $product->stock -= $qty;

            if (!$tr->save())
                throw new \Exception('An error ocurred when insert to database');

            $product->save();

            return response()->json([
                'status' => 201,
                'message' => 'Data inserted',
                'data' => [$tr]
            ], 201);
        } catch (\Throwable $e) {
            // throw $th;
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        //
        try {
            $tr = Transaction::with('product')->find($id);
            if (!$tr)
                throw new \Exception('Transaction data not found', 404);

            $request->validate([
                'product_id' => 'nullable|exists:products,id', // validate with reference in products table
                'qty' => 'nullable|integer|min:1', // validate integer must >= 1
            ]);
            $product_id = $request->input('product_id');

            if ($product_id) {
                $product = Product::findOrFail($product_id);

                if ($product->id == $tr->product->id) {
                    $product->stock += $tr->qty;
                    $product->qty_sold -= $tr->qty;
                } else {
                    $oldProduct = $tr->product;
                    $oldProduct->stock +=  $tr->qty;
                    $oldProduct->qty_sold -= $tr->qty;
                }

                // update
                $tr->product_id = $product->id;
            }

            $qty = $request->input('qty');

            if ($qty) {
                if ($qty > $product->stock) {
                    throw new \Exception('Quantity exceeds available stock', 400);
                }

                if ($qty > $product->stock) {
                    throw new \Exception('Quantity exceeds available stock', 400);
                }

                // update
                $tr->qty = $qty;
            }

            $tr->save();

            if (isset($oldProduct) && $oldProduct)
                $oldProduct->save();

            if (isset($product) && $product) {
                $product->stock     -= $qty;
                $product->qty_sold  += $qty;
                $product->save();
            }

            return response()->json([
                'code' => 200,
                'message' => "transaction data updated",
                'data' => [$tr]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => ($th->getCode() == 0 || $th->getCode() > 500 ? 500 : $th->getCode()) ?? 500,
                'message' => $th->getMessage() ?? "Internal server error",
                'data' => null
            ], ($th->getCode() == 0 || $th->getCode() > 500 ? 500 : $th->getCode()) ?? 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $tr = Transaction::find($id);
            if (!$tr)
                throw new \Exception('Transaction data not found', 404);

            $tr->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Transaction data deleted',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => $e->getCode() ?? '500',
                'message' => $e->getMessage() ?? 'Internal server error',
                'data' => null
            ], $e->getCode());
        }
    }
}
