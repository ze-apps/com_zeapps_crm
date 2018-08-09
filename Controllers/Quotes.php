<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use Zeapps\Core\Storage;
use Mpdf\Mpdf;

use App\com_zeapps_crm\Models\Quotes as QuotesModel;
use App\com_zeapps_crm\Models\QuoteLines;
use App\com_zeapps_crm\Models\QuoteLineDetails;
use App\com_zeapps_crm\Models\QuoteDocuments;
use App\com_zeapps_crm\Models\QuoteActivities;
use App\com_zeapps_crm\Models\CreditBalances;
use App\com_zeapps_crm\Models\CreditBalanceDetails;

use App\com_zeapps_crm\Models\Invoices as InvoicesModel;
use App\com_zeapps_crm\Models\Orders as OrdersModel;
use App\com_zeapps_crm\Models\Deliveries as DeliveriesModel;

use Zeapps\Models\Config;

use Zeapps\Core\Mail;

class Quotes extends Controller
{
    public function lists()
    {
        $data = array();
        return view("quotes/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }


    public function sendEmail() {
        $data = array();
        return view("quotes/send_email", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function sendEmailPost() {
        // constitution du tableau
        $data = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }





        $html = "<html><body>" . nl2br($data["content"]) . "</body></html>";
        $text = $data["content"];
        $sender = array() ;//array("name" => "Nicolas Ramel", "email" => "nicolas.ramel@preview-communication.fr");


        $data["to"] = str_replace(";", ",", $data["to"]);
        $tos = explode(",", $data["to"]);


        $to = array() ;//array(array("name" => "Nicolas Ramel", "email" => "nicolas.ramel@preview-communication.fr"));
        foreach ($tos as $to_data) {
            $to_data = trim($to_data) ;
            if (filter_var($to_data, FILTER_VALIDATE_EMAIL)) {
                $to[] = array("email" => $to_data) ;
            }
        }


        $cc = array();
        $bcc = array();



        $attachment = array();
        if (isset($data["attachments"]) && is_array($data["attachments"])) {
            foreach ($data["attachments"] as $attach) {
                $attachment[] = array(
                    'content' => Storage::getFileBase64($attach["file"]),
                    'name' => $attach["name"]
                );
            }
        }


        $quote = QuotesModel::where("id", $data["id"])->first();



        $emailModule = array();
        if ($quote->id_contact) {
            $emailModule[] = array("module" => "com_zeapps_contact", "id" => "contacts_" . $quote->id_contact);
        }

        if ($quote->id_company) {
            $emailModule[] = array("module" => "com_zeapps_contact", "id" => "compagnies_" . $quote->id_company);
        }

        $emailModule[] = array("module" => "com_zeapps_crm", "id" => "quotes_" . $data["id"]) ;


        Mail::send($data["subject"],
            $html,
            $text,
            $sender,
            $to,
            $bcc, // Bcc
            $cc, // Cc
            $attachment, // Attachment
            -1, // $id_user_account
            $emailModule
        );

        echo json_encode("ok");
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

        $lines = QuoteLines::orderBy('sort')->where('id_quote', $id)->get();
        $line_details = QuoteLineDetails::where('id_quote', $id)->get();
        $documents = QuoteDocuments::where('id_quote', $id)->get();
        $activities = QuoteActivities::where('id_quote', $id)->get();

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
            'line_details' => $line_details,
            'documents' => $documents,
            'activities' => $activities,
            'credits' => $credits
        ));
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


        $ids = [];
        if ($total < 500) {
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

            $return = [];

            if ($src = QuotesModel::where("id", $id)->first()) {
                $src->lines = QuoteLines::where('id_quote', $id)->get();
                $src->line_details = QuoteLineDetails::where('id_quote', $id)->get();

                if ($data) {
                    foreach ($data as $document => $value) {
                        if ($value == 'true') {
                            if ($document == "quotes") {
                                QuotesModel::createFrom($src);
                            } elseif ($document == "orders") {
                                OrdersModel::createFrom($src);
                            } elseif ($document == "invoices") {
                                InvoicesModel::createFrom($src);
                            } elseif ($document == "deliveries") {
                                DeliveriesModel::createFrom($src);
                            }
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
                $quoteLine->accounting_number = "";
            }


            $quoteLine->save();
        }

        echo json_encode($quoteLine->id);
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
            QuoteLineDetails::where("id_line", $id)->delete();

            echo json_encode($line->delete());

        }
    }

    public function saveLineDetail()
    {
        // constitution du tableau
        $data = array();

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


    public function makePDF(Request $request)
    {
        $id = $request->input('id', 0);
        $echo = $request->input('echo', true);


        $data = [];

        $data['quote'] = QuotesModel::where("id", $id)->first();
        $data['lines'] = QuoteLines::where("id_quote", $id)->orderBy("sort")->get();
        $line_details = QuoteLineDetails::where("id_quote", $id)->get();

        $data['showDiscount'] = false;
        $data['tvas'] = [];
        foreach ($data['lines'] as $line) {
            if (floatval($line->discount) > 0) {
                $data['showDiscount'] = true;
            }

            if ($line->id_taxe !== '0') {
                if (!isset($data['tvas'][$line->id_taxe])) {
                    $data['tvas'][$line->id_taxe] = array(
                        'ht' => 0,
                        'value_taxe' => floatval($line->value_taxe)
                    );
                }

                $data['tvas'][$line->id_taxe]['ht'] += floatval($line->total_ht);
                $data['tvas'][$line->id_taxe]['value'] = round(floatval($data['tvas'][$line->id_taxe]['ht']) * ($data['tvas'][$line->id_taxe]['value_taxe'] / 100), 2);
            }
        }
        foreach ($line_details as $line) {
            if ($line->id_taxe !== '0') {
                if (!isset($data['tvas'][$line->id_taxe])) {
                    $data['tvas'][$line->id_taxe] = array(
                        'ht' => 0,
                        'value_taxe' => floatval($line->value_taxe)
                    );
                }

                $data['tvas'][$line->id_taxe]['ht'] += floatval($line->total_ht);
                $data['tvas'][$line->id_taxe]['value'] = round(floatval($data['tvas'][$line->id_taxe]['ht']) * ($data['tvas'][$line->id_taxe]['value_taxe'] / 100), 2);
            }
        }

        //load the view and saved it into $html variable
        $html = view("quotes/PDF", $data, BASEPATH . 'App/com_zeapps_crm/views/')->getContent();

        $nomPDF = $data['quote']->name_company . '_' . $data['quote']->numerotation . '_' . $data['quote']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');


        //this the the PDF filename that user will get to download
        $pdfFilePath = Storage::getTempFolder() . $nomPDF . '.pdf';

        //set the PDF
        $mpdf = new Mpdf();

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