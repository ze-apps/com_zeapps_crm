<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\Deliveries as DeliveriesModel ;
use App\com_zeapps_crm\Models\DeliveryLines;
use App\com_zeapps_crm\Models\DeliveryLineDetails;
use App\com_zeapps_crm\Models\DeliveryDocuments;
use App\com_zeapps_crm\Models\DeliveryActivities;
use App\com_zeapps_crm\Models\CreditBalances;
use App\com_zeapps_crm\Models\CreditBalanceDetails;

use App\com_zeapps_crm\Models\Orders as OrdersModel ;
use App\com_zeapps_crm\Models\Quotes as QuotesModel ;
use App\com_zeapps_crm\Models\Invoices as InvoicesModel ;

use Zeapps\Models\Config;

class Deliveries extends Controller
{
    public function lists(){
        $data = array();
        return view("deliveries/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function view(){
        $data = array();
        return view("deliveries/view", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_line(){
        $data = array();
        return view("deliveries/form_line", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function lists_partial(){
        $data = array();
        return view("deliveries/lists_partial", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_modal(){
        $data = array();
        return view("deliveries/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }


    public function testFormat(){
        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $format = $data['format'];
        $num = $data['numerotation'];

        $result = DeliveriesModel::parseFormat($format, $num);

        echo json_encode($result);
    }


    public function get(Request $request) {

        $id = $request->input('id', 0);

        $deliverie = DeliveriesModel::where('id', $id)->first();

        $lines = DeliveryLines::orderBy('sort')->where('id_delivery', $id)->get();
        $line_details = DeliveryLineDetails::where('id_delivery', $id)->get();
        $documents = DeliveryDocuments::where('id_delivery', $id)->get();
        $activities = DeliveryActivities::where('id_delivery', $id)->get();

        if($deliverie->id_company) {
            $credits = CreditBalances::where('id_company', $deliverie->id_company)
                ->where('left_to_pay', '>=',  0.01)
                ->get();
        }
        else {
            $credits = CreditBalances::where('id_contact', $deliverie->id_contact)
                ->where('left_to_pay', '>=',  0.01)
                ->get();
        }

        echo json_encode(array(
            'delivery' => $deliverie,
            'lines' => $lines,
            'line_details' => $line_details,
            'documents' => $documents,
            'activities' => $activities,
            'credits' => $credits
        ));
    }



    public function getAll(Request $request) {
        $id = $request->input('id', 0);
        $type = $request->input('type', 'company');
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);
        $context = $request->input('context', false);


        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if ($id != 0) {
            $filters['id_' . $type] = $id;
        }





        $deliveries_rs = DeliveriesModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC') ;
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $deliveries_rs = $deliveries_rs->where($key, 'like', '%' . $value . '%') ;
            } else {
                $deliveries_rs = $deliveries_rs->where($key, $value) ;
            }
        }

        $total = $deliveries_rs->count();
        $deliveries_rs_id = $deliveries_rs ;

        $deliveries = $deliveries_rs->limit($limit)->offset($offset)->get();;


        if(!$deliveries){
            $deliveries = [];
        }


        $ids = [];
        if($total < 500) {
            $rows = $deliveries_rs_id->select(array("id"))->get();
            foreach ($rows as $row) {
                array_push($ids, $row->id);
            }
        }

        echo json_encode(array(
            'deliveries' => $deliveries,
            'total' => $total,
            'ids' => $ids
        ));

    }


    public function modal(Request $request) {
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);



        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }


        $deliveries_rs = DeliveriesModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC') ;
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $deliveries_rs = $deliveries_rs->where($key, 'like', '%' . $value . '%') ;
            } else {
                $deliveries_rs = $deliveries_rs->where($key, $value) ;
            }
        }

        $total = $deliveries_rs->count();
        $deliveries_rs_id = $deliveries_rs ;

        $deliveries = $deliveries_rs->limit($limit)->offset($offset)->get();;


        if(!$deliveries){
            $deliveries = [];
        }

        echo json_encode(array(
            'data' => $deliveries,
            'total' => $total
        ));

    }

    public function save() {
        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }



        $deliverie = new DeliveriesModel() ;

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $deliverie = DeliveriesModel::where('id', $data["id"])->first() ;
        }

        foreach ($data as $key =>$value) {
            $deliverie->$key = $value ;
        }


        $deliverie->save() ;

        echo json_encode($deliverie->id);
    }

