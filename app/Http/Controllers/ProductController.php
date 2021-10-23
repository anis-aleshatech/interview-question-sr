<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //dd($request);
        $product = Product::create($request->only('title', 'sku', 'description'));
        if ($product) {
            if ($request->hasFile('product_image')) {
                $files = $request->file('product_image');
                foreach ($files as $file) {
                    $imagesPath = $file->store('public');
                    $imagesName = (explode('/', $imagesPath))[1];
                    $host = $_SERVER['HTTP_HOST'];
                    $protocol = $_SERVER['PROTOCOL'] = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';

                    $data = [];
                    $data['product_id '] = $product->id;
                    $data['thumbnail '] = 1;
                    $data['file_path'] = $protocol . $host . "/public/storage/" . $imagesName;
                    ProductImage::create($data);
                }

            }
        }
        if ($request->product_variant) {
            $variants = $request->product_variant;

            if($variants!=""){
                foreach ($variants as $key => $variant) {
                    $tags = $variants[$key]['tags'];
                    if($variants[$key]!=""){
                        foreach ($tags as $k => $tag) {
                            if(isset($variants[$key]['option']) && $variants[$key]['option'] > 0){
                                $vdata['variant'] = $tags[$k];
                                $vdata['variant_id'] = $variants[$key]['option'];
                                $vdata['product_id'] = $product->id;                   
                                ProductVariant::insert($vdata);
                                $lastentryid =  DB::getPdo()->lastInsertId();
                                

                                if ($request->product_variant_prices) {
                                    $pvPrices = $request->product_variant_prices;
                                    if(count($pvPrices) > 0){
                                        foreach ($pvPrices as $key => $pvPrice) {
                                            if($pvPrice!=""){
                                                $variationTitle = rtrim($pvPrices[$key]['title'],'/');
                                                $explodearray = count(explode('/',$variationTitle));                            

                                                // if($explodearray==3){
                                                //     list($v1, $v2, $v3) = explode('/',$variationTitle);
                                                //     if($tags[$k] == $v1){
                                                //         $variationoneid = $lastentryid;
                                                //     }
                                                // }
                                                // elseif($explodearray==2){
                                                //     list($v1, $v2) = explode('/',$variationTitle);
                                                //     if($tags[$k] == $v1){
                                                //         $variationoneid = $lastentryid;
                                                //     }
                                                // }
                                                // elseif($explodearray==1){
                                                //     list($v1) = explode('/',$variationTitle);
                                                //     if($tags[$k] == $v1){
                                                //         $variationoneid = $lastentryid;
                                                //     }
                                                // }

                                                list($v1, $v2, $v3) = explode('/',$variationTitle);
                                                if($tags[$k] == $v1){
                                                    $variationoneid = $lastentryid;
                                                }


                                                if($pvPrices[$key]!=""){
                                                    $vpdata['product_variant_one'] = $variationoneid;
                                                    $vpdata['product_variant_two'] = $variationoneid;
                                                    $vpdata['product_variant_three'] = $variationoneid;
                                                    $vpdata['price'] = $pvPrices[$key]['price']; 
                                                    $vpdata['stock'] = $pvPrices[$key]['stock']; 
                                                    $vpdata['product_id'] = 1;                   
                                                    ProductVariantPrice::insert($vpdata);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
