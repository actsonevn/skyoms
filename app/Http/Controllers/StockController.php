<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    // [POST] create new stock divide
    public function createNewStockDevide(Request $request) {
        $result = DB::table('stock_divide')->insert([
            'parent_product_id' => $request->parentId,
            'product_name' => $request->productName,
            'sub_name' => $request->subName,
            'main_sku' => $request->mainSku,
            'sku' => $request->sku,
            'category' => $request->category,
            'stock_total' => $request->stockTotal,
            'price_shopee' => $request->priceShopee,
            'stock_shopee' => $request->stockShopee,
            'price_lazada' => $request->priceLazada,
            'stock_lazada' => $request->stockLazada,
            'type' => $request->type,
            'product_name_vat' => $request->productNameVAT,
            'view' => $request->view,
            'link_img' => $request->linkImg,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Create success',
            'result' =>  $result,
        ], 200);
    }

    // [GET] get stock list
    public function getStockList(Request $request) {
        $page = !$request->input('page') || $request->input('page') == 1 ? 0 : $request->input('page') - 1; // page default is 0
        $page_size = $request->input('page_size') && $request->input('page_size') < 20 ? $request->input('page_size') : 20; // page size default is 10

        $offset = $page_size * ($page +  1) - $page_size;
        $limit = $page_size;

        $sort = $request->input('sort');
        $sortType = $request->input('sort_type');

        $sortString = ($sort ? $sort : 'product_id').' '.($sortType ? $sortType : 'ASC');

        $brandName = $request->input('brand_name');
        $productName = $request->input('product_name');
        $minStock = $request->input('min_stock');
        $maxStock = $request->input('max_stock');

        $arrayCondition = [];
        if($brandName) {
            array_push($arrayCondition, ['brand_name', $brandName]);
        }

        if($productName) {
            array_push($arrayCondition, ['product_name', '=', '%'.$productName.'%']);
        }

        if(($minStock || $minStock == 0) && $maxStock) {
            array_push($arrayCondition, ['stock_total', '>=' , $minStock]);
            array_push($arrayCondition, ['stock_total', '<=' , $maxStock]);
        }

        // skip: is the starting position to get data in the table
        // take: is the number of rows of data retrieved
        $stocks = DB::table('stock_divide')
        ->where('parent_product_id', null)
        ->where($arrayCondition)
        ->groupBy('product_id')
        ->orderByRaw($sortString)
        ->skip($offset)->take($limit)
        ->get();

        $countStock = DB::table('stock_divide')
        ->where('parent_product_id', null)
        ->where($arrayCondition)
        ->groupBy('product_id')
        ->get();

        $arrayData = [];

        foreach($stocks->toArray() as $stock) {
            
            # stock single
            if($stock->type == 0) {
                array_push($arrayData, [
                    "id" => $stock->id,
                    "productId" => $stock->product_id,
                    "productName" => $stock->product_name,
                    "sku" => $stock->main_sku,
                    "category" => $stock->category,
                    "linkImg" => $stock->link_img,
                    "view" => $stock->view,
                    "priceShopee" => $stock->price_shopee,
                    "stockShopee" => $stock->stock_shopee,
                    "type" => $stock->type,
                    "stockDetail" => [],
                ]);
            } 

            # stock avairation
            if($stock->type == 1 && $stock->parent_product_id == null) {
                $findStockByProductId = DB::table('stock_divide')
                ->where('parent_product_id', $stock->product_id)
                ->where('parent_product_id', '!=', null)
                ->get();


                $subStockList = [];

                if($findStockByProductId) {
                    foreach($findStockByProductId->toArray() as $findStock) { 
                        if($findStock->parent_product_id == $stock->product_id) {
                            array_push($subStockList, [
                                "id" => $findStock->id,
                                "productId" => $findStock->product_id,
                                "subName" => $findStock->sub_name,
                                "subSku" => $findStock->sku,
                                "view" => $findStock->view,
                                "priceShopee" => $findStock->price_shopee,
                                "stockShopee" => $findStock->stock_shopee,
                            ]);
                        }
                    }
                }
               

                array_push($arrayData, [
                    "id" => $stock->id,
                    "productId" => $stock->product_id,
                    "productName" => $stock->product_name,
                    "sku" => $stock->main_sku,
                    "category" => $stock->category,
                    "linkImg" => $stock->link_img,
                    "type" => $stock->type,
                    "stockDetail" => $subStockList,
                ]);
            }
        }

        return response()->json([
            'status' => 200,
            'data' => $arrayData,
            'totalPage' => ceil(count($countStock) / $limit)
        ], 200);
    }
}
