<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // variable using in scoped class
    private static $_CACHE_OVERVIEW = 'CACHE_OVERVIEW_KEY';
    private static $_CACHE_ORDER_LIST = 'CACHE_CUSTOMER_ORDER_LIST_KEY';

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

    // [GET] Get data customer order
    public function getCustomerOrderList() { 
        // handle buffer when exists.
        if(Cache::has(self::$_CACHE_ORDER_LIST)) {
            $data_cache = Cache::get(self::$_CACHE_ORDER_LIST);

            return response()->json([
                'status' => 200,
                'data' => $data_cache,
                'is_cache' => true
            ], 200);
        }

        // get data customer order by query
        $res_customer_order =  DB::table('post as p')
        ->select('p.id as order_id','pm_sfn.meta_value as customer','p.post_status as status','oim_lt.meta_value as total_price','p.post_date as create_time',
        DB::raw("GROUP_CONCAT(CONCAT(oi.order_item_name, '- (Qty: ', oim_qty.meta_value, ')') SEPARATOR ', ') AS list_order"))
        ->join('postmeta as pm_sfn', 'p.id', '=', 'pm_sfn.post_id')
        ->join('order_items as oi', 'oi.order_id', '=', 'p.id')
        ->join('order_itemmeta as oim_qty', 'oi.order_item_id', '=', 'oim_qty.order_item_id')
        ->join('order_itemmeta as oim_lt', 'oi.order_item_id', '=', 'oim_lt.order_item_id')
        ->where([
            ['pm_sfn.meta_key', '_shipping_first_name'],
            ['oim_qty.meta_key', '_qty'],
            ['oim_lt.meta_key', '_line_total'],
            ['p.post_status', '!=', 'wc-cancelled'],
            ['p.post_status', '!=', 'wc-trash'],
            ['p.id', '!=', 'Null'],
            ['oi.order_item_type', 'line_item'],
        ])->groupBy('p.id')
        ->get();

        Cache::put(self::$_CACHE_ORDER_LIST, $res_customer_order, 30); // 30 seconds is time expried cache data

        return response()->json([
            'status' => 200,
            'data' => $res_customer_order
        ], 200);
    }

    // [GET] Get order by hours
    public function getOrderByHours() { 
        // get data customer order by query
        $res_order_by_hours = DB::table('post as p')
        ->select(DB::raw('SUM(pm_ot.meta_value - pm_os.meta_value) AS total_price'), DB::raw('HOUR(p.post_date) AS hours'))
        ->join('postmeta as pm_ot', 'p.id', '=', 'pm_ot.post_id')
        ->join('postmeta as pm_os', 'p.id', '=', 'pm_os.post_id')
        ->where([
            ['p.post_type', 'shop_order'],
            ['p.post_status', '!=' ,'wc-cancelled'],
            ['p.post_status', '!=' ,'wc-trash'],
            ['pm_ot.meta_key', '_order_total'],
            ['pm_os.meta_key', '_order_shipping'],
        ])->groupBy(DB::raw('HOUR(p.post_date)'))
        ->get();

        $hours = [];
        $data = [];

        $rel_query = $res_order_by_hours->toArray();
        for($i = 0; $i <= 23; $i++) {
            $isNull = false;
            foreach( $rel_query as $val) {
                if($i == $val->hours)
                {
                    array_push( $hours, $val->hours.'h' ); 
                    array_push( $data, $val->total_price ); 
                    $isNull = true;
                    break;
                }
            }

            if(!$isNull)
            {
                array_push( $hours, $i.'h' ); 
                array_push( $data, 0 ); 
            }
        }


        return response()->json([
            'status' => 200,
            'hours' =>  $hours,
            'data' => $data,
        ], 200);
    }
}
