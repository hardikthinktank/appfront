<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Jobs\SendPriceChangeNotification;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // List all products
    public function index()
    {
        $products = Product::all();
        return view('admin.products', compact('products'));
    }

    //Create product
    public function create()
    {
        return view('admin.add_product');
    }

    //Show edit product form
    public function edit(Product $product)
    {
        return view('admin.edit_product', compact('product'));
    }

    //Update product.
    public function update(Request $request,Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $oldPrice = $product->price;
        $product->update($request->except('image'));

        if ($request->hasFile('image')) {
            if ($product->image) {
                $imagePath = str_replace('storage/', '', $product->image);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
    
            $file = $request->file('image');
            $timestamp = now()->timestamp;
            $filename = $timestamp . '_' . $file->getClientOriginalName(); 
            $filePath = $file->storeAs('uploads', $filename, 'public'); 
            $product->update(['image' => 'storage/' .$filePath]);
        }

        $product->save();

        if ($oldPrice != $product->price) {
            
            $notificationEmail = env('PRICE_NOTIFICATION_EMAIL', 'hardik@yopmail.com');

            try {
                
                SendPriceChangeNotification::dispatch($product,$oldPrice,$product->price,$notificationEmail);
            } catch (\Exception $e) {
                 Log::error('Failed to dispatch price change notification: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    //Delete product
    public function destroy(Product $product)
    {   
        if ($product->image) {
            $imagePath = str_replace('storage/', '', $product->image);
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
    }
    // Show add product form
    
    //Add new product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $product = Product::create($request->except('image'));

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $timestamp = now()->timestamp;
            $filename = $timestamp . '_' . $file->getClientOriginalName(); 
            $filePath = $file->storeAs('uploads', $filename, 'public'); 
            $product->update(['image' => 'storage/' .$filePath]);
        } else {
            $product->update(['image' => 'product-placeholder.jpg']);
        }
        return redirect()->route('admin.products.index')->with('success', 'Product added successfully');
    }
}
