<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 1); // default page is 1
        $size = $request->query('size', 12); // default size is 12
        $order = $request->query('order', -1); // default order is -1

        $o_order = "DESC";
        $o_column = "created_at";

        // Handling the sorting order
        switch ($order) {
            case 1:
                $o_order = "DESC";
                $o_column = "created_at";
                break;
            case 2:
                $o_order = "ASC";
                $o_column = "created_at";
                break;
            case 3:
                $o_order = "ASC";
                $o_column = "regular_price";
                break;
            case 4:
                $o_order = "DESC";  // High to Low
                $o_column = "regular_price";  // Sorting by price
                break;
        }

        // Fetch brands and categories
        $brands = Brand::orderBy("name", "ASC")->get();
        $q_brands = $request->query("brands");
        $categories = Category::orderBy("name", "ASC")->get();
        $q_categories = $request->query("categories");

        // Handle price range
        $prange = $request->query("prange", "0,500");
        list($from, $to) = explode(",", $prange);

        // Query products with filters and pagination
        $products = Product::where(function ($query) use ($q_brands) {
                if ($q_brands) {
                    $query->whereIn('brand_id', explode(',', $q_brands));
                }
            })
            ->where(function ($query) use ($q_categories) {
                if ($q_categories) {
                    $query->whereIn('category_id', explode(',', $q_categories));
                }
            })
            ->whereBetween('regular_price', [$from, $to])
            ->orderBy($o_column, $o_order)
            ->paginate($size);  // Use paginate to get the paginated result

        // Return view with products and filters
        return view('shop', [
            'products' => $products,
            'page' => $page,
            'size' => $size,
            'order' => $order,
            'brands' => $brands,
            'q_brands' => $q_brands,
            'categories' => $categories,
            'q_categories' => $q_categories,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function productDetails($slug)
    {
        $product = Product::where('slug', $slug)->first();
        $rproducts = Product::where('slug', '!=', $slug)->inRandomOrder()->take(8)->get();
        return view('details', ['product' => $product, 'rproducts' => $rproducts]);
    }
}
