<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Session as Session;

class orderController extends Controller
{

    public function orderList()
    {
        $orders = DB::table('order_master')
            ->join('product_master', 'order_master.prodID', '=', 'product_master.prodID')
            ->select('order_master.orderID', 'order_master.orderDate', 'order_master.prodID', 'product_master.prodName', 'order_master.prodRate', 'order_master.orderQty', 'order_master.orderValue')
            ->get();

        return view('index', ['orders' => $orders]);
    }


    public function orderfun(Request $request)
    {
        $input = $request->all();

        if ($input['upordid']) {
            $OldprodQty = $input['oldorderqty'];
            // $OldprodQty = DB::table('product_master')->where('prodID', $input['prodid'])->value('prodQty');
            $newProdQty = '';
            if ((int)$input['orderqty2'] > (int)$input['orderqty']) {
                $newProdQty = (int)$input['orderqty2'] - (int)$input['orderqty'];
                $newProdQty = abs($newProdQty);
                $newProdQty = (int)$OldprodQty + $newProdQty;
            } else {
                $newProdQty = (int)$input['orderqty2'] - (int)$input['orderqty'];
                $newProdQty = abs($newProdQty);
                $newProdQty = (int)$OldprodQty - $newProdQty;
            }

            DB::table('order_master')->where('orderID', $input['upordid'])
                ->update([
                    'orderQty' => $input['orderqty'],
                    'orderValue' => $input['ordervalue']
                ]);

            DB::table('product_master')->where('prodID', $input['prodid'])
                ->update([
                    'prodQty' => $newProdQty

                ]);
            $message = "Updated...!!";
        } else {
            $date = date("Y-m-d");
            DB::table('order_master')->insert([
                'prodID' => $input['prodid'],
                'prodRate' => $input['prodrate'],
                'orderQty' => $input['orderqty'],
                'orderValue' => $input['ordervalue'],
                'orderDate' => $date
            ]);
            $oldQty = DB::table('product_master')->select('prodQty')->where('prodID', $input['prodid'])->first()->prodQty;
            $newQty = $oldQty - $input['orderqty'];
            DB::table('product_master')->where('prodID', $input['prodid'])->update(['prodQty' => $newQty]);
            $message = "Order Placed";
        }



        $orders = DB::table('order_master')
            ->join('product_master', 'order_master.prodID', '=', 'product_master.prodID')
            ->select('order_master.orderID', 'order_master.orderDate', 'order_master.prodID', 'product_master.prodName', 'order_master.prodRate', 'order_master.orderQty', 'order_master.orderValue')
            ->get();

        return view('index', ['message' => $message, 'orders' => $orders]);
    }

    public function getProductFun(Request $request)
    {
        $prodname = $request->prodname;
        $prod = DB::table('product_master')->where('prodName', $prodname)->first();
        return json_encode($prod);
    }
    public function getOrderDetail(Request $request)
    {
        $ordid = $request->ordid;
        $prod = DB::table('order_master')
            ->join('product_master', 'order_master.prodID', '=', 'product_master.prodID')
            ->select('order_master.*', 'product_master.*')
            ->where('order_master.orderID', $ordid)
            ->first();

        return json_encode($prod);
    }
    public function deleteOrder(Request $request)
    {
        $ordid = $request->ordid;
        $order = DB::table('order_master')->where('orderID', $ordid)->first();
        $prodQty = DB::table('product_master')->where('prodID', $order->prodID)->value('prodQty');
        $newProdQty = (int)$order->orderQty + (int)$prodQty;

        DB::table('product_master')->where('prodID', $order->prodID)->update(['prodQty' => $newProdQty]);
        $res = DB::table('order_master')->where('orderID', $ordid)->delete();
        return json_encode($res);
    }
}
