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

class Quotes extends Controller
{
    public function lists(){
        $data = array();
        return view("quotes/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
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

        if($id !== '0') {
            $filters['id_' . $type] = $id;
        }





        $quotes_rs = QuotesModel::orderBy('date_creation', 'ASC')->orderBy('id', 'ASC') ;
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


}