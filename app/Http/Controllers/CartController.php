<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index(){
        $cartItems = Cart::instance('cart')->content();
        return view('cart',['cartItems' => $cartItems]);
    }
    public function addToCart(Request $request)
{
    $product = Product::find($request->id);
    $price = $product->sale_price ? $product->sale_price : $product->regular_price;
    Cart::instance('cart')->add($product->id, $product->name, $request->quantity, $price)
        ->associate('App\Models\Product');
    return redirect()->back()->with('message', 'Success! Item has been added successfully!');
}
public function updateCart(Request $request)
{
    // Pastikan validasi menerima rowId dan quantity
    $request->validate([
        'rowid' => 'required|string',
        'quantity' => 'required|integer|min:1',
    ]);

    // Ambil rowId dan quantity
    $rowId = $request->rowid;
    $quantity = $request->quantity;

    // Cek dan update keranjang
    Cart::instance('cart')->update($rowId, $quantity);

    // Redirect kembali ke halaman keranjang
    return redirect()->back()->with('message', 'Cart updated successfully!');
}

public function removeItem(Request $request){
    $rowId = $request->rowid;
    Cart::instance('cart')->remove($rowId);
     return redirect()->route('cart.index');
}
public function clearCart(){
    Cart::instance('cart')->destroy();
    return redirect()->route('cart.index');
}
}
