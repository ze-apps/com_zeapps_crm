<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;


use App\com_zeapps_crm\Models\Stock\ProductStocks;
use App\com_zeapps_crm\Models\Stock\StockMovements;
use App\com_zeapps_crm\Models\Stock\Warehouses;
use App\com_zeapps_crm\Models\Product\Products;


class Stock extends Controller
{
    public function view()
    {
        $data = array();
        return view("stock/view", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function details()
    {
        $data = array();
        return view("stock/details", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    /*public function chart()
    {
        $data = array();
        return view("stock/chart", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }*/

    /*public function history()
    {
        $data = array();
        return view("stock/history", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }*/

    /*public function form_modal()
    {
        $data = array();
        return view("stock/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }*/

    /*public function form_transfert()
    {
        $data = array();
        return view("stock/form_transfert", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }*/

    /*public function form_mvt()
    {
        $data = array();
        return view("stock/form_mvt", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }*/

    /*public function modal()
    {
        $data = array();
        return view("stock/modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }*/


    public function get(Request $request)
    {
        $id_stock = $request->input('id_stock', 0);
        $id_warehouse = $request->input('id_warehouse', 0);

        $where = array('com_zeapps_crm_product_stocks.id' => $id_stock);
        if ($id_warehouse) {
            $where['com_zeapps_crm_stocks.id_warehouse'] = $id_warehouse;
        }

        $total = 0;
        $product_stock = ProductStocks::getStock($where);

        if ($product_stock && $product_stock->id) {
            $whereStock = array('id_stock' => $product_stock->id);
            if ($id_warehouse) {
                $whereStock['id_warehouse'] = $id_warehouse;
            }

            $product_stock->avg = StockMovements::avg($whereStock);


            $product_stock->movements = StockMovements::limit(15)->offset(0)->orderBy('date_mvt', 'DESC');
            foreach ($whereStock as $key => $value) {
                $product_stock->movementsCount = $product_stock->movements->where($key, $value);
            }
            $product_stock->movements = $product_stock->movements->get();


            if ($product_stock->movements) {
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

            $product_stock->movementsCount = StockMovements::select('id');
            foreach ($whereStock as $key => $value) {
                $product_stock->movementsCount = $product_stock->movementsCount->where($key, $value);
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


    public function getAll(Request $request)
    {
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);
        $context = $request->input('context', false);

        $filters = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if ($context) {
            $warehouses = Warehouses::get();
        } else {
            $warehouses = null;
        }

        /*if (!$product_stocks = $this->stocks->all($filters, $limit, $offset)) {
            $product_stocks = [];
        }

        $total = $this->stocks->group_by('id_warehouse')->count($filters);*/


        $product_stocks = Products::where("type_product", "product")->where("id_parent", 0)->get();




        $total = 1 ;

        echo json_encode(array(
            'product_stocks' => $product_stocks,
            'warehouses' => $warehouses,
            'total' => $total
        ));
    }


}