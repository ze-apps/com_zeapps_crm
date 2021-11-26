<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;
use Zeapps\Core\Log;

use Zeapps\Core\Event;

use Zeapps\Core\Storage;
use Mpdf\Mpdf;

use App\com_zeapps_crm\Models\Quote\Quotes as QuotesModel;
use App\com_zeapps_crm\Models\Quote\QuoteLines;
use App\com_zeapps_crm\Models\Quote\QuoteDocuments;
use App\com_zeapps_crm\Models\Quote\QuoteActivities;
use App\com_zeapps_crm\Models\Quote\QuoteLinePriceList;
use App\com_zeapps_crm\Models\Quote\QuoteTaxes;
use App\com_zeapps_crm\Models\CreditBalances;
use App\com_zeapps_crm\Models\CreditBalanceDetails;

use App\com_zeapps_crm\Models\Invoice\Invoices as InvoicesModel;
use App\com_zeapps_crm\Models\Order\Orders as OrdersModel;
use App\com_zeapps_crm\Models\Delivery\Deliveries as DeliveriesModel;

use App\com_zeapps_crm\Models\DocumentRelated;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Zeapps\Controllers\User;
use Zeapps\Models\Config;

use Zeapps\Core\Mail;

class Quotes extends Controller
{
    public function lists()
    {
        $data = array();
        return view("quotes/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function view()
    {
        $data = array();
        return view("quotes/view", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_line()
    {
        $data = array();
        return view("quotes/form_line", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function lists_partial()
    {
        $data = array();
        return view("quotes/lists_partial", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_modal()
    {
        $data = array();
        return view("quotes/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function modal_activities_list()
    {
        $data = array();
        return view("quotes/modal_activities_list", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }


    

    public function testFormat()
    {
        // constitution du tableau
        $data = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $format = $data['format'];
        $num = $data['numerotation'];

        $result = QuotesModel::parseFormat($format, $num);

        echo json_encode($result);
    }


    public function get(Request $request)
    {
        $id = $request->input('id', 0);

        $quote = QuotesModel::where('id', $id)->first();
        $lines = QuoteLines::getFromQuote($id);
        $tableTaxes = QuoteTaxes::getTableTaxe($id);


        $documents = QuoteDocuments::where('id_quote', $id)->get();

        $activities = QuoteActivities::where('id_quote', $id)->orderBy("deadline", "DESC")->get();
        
        if ($quote->id_company) {
            $credits = CreditBalances::where('id_company', $quote->id_company)
                ->where('left_to_pay', '>=', 0.01)
                ->get();
        } else {
            $credits = CreditBalances::where('id_contact', $quote->id_contact)
                ->where('left_to_pay', '>=', 0.01)
                ->get();
        }

        echo json_encode(array(
            'quote' => $quote,
            'lines' => $lines,
            'tableTaxes' => $tableTaxes,
            'documents' => $documents,
            'activities' => $activities,
            'credits' => $credits
        ));
    }

    public function getActivite(Request $request) {
        $id = $request->input('id', 0);
        
        $activities = QuoteActivities::where('id_quote', $id)->orderBy("deadline", "DESC")->get();
        echo json_encode($activities);
    }


    public function getAll(Request $request)
    {
        $id = $request->input('id', 0);
        $type = $request->input('type', 'company');
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);
        $context = $request->input('context', false);


        $filters = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if ($id != 0) {
            $filters['id_' . $type] = $id;
        }


        $quotes_rs = QuotesModel::select("com_zeapps_crm_quotes.*")
                ->groupBy('com_zeapps_crm_quotes.id')
                ->orderBy('com_zeapps_crm_quotes.id', 'DESC');
        
        if (isset($filters["date_activite >="]) || isset($filters["date_activite >="])) {
            $quotes_rs = $quotes_rs->join('com_zeapps_crm_quote_activities', 'com_zeapps_crm_quote_activities.id_quote', '=', 'com_zeapps_crm_quotes.id');
            $quotes_rs = $quotes_rs->where("com_zeapps_crm_quote_activities.status", "A faire");
        }
        



        foreach ($filters as $key => $value) {
            if ($key == "id_account_family") {
                $quotes_rs = $quotes_rs->join('com_zeapps_contact_companies', 'com_zeapps_contact_companies.id', '=', 'com_zeapps_crm_quotes.id_company');
                $quotes_rs = $quotes_rs->where("com_zeapps_contact_companies.id_account_family", $value);

            } elseif ($key == "date_activite >=") {
                $quotes_rs = $quotes_rs->where("com_zeapps_crm_quote_activities.deadline", ">=", $value);

            } elseif ($key == "date_activite <=") {
                $quotes_rs = $quotes_rs->where("com_zeapps_crm_quote_activities.deadline", "<=", $value);

            } elseif (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $quotes_rs = $quotes_rs->where("com_zeapps_crm_quotes." . $key, 'like', '%' . $value . '%');

            } elseif (strpos($key, " ") !== false) {
                $tabKey = explode(" ", $key);
                $quotes_rs = $quotes_rs->where("com_zeapps_crm_quotes." . $tabKey[0], $tabKey[1], $value);

            } else {
                $quotes_rs = $quotes_rs->where("com_zeapps_crm_quotes." . $key, $value);
            }
        }

        
        
        // $total = $quotes_rs->count();
        $quotes_rs_id = $quotes_rs;

        $total = $quotes_rs->get()->count(); // attention, on peut avoir un bug sur une grosse quantité de résultats
        $quotes = $quotes_rs->limit($limit)->offset($offset)->get();



        if (!$quotes) {
            $quotes = [];
        }


        $ids = [];
        if ($total < 500) {
            $rows = $quotes_rs_id->select(array("com_zeapps_crm_quotes.id"))->get();
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


    public function modal(Request $request)
    {
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);


        $filters = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }


        $quotes_rs = QuotesModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC');
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $quotes_rs = $quotes_rs->where($key, 'like', '%' . $value . '%');
            } else {
                $quotes_rs = $quotes_rs->where($key, $value);
            }
        }

        $total = $quotes_rs->count();
        $quotes_rs_id = $quotes_rs;

        $quotes = $quotes_rs->limit($limit)->offset($offset)->get();;


        if (!$quotes) {
            $quotes = [];
        }

        echo json_encode(array(
            'data' => $quotes,
            'total' => $total
        ));

    }

    public function save()
    {
        // constitution du tableau
        $data = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }


        $quote = new QuotesModel();

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $quote = QuotesModel::where('id', $data["id"])->first();
        }

        foreach ($data as $key => $value) {
            $quote->$key = $value;
        }

        $quote->save();

        echo json_encode($quote->id);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            QuoteLines::where('id_quote', $id)->delete();

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

    public function transform(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            // constitution du tableau
            $data = array();

            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
                // POST is actually in json format, do an internal translation
                $data = json_decode(file_get_contents('php://input'), true);
            }

            $return = new \stdClass();


            if ($src = QuotesModel::where("id", $id)->first()) {
                $src->lines = QuoteLines::getFromQuote($id);

                if ($data) {
                    foreach ($data as $document => $value) {
                        if ($value == 'true') {
                            $idTo = 0 ;

                            if ($document == "quotes") {
                                $return->quotes = QuotesModel::createFrom($src, QuotesModel::class);
                                $idTo = $return->quotes["id"];

                            } elseif ($document == "orders") {
                                $return->orders = OrdersModel::createFrom($src, QuotesModel::class);
                                $idTo = $return->orders["id"];

                            } elseif ($document == "invoices") {
                                $return->invoices = InvoicesModel::createFrom($src, QuotesModel::class);
                                $idTo = $return->invoices["id"];

                            } elseif ($document == "deliveries") {
                                $return->deliveries = DeliveriesModel::createFrom($src, QuotesModel::class);
                                $idTo = $return->deliveries["id"];
                            }

                            $objDocumentRelated = new DocumentRelated() ;
                            $objDocumentRelated->type_document_from = "quotes" ;
                            $objDocumentRelated->id_document_from = $id ;
                            $objDocumentRelated->type_document_to = $document ;
                            $objDocumentRelated->id_document_to = $idTo ;
                            $objDocumentRelated->save();

                            Event::sendAction('com_zeapps_crm_transform', 'transform', $objDocumentRelated);
                        }
                    }
                }
            }

            echo json_encode($return);
        } else {
            echo json_encode(false);
        }
    }


    public function saveLine()
    {
        // constitution du tableau
        $data = array();
        $id_quote_line = 0 ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }


        if (isset($data)) {
            $id_quote_line = $this->saveLineData($data, $data["id_quote"], 0) ;
        }

        echo json_encode($id_quote_line);
    }

    private function saveLineData($data, $id_quote, $id_parent) {

        $idSublineToDelete = array() ;


        $quoteLine = new QuoteLines();

        if (isset($data["id"]) && is_numeric($data["id"])) {
            // load subline to check if need to delete
            $sublines = QuoteLines::where("id_parent", $data["id"])->get() ;

            foreach ($sublines as $subline) {
                $idSublineToDelete[] = $subline->id ;
            }


            // load line
            $quoteLine = QuoteLines::where('id', $data["id"])->first();
        }

        if (!isset($data["id_quote"])) {
            $data["id_quote"] = $id_quote ;
        }


        foreach ($data as $key => $value) {
            $quoteLine->$key = $value;
        }


        // set id parent line
        $quoteLine->id_parent = $id_parent ;



        if (!isset($quoteLine->accounting_number)) {
            $quoteLine->accounting_number = "";
        }


        $quoteLine->save();



        // save price list
        if (isset($data["priceList"]) && count($data["priceList"])) {
            foreach ($data["priceList"] as $priceList) {

                $quoteLinePriceList = QuoteLinePriceList::where("id_quote_line", $quoteLine->id)->where("id_price_list", $priceList["id_price_list"])->first();

                if (!$quoteLinePriceList) {
                    $quoteLinePriceList = new QuoteLinePriceList() ;
                    $quoteLinePriceList->id_quote_line = $quoteLine->id ;
                    $quoteLinePriceList->id_price_list = $priceList["id_price_list"] ;
                }
                $quoteLinePriceList->accounting_number = $priceList["accounting_number"] ;
                $quoteLinePriceList->price_ht = $priceList["price_ht"] ;
                $quoteLinePriceList->price_ttc = $priceList["price_ttc"] ;
                $quoteLinePriceList->id_taxe = $priceList["id_taxe"] ;
                $quoteLinePriceList->value_taxe = $priceList["value_taxe"] ;
                $quoteLinePriceList->percentage_discount = $priceList["percentage_discount"] ;

                $quoteLinePriceList->save() ;
            }
        }





        // save sub line
        if (isset($data["sublines"]) && count($data["sublines"])) {
            foreach ($data["sublines"] as $dataSubline) {

                if (isset($dataSubline["id"])) {
                    $key = array_search($dataSubline["id"], $idSublineToDelete);
                    if ($key !== false) {
                        unset($idSublineToDelete[$key]);
                    }
                }

                $this->saveLineData($dataSubline, $data["id_quote"], $quoteLine->id);
            }
        }

        if (count($idSublineToDelete)) {
            foreach ($idSublineToDelete as $idToDelete) {
                QuoteLines::deleteLine($idToDelete);
            }
        }


        return $quoteLine->id ;
    }



    public function updateLinePosition()
    {
        // constitution du tableau
        $data = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data)) {
            $line = QuoteLines::where("id", $data['id'])->first();

            QuoteLines::updateOldTable($line->id_quote, $data['oldSort']);
            QuoteLines::updateNewTable($line->id_quote, $data['sort']);

            $QuoteLine = QuoteLines::where("id", $data["id"])->first();
            if ($QuoteLine) {
                $QuoteLine->sort = $data['sort'];
            }
            $QuoteLine->save();
        }

        echo json_encode($data['id']);
    }

    public function deleteLine(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            $line = QuoteLines::where("id", $id)->first();
            QuoteLines::updateOldTable($line->id_quote, $line->sort);

            echo json_encode($line->delete());
        }
    }

    public function activity()
    {
        // constitution du tableau
        $data = array();

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

    public function del_activity(Request $request)
    {
        $id = $request->input('id', 0);

        echo json_encode(QuoteActivities::where("id", $id)->delete());
    }

    public function uploadDocuments(Request $request)
    {
        $id = $request->input('id', 0);

        $objQuoteDocuments = new QuoteDocuments();

        if ($id) {
            $objQuoteDocuments = QuoteDocuments::where('id', $id)->first();

            // Suppression de l'ancien fichier
            if ($objQuoteDocuments && isset($_FILES["file"])) {
                Storage::deleteFile($objQuoteDocuments->path);
            }
        } else {
            $objQuoteDocuments->id_quote = $request->input('idQuote', 0);
        }
        
        $objQuoteDocuments->name = $request->input("name");
        $objQuoteDocuments->description = $request->input("description");
        $objQuoteDocuments->id_user = $request->input("id_user");
        $objQuoteDocuments->user_name = $request->input("user_name");

        if (isset($_FILES["file"])) {
            $objQuoteDocuments->path = Storage::uploadFile($_FILES["file"]);
        }
        $objQuoteDocuments->save();

        echo json_encode($objQuoteDocuments);
    }

    public function del_document(Request $request)
    {
        $id = $request->input('id', 0);

        $objQuoteDocuments = new QuoteDocuments();

        if ($id) {
            $objQuoteDocuments = QuoteDocuments::where('id', $id)->first();
            if ($objQuoteDocuments) {
                Storage::deleteFile($objQuoteDocuments->path);

                $objQuoteDocuments->delete();

                echo "ok" ;
                exit();
            }
        }

        echo "false";
    }


    public function makePDF(Request $request)
    {
        $id = $request->input('id', 0);
        $echo = $request->input('echo', true);


        $data = [];

        $data['text_before_lines'] = Config::find("crm_quote_text_before_lines") ;
        $data['text_after_lines'] = Config::find("crm_quote_text_after_lines") ;


        $data['quote'] = QuotesModel::where("id", $id)->first();
        $data['lines'] = QuoteLines::getFromQuote($id);
        $data['tableTaxes'] = QuoteTaxes::getTableTaxe($id);

        $data['showDiscount'] = false;
        $data['tvas'] = [];
        foreach ($data['lines'] as $line) {
            if (floatval($line->discount) > 0) {
                $data['showDiscount'] = true;
            }
        }

        //load the view and saved it into $html variable
        $html = view("quotes/PDF", $data, BASEPATH . 'App/com_zeapps_crm/views/')->getContent();

        $nomPDF = __t("quote") . '_' . $data['quote']->numerotation . '_' . $data['quote']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');


        //this the the PDF filename that user will get to download
        $pdfFilePath = Storage::getTempFolder() . $nomPDF . '.pdf';

        //set the PDF
        $mpdf = new Mpdf();
        $mpdf->curlAllowUnsafeSslRequests = true;

        //generate the PDF from the given html
        $mpdf->WriteHTML($html);

        //download it.
        $mpdf->Output(BASEPATH . $pdfFilePath, "F");

        if ($echo) {
            echo json_encode($pdfFilePath);
        }

        return $pdfFilePath;
    }
}