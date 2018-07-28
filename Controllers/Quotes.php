<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\Quotes as QuotesModel ;
use App\com_zeapps_crm\Models\QuoteLines;
use App\com_zeapps_crm\Models\QuoteLineDetails;
use App\com_zeapps_crm\Models\QuoteDocuments;
use App\com_zeapps_crm\Models\QuoteActivities;
use App\com_zeapps_crm\Models\CreditBalances;
use App\com_zeapps_crm\Models\CreditBalanceDetails;

use App\com_zeapps_crm\Models\Invoices as InvoicesModel ;
use App\com_zeapps_crm\Models\Orders as OrdersModel ;
use App\com_zeapps_crm\Models\Deliveries as DeliveriesModel ;

use Zeapps\Models\Config;

class Quotes extends Controller
{
    public function lists(){
        $data = array();
        return view("quotes/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function view(){
        $data = array();
        return view("quotes/view", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_line(){
        $data = array();
        return view("quotes/form_line", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function lists_partial(){
        $data = array();
        return view("quotes/lists_partial", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_modal(){
        $data = array();
        return view("quotes/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
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

        $result = QuotesModel::parseFormat($format, $num);

        echo json_encode($result);
    }


    public function get(Request $request) {

        $id = $request->input('id', 0);

        $quote = QuotesModel::where('id', $id)->first();

        $lines = QuoteLines::orderBy('sort')->where('id_quote', $id)->get();
        $line_details = QuoteLineDetails::where('id_quote', $id)->get();
        $documents = QuoteDocuments::where('id_quote', $id)->get();
        $activities = QuoteActivities::where('id_quote', $id)->get();

        if($quote->id_company) {
            $credits = CreditBalances::where('id_company', $quote->id_company)
                ->where('left_to_pay', '>=',  0.01)
                ->get();
        }
        else {
            $credits = CreditBalances::where('id_contact', $quote->id_contact)
                ->where('left_to_pay', '>=',  0.01)
                ->get();
        }

        echo json_encode(array(
            'quote' => $quote,
            'lines' => $lines,
            'line_details' => $line_details,
            'documents' => $documents,
            'activities' => $activities,
            'credits' => $credits
        ));
    }



    public function getAll(Request $request) {
        $id = $request->input('id', 0);
        $type = $request->input('id', 'company');
        $limit = $request->input('id', 15);
        $offset = $request->input('id', 0);
        $context = $request->input('id', false);


        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if ($id != 0) {
            $filters['id_' . $type] = $id;
        }





        $quotes_rs = QuotesModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC') ;
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $quotes_rs = $quotes_rs->where($key, 'like', '%' . $value . '%') ;
            } else {
                $quotes_rs = $quotes_rs->where($key, $value) ;
            }
        }

        $total = $quotes_rs->count();
        $quotes_rs_id = $quotes_rs ;

        $quotes = $quotes_rs->limit($limit)->offset($offset)->get();;


        if(!$quotes){
            $quotes = [];
        }


        $ids = [];
        if($total < 500) {
            $rows = $quotes_rs_id->select(array("id"))->get();
            foreach ($rows as $row) {
                array_push($ids, $row->id);
            }
        }

        echo json_encode(array(
            'quotes' => $quotes,
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


        $quotes_rs = QuotesModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC') ;
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $quotes_rs = $quotes_rs->where($key, 'like', '%' . $value . '%') ;
            } else {
                $quotes_rs = $quotes_rs->where($key, $value) ;
            }
        }

        $total = $quotes_rs->count();
        $quotes_rs_id = $quotes_rs ;

        $quotes = $quotes_rs->limit($limit)->offset($offset)->get();;


        if(!$quotes){
            $quotes = [];
        }

        echo json_encode(array(
            'data' => $quotes,
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



        $quote = new QuotesModel() ;
        $createNumber = true ;

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $quote = QuotesModel::where('id', $data["id"])->first() ;
            if ($quote) {
                $createNumber = false ;
            }
        }

        foreach ($data as $key =>$value) {
            $quote->$key = $value ;
        }

        if ($createNumber) {
            $format = Config::where('id', 'crm_quote_format')->first()->value ;
            $num = QuotesModel::get_numerotation();
            $quote->numerotation = QuotesModel::parseFormat($format, $num);
        }

        $quote->save() ;

        echo json_encode($quote->id);
    }

    public function delete(Request $request) {
        $id = $request->input('id', 0);

        if($id) {
            QuoteLines::where('id_quote', $id)->delete();
            QuoteLineDetails::where('id_quote', $id)->delete();

            $documents = QuoteDocuments::where('id_quote', $id)->get();

            $path = BASEPATH;

            if ($documents && is_array($documents)) {
                for ($i = 0; $i < sizeof($documents); $i++) {
                    unlink($path . $documents[$i]->path);
                }
            }

            QuoteDocuments::where('id_quote', $id)->delete();

            echo json_encode(QuotesModel::where("id", $id)->delete());
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

            if($src = QuotesModel::where("id", $id)->first()){
                $src->lines = QuoteLines::where('id_quote', $id)->get() ;
                $src->line_details = QuoteLineDetails::where('id_quote', $id)->get();

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
            $quoteLine = new QuoteLines();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $quoteLine = QuoteLines::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $quoteLine->$key = $value;
            }

            if (!isset($quoteLine->accounting_number)) {
                $quoteLine->accounting_number = "" ;
            }


            $quoteLine->save();
        }

        echo json_encode($quoteLine->id);
    }

    public function updateLinePosition(){
        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data)) {
            $line = QuoteLines::where("id", $data['id'])->first() ;

            QuoteLines::updateOldTable($line->id_quote, $data['oldSort']);
            QuoteLines::updateNewTable($line->id_quote, $data['sort']);

            $QuoteLine = QuoteLines::where("id", $data["id"])->first();
            if ($QuoteLine) {
                $QuoteLine->sort = $data['sort'] ;
            }
            $QuoteLine->save();
        }

        echo json_encode($data['id']);
    }

    public function deleteLine(Request $request){
        $id = $request->input('id', 0);

        if($id){
            $line = QuoteLines::where("id", $id)->first();
            QuoteLines::updateOldTable($line->id_quote, $line->sort);
            QuoteLineDetails::where("id_line", $id)->delete();

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
            $quoteLineDetail = new QuoteLineDetails();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $quoteLineDetail = QuoteLineDetails::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $quoteLineDetail->$key = $value;
            }

            $quoteLineDetail->save();

            echo json_encode($quoteLineDetail->id);
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
            $quoteActivities = new QuoteActivities();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $quoteActivities = QuoteActivities::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $quoteActivities->$key = $value;
            }

            $quoteActivities->save();

            echo json_encode(QuoteActivities::where("id", $quoteActivities->id)->first());
        }
    }

    public function del_activity(Request $request){
        $id = $request->input('id', 0);

        echo json_encode(QuoteActivities::where("id", $id)->delete());
    }
}