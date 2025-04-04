<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ExchangeRateService;

class ProductController extends Controller
{

    protected $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }
    //show all product
    public function index()
    {
        $products = Product::all();
        $exchangeRate = $this->exchangeRateService->getExchangeRate();
        return view('products.list', compact('products', 'exchangeRate'));
    }
    //show single prodct
    public function show(Product $product)
    {
        $exchangeRate = $this->exchangeRateService->getExchangeRate();
        $product->price_usd = number_format($product->price, 2);
        $product->price_eur = number_format($product->price * $exchangeRate, 2);
        return view('products.show', compact('product', 'exchangeRate'));
    } 
}
