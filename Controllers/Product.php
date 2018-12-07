<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\Products;
use App\com_zeapps_crm\Models\ProductLines;
use App\com_zeapps_crm\Models\ProductCategories;


class Product extends Controller
{
    public function modal_search_product()
    {
        $data = array();
        return view("product/modal_search_product", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function view()
    {
        $data = array();
        return view("product/view", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form(Request $request)
    {
        $compose = $request->input('compose', false);

        $data = array();

        if ($compose) {
            return view("product/form_compose", $data, BASEPATH . 'App/com_zeapps_crm/views/');
        } else {
            return view("product/form", $data, BASEPATH . 'App/com_zeapps_crm/views/');
        }
    }

    public function config()
    {
        $data = array();
        return view("product/config", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function config_modal()
    {
        $data = array();
        return view("product/config_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }


    public function modal(Request $request)
    {
        $id = $request->input('id', 0);
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);

        $filters = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if ($id !== "0") {
            $filters['id_cat'] = ProductCategories::getSubCatIds_r($id);
        }


        $products_rs = Products::orderBy('name', 'ASC');
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $products_rs = $products_rs->where($key, 'like', '%' . $value . '%');
            } else {
                $products_rs = $products_rs->where($key, $value);
            }
        }

        $total = $products_rs->count();

        $products = $products_rs->limit($limit)->offset($offset)->get();


        if (!$products) {
            $products = [];
        }

        echo json_encode(array("data" => $products, "total" => $total));
    }


    public function get(Request $request)
    {
        $id = $request->input('id', 0);

        if (isset($id)) {
            $product = Products::where("id", $id)->first();

            if ($product && $product->compose == 1) {
                $lines_array = array();


                // TODO : mettre en place la lecture des produits associés
                /*$lines = ProductLines::where("id_product", $id)->get();
                if ($lines) {
                    foreach ($lines as $line) {
                        if ($part = Products::where("id", $line->id_part)->first()) {
                            $line->product = $part;
                            $lines_array[] = $line;
                        }
                    }
                }*/

                $product->lines = $lines_array ;
            }

            echo json_encode($product);
        }
        return;
    }

    public function get_code(Request $request)
    {
        $code = $request->input('code', NULL);

        if (isset($code)) {
            $product = Products::where("ref", $code)->first();


            // TODO : traiter les lignes composées
            /*if ($product && $product->compose == 1) {
                $lines = ProductLines::where("id_product", $product->id)->get();
                $product->lines = [];
                if ($lines && is_array($lines)) {
                    foreach ($lines as $line) {
                        if ($part = Products::where("id", $line->id_part)->fisrt()) {
                            $line->product = $part;
                        }
                        array_push($product->lines, $line);
                    }
                }
            }*/

            if ($product) {
                echo json_encode($product);
            } else {
                echo json_encode(false);
            }
        } else {
            echo json_encode(false);
        }
    }

    public function getAll()
    {
        echo json_encode(Products::get());
    }

    public function save()
    {
        $error = NULL;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);


            if (isset($data['lines'])) {
                $lines = $data['lines'];
                unset($data['lines']);
            } else {
                $lines = false;
            }


            $product = new Products();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $product = Products::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $product->$key = $value;
            }

            $product->save();
            $data['id'] = $product->id;


            if (isset($data['compose']) && $data['compose'] == '1' && $lines) {
                foreach ($lines as $line) {
                    $line['id_product'] = $data['id'];
                    $line['auto'] = isset($data['auto']) ? $data['auto'] : 0;
                    unset($line['product']);

                    $productLine = new ProductLines();

                    if (isset($line["id"]) && is_numeric($line["id"]) && $line["id"] != 0) {
                        $productLine = ProductLines::where('id', $line["id"])->first();
                    }

                    foreach ($line as $key => $value) {
                        $productLine->$key = $value;
                    }

                    $productLine->save();
                }
            }





            if ($lines = ProductLines::where("id_part", $data['id'])->where("auto", true)->get()) {
                foreach ($lines as $line) {
                    $this->_updatePriceOf($line->id_product);
                }
            }
        }

        echo json_encode("OK");
        return;

    }

    public function delete(Request $request)
    {
        $id = $request->input('id', 0);

        if (isset($id)) {
            $product = Products::where("id", $id)->first() ;

            if ($product->compose) {
                ProductLines::where("id_product", $id)->delete();
            }

            $product->delete($id);

            if ($product->id_cat > 0) {
                ProductCategories::removeProductIn($product->id_cat);
            }

        }
        return;
    }

    private function _updatePriceOf(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            if ($lines = ProductLines::where("id_product", $id)->get()) {
                $price_ht = 0;
                $price_ttc = 0;
                foreach ($lines as $line) {
                    $part = Products::where("id", $line->id_part)->first() ;
                    $price_ht += (floatval($part->price_ttc) * floatval($line->quantite));
                    $price_ttc += (floatval($part->price_ttc) * floatval($line->quantite));
                }

                Products::where("id", $id)->update(array('price_ht' => $price_ht, 'price_ttc' => $price_ttc));
            }
        }
    }

}