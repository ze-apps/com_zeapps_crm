<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\Product\Products;
use App\com_zeapps_crm\Models\Product\ProductLines;
use App\com_zeapps_crm\Models\Product\ProductCategories;
use App\com_zeapps_crm\Models\Product\ProductPriceList;


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

        return view("product/form", $data, BASEPATH . 'App/com_zeapps_crm/views/');
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


        $products_rs = Products::where("id_parent", 0)->orderBy('name', 'ASC');
        foreach ($filters as $key => $value) {
            if ($key == "id_cat") {
                $products_rs = $products_rs->whereIn("id_cat", $value);
            } elseif (strpos($key, " LIKE")) {
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
            $product->sublines = array();


            // recupère les tarifs de la grille
            $priceList = array();
            $ProductPriceLists = ProductPriceList::where("id_product", $product->id)->get();
            if ($ProductPriceLists) {
                foreach ($ProductPriceLists as $ProductPriceList) {
                    $priceList[$ProductPriceList->id_price_list] = $ProductPriceList ;
                }
            }
            $product->priceList = $priceList;




            if ($product->type_product == "pack") {
                $product->sublines = Products::where("id_parent", $id)->get();

                foreach ($product->sublines as &$subline) {
                    $priceListSubline = array();
                    $ProductPriceLists = ProductPriceList::where("id_product", $subline->id_product)->get();

                    if ($ProductPriceLists) {
                        foreach ($ProductPriceLists as $ProductPriceList) {
                            $priceListSubline[$ProductPriceList->id_price_list] = $ProductPriceList ;
                        }
                    }

                    $subline->priceList = $priceListSubline;
                }
            }

            echo json_encode($product, JSON_PRETTY_PRINT);
        }
        return;
    }

    public function get_code(Request $request)
    {
        $code = $request->input('code', NULL);

        if (isset($code)) {
            $product = Products::where("ref", $code)->first();
            if ($product) {
                $product->sublines = array();

                // recupère les tarifs de la grille
                $priceList = array();
                $ProductPriceLists = ProductPriceList::where("id_product", $product->id)->get();
                if ($ProductPriceLists) {
                    foreach ($ProductPriceLists as $ProductPriceList) {
                        $priceList[$ProductPriceList->id_price_list] = $ProductPriceList ;
                    }
                }
                $product->priceList = $priceList;


                if ($product->type_product == "pack") {
                    $product->sublines = Products::where("id_parent", $product->id)->get();

                    foreach ($product->sublines as &$subline) {
                        $priceListSubline = array();
                        $ProductPriceLists = ProductPriceList::where("id_product", $subline->id_product)->get();

                        if ($ProductPriceLists) {
                            foreach ($ProductPriceLists as $ProductPriceList) {
                                $priceListSubline[$ProductPriceList->id_price_list] = $ProductPriceList ;
                            }
                        }

                        $subline->priceList = $priceListSubline;
                    }
                }
            }

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

            $product = new Products();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $product = Products::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $product->$key = $value;
            }

            $product->save();
            $data['id'] = $product->id;




            // enregistre les tarifs de la grille de prix
            if (isset($data['priceList'])) {
                foreach ($data['priceList'] as $indexPriceList => $priceList) {
                    if ($indexPriceList >= 1) {
                        $productPriceList = new ProductPriceList();

                        if (isset($priceList["id"]) && is_numeric($priceList["id"])) {
                            $productPriceList = ProductPriceList::where('id', $priceList["id"])->first();
                        }

                        foreach ($priceList as $key => $value) {
                            $productPriceList->$key = $value;
                        }

                        $productPriceList->id_product = $product->id;
                        $productPriceList->id_price_list = $indexPriceList;


                        $productPriceList->save();
                    }
                }
            }





            if (isset($data['type_product']) && $data['type_product'] == 'pack' && isset($data['sublines']) && count($data['sublines'])) {

                // recherche les lignes existantes pour voir si on doit les supprimer
                $sublines = Products::where('id_parent', $product->id)->get();


                foreach ($data['sublines'] as $line) {
                    $productLine = new Products();

                    if (isset($line["id"]) && is_numeric($line["id"]) && $line["id"]) {
                        foreach ($sublines as &$subline) {
                            if ($subline->id == $line["id"]) {
                                $subline->dont_delete = true ;
                            }
                        }

                        $productLine = Products::where('id', $line["id"])->first();
                    }

                    foreach ($line as $key => $value) {
                        $productLine->$key = $value;
                    }

                    $productLine->id_parent = $product->id ;
                    $productLine->save();
                }


                // supprime les lignes qui ne sont plus utilisé
                foreach ($sublines as $subline) {
                    if (!isset($subline->dont_delete)) {
                        $subline->delete();
                    }
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