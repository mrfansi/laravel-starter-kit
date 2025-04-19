<?php

namespace Platform\Tenant\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Dalam konteks tenant, kita hanya mengambil produk untuk tenant saat ini
        // karena prefix tabel sudah diatur oleh package tenancy
        $products = \DB::table('products')->get();
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'tenant_id' => tenant('id'),
        ]);
    }
    
    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sku' => 'required|string|unique:products,sku',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $product = \DB::table('products')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'sku' => $request->sku,
            'stock' => $request->stock,
            'is_active' => $request->is_active ?? true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'tenant_id' => tenant('id'),
        ], 201);
    }
    
    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = \DB::table('products')->find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $product,
            'tenant_id' => tenant('id'),
        ]);
    }
    
    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $product = \DB::table('products')->find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'sku' => 'string|unique:products,sku,' . $id,
            'stock' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $updateData = [];
        foreach (['name', 'description', 'price', 'sku', 'stock', 'is_active'] as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }
        
        $updateData['updated_at'] = now();
        
        \DB::table('products')->where('id', $id)->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'tenant_id' => tenant('id'),
        ]);
    }
    
    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = \DB::table('products')->find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }
        
        \DB::table('products')->where('id', $id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
            'tenant_id' => tenant('id'),
        ]);
    }
}
