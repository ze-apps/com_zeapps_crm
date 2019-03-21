<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use Illuminate\Database\Capsule\Manager as Capsule;


use App\com_zeapps_crm\Models\Stock\ProductStocks;
use App\com_zeapps_crm\Models\Stock\StockMovements;
use App\com_zeapps_crm\Models\Stock\Warehouses;
use App\com_zeapps_crm\Models\Product\Products;
use App\com_zeapps_crm\Models\Product\ProductCategories;


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

    public function chart()
    {
        $data = array();
        return view("stock/chart", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function history()
    {
        $data = array();
        return view("stock/history", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    /*public function form_modal()
    {
        $data = array();
        return view("stock/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }*/

    public function form_transfert()
    {
        $data = array();
        return view("stock/form_transfert", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_mvt()
    {
        $data = array();
        return view("stock/form_mvt", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    /*public function modal()
    {
        $data = array();
        return view("stock/modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }*/


    public function get(Request $request)
    {
        $id_product = $request->input('id_product', 0);
        $id_warehouse = $request->input('id_warehouse', 0);

        $where = array('com_zeapps_crm_product_stocks.id' => $id_product);
        if ($id_warehouse) {
            $where['com_zeapps_crm_stocks.id_warehouse'] = $id_warehouse;
        }

        $total = 0;
        $product_stock = Products::find($id_product);

        if ($product_stock) {
            $whereStock = array('id_product' => $id_product);
            if ($id_warehouse) {
                $whereStock['id_warehouse'] = $id_warehouse;
            }


            $stockMvtProduct = StockMovements::select(Capsule::Raw("SUM(qty) as qty"))
                ->where("id_product", $product_stock->id);
            if ($id_warehouse) {
                $stockMvtProduct = $stockMvtProduct->where("id_warehouse", $id_warehouse);
            }
            $stockMvtProduct = $stockMvtProduct->first();

            $product_stock->qty = 0;
            if ($stockMvtProduct) {
                $product_stock->qty = $stockMvtProduct->qty;
            }


            $product_stock->avg = StockMovements::avg($whereStock);


            $product_stock->movements = StockMovements::limit(15)->offset(0)->orderBy('date_mvt', 'DESC');
            foreach ($whereStock as $key => $value) {
                $product_stock->movementsCount = $product_stock->movements->where($key, $value);
            }
            $product_stock->movements = $product_stock->movements->get();


            if ($product_stock->movements) {
                $last = [];
                $last['month'] = StockMovements::last_year($whereStock);
                $last['dates'] = StockMovements::last_months($whereStock);
                $last['date'] = StockMovements::last_month($whereStock);
                $last['days'] = StockMovements::last_week($whereStock);
                $product_stock->last = $last;
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


        echo json_encode(array(
            'product_stock' => $product_stock,
            'total' => $total
        ));
    }


    public function getAll(Request $request)
    {
        $id = $request->input('id', 0);
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);
        $context = $request->input('context', false);


        $filters = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }


        if ($id !== "0") {
            $filters['id_cat'] = ProductCategories::getSubCatIds_r($id);
        }


        // remove $filters["id_warehouse"] not necessary at this moment
        $id_warehouse = 0;
        if (isset($filters["id_warehouse"])) {
            $id_warehouse = $filters["id_warehouse"];
            unset($filters["id_warehouse"]);
        }


        $product_stocks = Products::select("id", "ref", "name", "price_unit_stock")
            ->where("type_product", "product")
            ->where("active", 1)
            ->where("id_parent", 0);


        foreach ($filters as $key => $value) {
            if ($key == "id_cat") {
                $product_stocks = $product_stocks->whereIn("id_cat", $value);
            } elseif (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $product_stocks = $product_stocks->where($key, 'like', '%' . $value . '%');
            } else {
                $product_stocks = $product_stocks->where($key, $value);
            }
        }


        $total = $product_stocks->count();

        $product_stocks = $product_stocks->limit($limit)->offset($offset)->get();


        // get stock Qty
        foreach ($product_stocks as &$product_stock) {
            $stockMvtProduct = StockMovements::select(Capsule::Raw("SUM(qty) as qty"))
                ->where("id_product", $product_stock->id);
            if ($id_warehouse) {
                $stockMvtProduct = $stockMvtProduct->where("id_warehouse", $id_warehouse);
            }
            $stockMvtProduct = $stockMvtProduct->first();

            $product_stock->qty = 0;
            if ($stockMvtProduct) {
                $product_stock->qty = $stockMvtProduct->qty;
            }


            $whereStock = array();
            if ($id_warehouse) {
                $whereStock["id_warehouse"] = $id_warehouse;
            }
            $whereStock["id_product"] = $product_stock->id;
            $product_stock->avg = StockMovements::avg($whereStock);
        }


        echo json_encode(array(
            'product_stocks' => $product_stocks,
            'total' => $total
        ));
    }


    public function get_movements(Request $request)
    {
        $id_product = $request->input('id_product', 0);
        $id_warehouse = $request->input('id_warehouse', null);
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);


        $stock_movements = StockMovements::where("id_product", $id_product);

        if ($id_warehouse) {
            $stock_movements = $stock_movements->where("id_warehouse", $id_warehouse);
        }

        $total = $stock_movements->count();

        $stock_movements = $stock_movements->limit($limit)->offset($offset)->orderBy('date_mvt', 'DESC')->get();


        echo json_encode(array(
            "stock_movements" => $stock_movements,
            'total' => $total
        ));
    }


    public function add_transfert()
    {
        // constitution du tableau
        $data = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }


        $objStockMovements = new StockMovements();
        $objStockMovements->date_mvt = $data['date_mvt_field'];
        $objStockMovements->label = $data['label'];
        $objStockMovements->id_product = $data['id_product'];
        $objStockMovements->id_warehouse = $data['src'];
        $objStockMovements->qty = $data['qty'] * -1;
        $objStockMovements->ignored = 1;
        $objStockMovements->save();


        $objStockMovements = new StockMovements();
        $objStockMovements->date_mvt = $data['date_mvt_field'];
        $objStockMovements->label = $data['label'];
        $objStockMovements->id_product = $data['id_product'];
        $objStockMovements->id_warehouse = $data['trgt'];
        $objStockMovements->qty = $data['qty'];
        $objStockMovements->ignored = 1;
        $objStockMovements->save();

        echo json_encode($objStockMovements->id);
    }


    public function add_mvt()
    {
        // constitution du tableau
        $data = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $objStockMovements = new StockMovements();
        $objStockMovements->date_mvt = $data['date_mvt_field'];
        $objStockMovements->label = $data['label'];
        $objStockMovements->id_product = $data['id_product'];
        $objStockMovements->id_warehouse = $data['id_warehouse'];
        $objStockMovements->qty = $data['qty'];
        $objStockMovements->ignored = 1;
        $objStockMovements->save();

        echo $objStockMovements->id;
    }


    public function ignore_mvt(Request $request)
    {
        $id = $request->input('id', 0);
        $value = $request->input('value', 0);
        $id_product = $request->input('id_product', 0);
        $id_warehouse = $request->input('id_warehouse', 0);

        $objStockMovements = StockMovements::find($id);
        if ($objStockMovements) {
            $objStockMovements->ignored = $value ;
            $objStockMovements->save();
        }

        $w = array('id_product' => $id_product);
        if ($id_warehouse) {
            $w['id_warehouse'] = $id_warehouse;
        }

        $avg = StockMovements::avg($w);

        echo $avg;
    }
}