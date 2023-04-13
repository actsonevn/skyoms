<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // variable using in scoped class
    private static $_CACHE_OVERVIEW = 'CACHE_OVERVIEW_KEY';

    // [GET] Get data overview dashboard
    public function getOverviewDashboard() {
        // handle buffer when exists.
        if(Cache::has(self::$_CACHE_OVERVIEW)) {
            $data_cache = Cache::get(self::$_CACHE_OVERVIEW);

            return response()->json([
                'status' => 200,
                'data' => $data_cache,
                'is_cache' => true
            ], 200);
        }

        // get total records in table post
        $total_order = DB::table('post')->count('id');

        // get total records in table post width condition post status must be wc-completed
        $order_completed = DB::table('post')
        ->where([
            ['post_status', '!=', 'wc-cancelled'],
            ['post_status', '!=', 'wc-trash']
        ])->count();

        // get total records items sales and total price width conditions:
        // + status must be wc-completed 
        // + order item type must be line item
        $order_item_data = DB::table('post as p')
        ->select(DB::raw('count(oi.order_item_id) as total_item_sale'), DB::raw('sum(pm_ot.meta_value - pm_os.meta_value) as total_price'))
        ->join('order_items as oi', 'oi.order_id', '=', 'p.id')
        ->join('postmeta as pm_ot', 'pm_ot.post_id', '=', 'p.id')
        ->join('postmeta as pm_os', 'pm_os.post_id', '=', 'p.id')
        ->where([
            ['p.post_status', '!=', 'wc-cancelled'],
            ['p.post_status', '!=', 'wc-trash'],
            ['oi.order_item_type', '=', 'line_item'],
            ['pm_ot.meta_key', '=', '_order_total'],
            ['pm_os.meta_key', '=', '_order_shipping'],
        ])->get();

        $data_item = $order_item_data->toArray()[0]; // get firt raw in result array data;

        $data_reponse = [
            'total_order' => $total_order,
            'order_completed' => $order_completed,
            'order_item_sale' => $data_item->total_item_sale,
            'avg_total_price' => round($data_item->total_price / $data_item->total_item_sale, 2),
        ];

        Cache::put(self::$_CACHE_OVERVIEW, $data_reponse, 30); // 30 seconds is time expried cache data
        
        return response()->json([
            'status' => 200,
            'data' => $data_reponse
        ], 200);
    }
}
