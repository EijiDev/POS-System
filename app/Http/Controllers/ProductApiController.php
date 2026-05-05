<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller
{
    public function index()
    {
        return response()->json(
            Product::orderBy('category')->orderBy('name')->get()->map(fn($p) => [
                'product_id'  => $p->id,
                'product_name'=> $p->name,
                'category'    => $p->category,
                'price'       => $p->price,
                'cost'        => $p->cost,
                'stock'       => $p->stock,
                'sold'        => $p->sold,
                'status'      => $p->status,
                'image_url'   => $p->img,
            ])
        );
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'product_id'   => 'nullable|exists:products,id',
            'product_name' => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'price'        => 'required|numeric|min:0',
            'cost'         => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'status'       => 'required|in:Active,Inactive',
            'image_url'    => 'nullable|string',
        ]);

        $imgPath = $data['image_url'] ?? null;

        // Handle base64 image upload
        if (!empty($data['image_url']) && str_starts_with($data['image_url'], 'data:image')) {
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $data['image_url']);
            $imageData = base64_decode($imageData);
            $filename  = 'products/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, $imageData);
            $imgPath = Storage::url($filename);
        }

        $fields = [
            'name'     => $data['product_name'],
            'category' => $data['category'],
            'price'    => $data['price'],
            'cost'     => $data['cost'],
            'stock'    => $data['stock'],
            'status'   => $data['status'],
            'img'      => $imgPath,
        ];

        if (!empty($data['product_id'])) {
            $product = Product::findOrFail($data['product_id']);
            $product->update($fields);
        } else {
            $product = Product::create($fields);
        }

        return response()->json(['success' => true, 'product_id' => $product->id]);
    }

    public function delete(Request $request)
    {
        $data = $request->validate(['product_id' => 'required|exists:products,id']);
        Product::findOrFail($data['product_id'])->delete();
        return response()->json(['success' => true]);
    }

    // For POS — only active products
    public function posProducts()
    {
        return response()->json(
            Product::where('status', 'Active')->orderBy('category')->orderBy('name')->get()->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'category' => $p->category,
                'price'    => $p->price,
                'img'      => $p->img,
            ])
        );
    }
}
