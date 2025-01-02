<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {

        $products = Product::all();

        return view('products.index', compact('products'));
    }
}
