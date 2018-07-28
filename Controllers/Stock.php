<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;


use App\com_zeapps_crm\Models\ProductStocks;
use App\com_zeapps_crm\Models\StockMovements;
use App\com_zeapps_crm\Models\Warehouses;


class Stock extends Controller
{

    public function get(Request $request){
        $id_stock = $request->input('id_stock', 0);
        $id_warehouse = $request->input('id_warehouse', 0);

        $where = array('com_zeapps_crm_product_stocks.id' => $id_stock);
        if($id_warehouse) {
            $where['com_zeapps_crm_stocks.id_warehouse'] = $id_warehouse;
        }

        $total = 0 ;
        $product_stock = ProductStocks::getStock($where) ;

        if($product_stock && $product_stock->id) {
            $whereStock = array('id_stock' => $product_stock->id);
            if($id_warehouse) {
                $whereStock['id_warehouse'] = $id_warehouse;
            }

            $product_stock->avg = StockMovements::avg($whereStock);


            $product_stock->movements = StockMovements::limit(15)->offset(0)->orderBy('date_mvt', 'DESC') ;
            foreach ($whereStock as $key => $value) {
                $product_stock->movementsCount = $product_stock->movements->where($key, $value) ;
            }
            $product_stock->movements = $product_stock->movements->get();


            if($product_stock->movements) {
                $product_stock->last = [];
                $product_stock->last['month'] = StockMovements::last_year($whereStock);
                $product_stock->last['dates'] = StockMovements::last_months($whereStock);
                $product_stock->last['date'] = StockMovements::last_month($whereStock);
                $product_stock->last['days'] = StockMovements::last_week($whereStock);
            } else {
                $product_stock->movements = array();
                $product_stock->recent_mvmts = array();
                $product_stock->last = array(
                    'month' => [],
                    'dates' => [],
                    'date' => [],
                    'days' => []
                );
            }

            $product_stock->movementsCount = StockMovements::select('id') ;
            foreach ($whereStock as $key => $value) {
                $product_stock->movementsCount = $product_stock->movementsCount->where($key, $value) ;
            }
            $total = $product_stock->movementsCount->count();
        } else {
            $product_stock = array();
        }

        $warehouses = Warehouses::get();

        echo json_encode(array(
            'product_stock' => $product_stock,
            'warehouses' => $warehouses,
            'total' => $total
        ));
    }

}