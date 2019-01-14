<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\PriceList as PriceListModel;
use App\com_zeapps_crm\Models\PriceListRate;

class PriceList extends Controller
{
    public function lists(){
        $data = array();
        return view("priceList/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_modal(){
        $data = array();
        return view("priceList/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function taux(){
        $data = array();
        return view("priceList/taux", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }




    public function get(Request $request)
    {
        $id = $request->input('id', 0);
        echo json_encode(PriceListModel::find($id));
    }

    public function getAll(Request $request)
    {
        $priceList = PriceListModel::where("active", 1)->get();
        echo json_encode($priceList);
    }

    public function getAllAdmin(Request $request)
    {
        $priceLists = PriceListModel::get();


        foreach ($priceLists as &$priceList) {
            $priceList->type_pricelist_label = PriceListModel::getPriceListType($priceList->type_pricelist) ;
        }


        echo json_encode($priceLists);
    }

    public function getPriceListType(Request $request)
    {
        echo json_encode(PriceListModel::getPriceListTypes());
    }


    public function save() {
        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }


        if (isset($data["default"]) && is_numeric($data["default"]) && $data["default"] == 1) {
            PriceListModel::where("id", ">=", 0)->update(['default' => 0]);
        }


        $priceList = new PriceListModel() ;

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $priceList = PriceListModel::where('id', $data["id"])->first() ;
        }

        foreach ($data as $key =>$value) {
            $priceList->$key = $value ;
        }

        $priceList->save() ;

        echo json_encode($priceList->id);
    }

    public function delete(Request $request) {
        $id = $request->input('id', 0);

        if($id) {
            echo json_encode(PriceListModel::where("id", $id)->delete());
        } else {
            echo json_encode("ERROR");
        }
    }


    public function rate(Request $request)
    {
        $id_pricelist = $request->input('id_pricelist', 0);
        $priceList = PriceListRate::where("id_pricelist", $id_pricelist)->get();
        echo json_encode($priceList);
    }

    public function rateSave()
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);

            $id_pricelist = $data['id_pricelist'];
            $id_category = $data['id_category'];
            $percentage = $data['percentage'];
            $accounting_number = $data['accounting_number'];
            $id_taxe = $data['id_taxe'];
            $value_taxe = $data['value_taxe'];


            $objPriceListRate = PriceListRate::where("id_pricelist", $id_pricelist)->where("id_category", $id_category)->first();

            if (!$objPriceListRate) {
                $objPriceListRate = new PriceListRate() ;
            }

            $objPriceListRate->id_pricelist = $id_pricelist ;
            $objPriceListRate->id_category = $id_category ;
            $objPriceListRate->percentage = $percentage ;
            $objPriceListRate->accounting_number = $accounting_number ;
            $objPriceListRate->id_taxe = $id_taxe ;
            $objPriceListRate->value_taxe = $value_taxe ;

            $objPriceListRate->save();
        }

        echo json_encode("OK");
        return;
    }
}