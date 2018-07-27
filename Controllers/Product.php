<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\ProductProducts;
use App\com_zeapps_crm\Models\ProductCategories;


class Product extends Controller
{
    public function modal_search_product(){
        $data = array();
        return view("product/modal_search_product", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function view(){
        $data = array();
        return view("product/view", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form(Request $request){
        $compose = $request->input('compose', false);

        $data = array();

        if($compose) {
            return view("product/form_compose", $data, BASEPATH . 'App/com_zeapps_crm/views/');
        } else {
            return view("product/form", $data, BASEPATH . 'App/com_zeapps_crm/views/');
        }
    }

    public function config(){
        $data = array();
        return view("product/config", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function config_modal(){
        $data = array();
        return view("product/config_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }




    public function modal(Request $request){
        $id = $request->input('id', 0);
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if($id !== "0") {
            $filters['id_cat'] = ProductCategories::getSubCatIds_r($id);
        }







        $products_rs = ProductProducts::orderBy('name', 'ASC') ;
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $products_rs = $products_rs->where($key, 'like', '%' . $value . '%') ;
            } else {
                $products_rs = $products_rs->where($key, $value) ;
            }
        }

        $total = $products_rs->count();

        $products = $products_rs->limit($limit)->offset($offset)->get();




        if(!$products){
            $products = [];
        }

        echo json_encode(array("data" => $products, "total" => $total));
    }

}