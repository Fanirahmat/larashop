<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function __construct(){
        //OTORISASI GATE
        $this->middleware(function($request,$next){
            if (Gate::allows('manage-orders'))
            {
                return $next($request);
            } 
            else 
            {
                abort(403,'anda tidak memiliki cukup hak akses');
            }
        });
    }
    
    public function index()
    {
        $orders = \App\Order::with('user')->with('books')->paginate(10);
        return view('orders.index', ['orders' => $orders]);
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show($id)
    {
        //
    }

   
    public function edit($id)
    {
        $order = \App\Order::findOrFail($id);
        return view('orders.edit', ['order' => $order]);
    }

    
    public function update(Request $request, $id)
    {
        $order = \App\Order::findOrFail($id);
        $order->status = $request->get('status');
        $order->save();
        return redirect()->route('orders.edit', [$order->id])->with('status','Order status succesfully updated');

    }

    
    public function destroy($id)
    {
        //
    }
}
