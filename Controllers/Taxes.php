<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\Taxes as TaxesModel ;

class Taxes extends Controller
{
    /*public function lists(){
        $data = array();
        return view("quotes/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }*/


    public function getAll() {
        echo json_encode(TaxesModel::get());
    }


    /*public function get(Request $request) {

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
    }*/
}