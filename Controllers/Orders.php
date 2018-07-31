<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\Orders as OrdersModel ;
use App\com_zeapps_crm\Models\OrderLines;
use App\com_zeapps_crm\Models\OrderLineDetails;
use App\com_zeapps_crm\Models\OrderDocuments;
use App\com_zeapps_crm\Models\OrderActivities;
use App\com_zeapps_crm\Models\CreditBalances;
use App\com_zeapps_crm\Models\CreditBalanceDetails;

use App\com_zeapps_crm\Models\Invoices as InvoicesModel ;
use App\com_zeapps_crm\Models\Quotes as QuotesModel ;
use App\com_zeapps_crm\Models\Deliveries as DeliveriesModel ;

use Zeapps\Models\Config;

class Orders extends Controller
{
    public function lists(){
        $data = array();
        return view("orders/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function view(){
        $data = array();
        return view("orders/view", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_line(){
        $data = array();
        return view("orders/form_line", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function lists_partial(){
        $data = array();
        return view("orders/lists_partial", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_modal(){
        $data = array();
        return view("orders/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
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

        $result = OrdersModel::parseFormat($format, $num);

        echo json_encode($result);
    }


    public function get(Request $request) {

        $id = $request->input('id', 0);

        $order = OrdersModel::where('id', $id)->first();

        $lines = OrderLines::orderBy('sort')->where('id_order', $id)->get();
        $line_details = OrderLineDetails::where('id_order', $id)->get();
        $documents = OrderDocuments::where('id_order', $id)->get();
        $activities = OrderActivities::where('id_order', $id)->get();

        if($order->id_company) {
            $credits = CreditBalances::where('id_company', $order->id_company)
                ->where('left_to_pay', '>=',  0.01)
                ->get();
        }
        else {
            $credits = CreditBalances::where('id_contact', $order->id_contact)
                ->where('left_to_pay', '>=',  0.01)
                ->get();
        }

        echo json_encode(array(
            'order' => $order,
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





        $orders_rs = OrdersModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC') ;
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $orders_rs = $orders_rs->where($key, 'like', '%' . $value . '%') ;
            } else {
                $orders_rs = $orders_rs->where($key, $value) ;
            }
        }

        $total = $orders_rs->count();
        $orders_rs_id = $orders_rs ;

        $orders = $orders_rs->limit($limit)->offset($offset)->get();;


        if(!$orders){
            $orders = [];
        }


        $ids = [];
        if($total < 500) {
            $rows = $orders_rs_id->select(array("id"))->get();
            foreach ($rows as $row) {
                array_push($ids, $row->id);
            }
        }

        echo json_encode(array(
            'orders' => $orders,
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


        $orders_rs = OrdersModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC') ;
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $orders_rs = $orders_rs->where($key, 'like', '%' . $value . '%') ;
            } else {
                $orders_rs = $orders_rs->where($key, $value) ;
            }
        }

        $total = $orders_rs->count();
        $orders_rs_id = $orders_rs ;

        $orders = $orders_rs->limit($limit)->offset($offset)->get();;


        if(!$orders){
            $orders = [];
        }

        echo json_encode(array(
            'data' => $orders,
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



        $order = new OrdersModel() ;

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $order = OrdersModel::where('id', $data["id"])->first() ;
        }

        foreach ($data as $key =>$value) {
            $order->$key = $value ;
        }

        $order->save() ;

        echo json_encode($order->id);
    }

    public function delete(Request $request) {
        $id = $request->input('id', 0);

        if($id) {
            OrderLines::where('id_order', $id)->delete();
            OrderLineDetails::where('id_order', $id)->delete();

            $documents = OrderDocuments::where('id_order', $id)->get();

            $path = BASEPATH;

            if ($documents && is_array($documents)) {
                for ($i = 0; $i < sizeof($documents); $i++) {
                    unlink($path . $documents[$i]->path);
                }
            }

            OrderDocuments::where('id_order', $id)->delete();

            echo json_encode(OrdersModel::where("id", $id)->delete());
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

            if($src = OrdersModel::where("id", $id)->first()){
                $src->lines = OrderLines::where('id_order', $id)->get() ;
                $src->line_details = OrderLineDetails::where('id_order', $id)->get();

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
            $orderLine = new OrderLines();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $orderLine = OrderLines::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $orderLine->$key = $value;
            }

            if (!isset($orderLine->accounting_number)) {
                $orderLine->accounting_number = "" ;
            }


            $orderLine->save();
        }

        echo json_encode($orderLine->id);
    }

    public function updateLinePosition(){
        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data)) {
            $line = OrderLines::where("id", $data['id'])->first() ;

            OrderLines::updateOldTable($line->id_order, $data['oldSort']);
            OrderLines::updateNewTable($line->id_order, $data['sort']);

            $OrderLine = OrderLines::where("id", $data["id"])->first();
            if ($OrderLine) {
                $OrderLine->sort = $data['sort'] ;
            }
            $OrderLine->save();
        }

        echo json_encode($data['id']);
    }

    public function deleteLine(Request $request){
        $id = $request->input('id', 0);

        if($id){
            $line = OrderLines::where("id", $id)->first();
            OrderLines::updateOldTable($line->id_order, $line->sort);
            OrderLineDetails::where("id_line", $id)->delete();

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
            $orderLineDetail = new OrderLineDetails();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $orderLineDetail = OrderLineDetails::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $orderLineDetail->$key = $value;
            }

            $orderLineDetail->save();

            echo json_encode($orderLineDetail->id);
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
            $orderActivities = new OrderActivities();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $orderActivities = OrderActivities::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $orderActivities->$key = $value;
            }

            $orderActivities->save();

            echo json_encode(OrderActivities::where("id", $orderActivities->id)->first());
        }
    }

    public function del_activity(Request $request){
        $id = $request->input('id', 0);

        echo json_encode(OrderActivities::where("id", $id)->delete());
    }
}