    public function delete(Request $request) {
        $id = $request->input('id', 0);

        if($id) {
            DeliveryLines::where('id_delivery', $id)->delete();
            DeliveryLineDetails::where('id_delivery', $id)->delete();

            $documents = DeliveryDocuments::where('id_delivery', $id)->get();

            $path = BASEPATH;

            if ($documents && is_array($documents)) {
                for ($i = 0; $i < sizeof($documents); $i++) {
                    unlink($path . $documents[$i]->path);
                }
            }

            DeliveryDocuments::where('id_delivery', $id)->delete();

            echo json_encode(DeliveriesModel::where("id", $id)->delete());
        } else {
            echo json_encode("ERROR");
        }
    }

    public function transform(Request $request) {
        $id = $request->input('id', 0);

        if($id) {
            // constitution du tableau
            $data = array() ;

            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
                // POST is actually in json format, do an internal translation
                $data = json_decode(file_get_contents('php://input'), true);
            }

            $return = [];

            if($src = DeliveriesModel::where("id", $id)->first()){
                $src->lines = DeliveryLines::where('id_delivery', $id)->get() ;
                $src->line_details = DeliveryLineDetails::where('id_delivery', $id)->get();

                if($data){
                    foreach($data as $document => $value){
                        if($value == 'true'){
                            if ($document == "quotes") {
                                QuotesModel::createFrom($src) ;
                            } elseif ($document == "orders") {
                                OrdersModel::createFrom($src) ;
                            } elseif ($document == "invoices") {
                                InvoicesModel::createFrom($src) ;
                            } elseif ($document == "deliveries") {
                                DeliveriesModel::createFrom($src) ;
                            }
                        }
                    }
                }
            }

            echo json_encode($return);
        }
        else{
            echo json_encode(false);
        }
    }


    public function saveLine(){
        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }


        if (isset($data)) {
            $deliverieLine = new DeliveryLines();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $deliverieLine = DeliveryLines::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $deliverieLine->$key = $value;
            }

            if (!isset($deliverieLine->accounting_number)) {
                $deliverieLine->accounting_number = "" ;
            }


            $deliverieLine->save();
        }

        echo json_encode($deliverieLine->id);
    }

    public function updateLinePosition(){
        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data)) {
            $line = DeliveryLines::where("id", $data['id'])->first() ;

            DeliveryLines::updateOldTable($line->id_delivery, $data['oldSort']);
            DeliveryLines::updateNewTable($line->id_delivery, $data['sort']);

            $DeliveryLine = DeliveryLines::where("id", $data["id"])->first();
            if ($DeliveryLine) {
                $DeliveryLine->sort = $data['sort'] ;
            }
            $DeliveryLine->save();
        }

        echo json_encode($data['id']);
    }

    public function deleteLine(Request $request){
        $id = $request->input('id', 0);

        if($id){
            $line = DeliveryLines::where("id", $id)->first();
            DeliveryLines::updateOldTable($line->id_delivery, $line->sort);
            DeliveryLineDetails::where("id_line", $id)->delete();

            echo json_encode($line->delete());

        }
    }

    public function saveLineDetail(){
        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }


        if (isset($data)) {
            $deliverieLineDetail = new DeliveryLineDetails();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $deliverieLineDetail = DeliveryLineDetails::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $deliverieLineDetail->$key = $value;
            }

            $deliverieLineDetail->save();

            echo json_encode($deliverieLineDetail->id);
        }
    }

    public function activity(){
        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data)) {
            $deliverieActivities = new DeliveryActivities();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $deliverieActivities = DeliveryActivities::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $deliverieActivities->$key = $value;
            }

            $deliverieActivities->save();

            echo json_encode(DeliveryActivities::where("id", $deliverieActivities->id)->first());
        }
    }

    public function del_activity(Request $request){
        $id = $request->input('id', 0);

        echo json_encode(DeliveryActivities::where("id", $id)->delete());
    }
}