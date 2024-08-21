<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Session;

class ProductController extends Controller
{
    //
    public function index()
    {
        if(! \Auth::user()->can('Add New Product'))
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
        Session::forget('error');
        return view('product.addProduct');
    }
    public function store(ProductRequest $request){
        $product=[
            'name'=>$request->name,
            'doman'=>$request->doman,
            'analytical_id'=>$request->analytical_id,
            'status'=>1,
        ];
        Product::create($product);
        return redirect()->route('report.product.list')->with('success', __('Product successfully create!'));
    }
    public function edit($id)
    {
        Session::forget('error');
        $product=Product::FindOrFail($id);
        return view('product.editProduct',compact('product','id'));
    }
    public function update(ProductRequest $request){
        $product=[
            'name'=>$request->name,
            'doman'=>$request->doman,
            'analytical_id'=>$request->analytical_id,
            'status'=>1,
        ];
        Product::FindById($request->id)->update($product);
        return redirect()->route('report.product.list')->with('success', __('Product  Updated Successfully!'));
    }
    public function list(){
        if(! \Auth::user()->can('Product List'))
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
        $products = Product::get();
        return view('product.list',compact('products'));
    }
}
