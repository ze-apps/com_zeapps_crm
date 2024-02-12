<?php

namespace App\com_zeapps_crm\Controllers;

use App\com_zeapps_crm\Models\AccountingEntries;
use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;
use Zeapps\Core\Storage;

use Zeapps\Core\Event;

use Zeapps\libraries\XLSXWriter;

use App\com_zeapps_crm\Models\Invoice\Invoices as InvoicesModel;
use App\com_zeapps_crm\Models\Invoice\InvoiceLines;
use App\com_zeapps_crm\Models\Invoice\InvoiceDocuments;
use App\com_zeapps_crm\Models\Invoice\InvoiceActivities;
use App\com_zeapps_crm\Models\Invoice\InvoiceLinePriceList;
use App\com_zeapps_crm\Models\Invoice\InvoiceTaxes;
use App\com_zeapps_crm\Models\CreditBalances;
use App\com_zeapps_crm\Models\CreditBalanceDetails;
use App\com_zeapps_contact\Models\Modalities;

use App\com_zeapps_crm\Models\Order\Orders as OrdersModel;
use App\com_zeapps_crm\Models\Quote\Quotes as QuotesModel;
use App\com_zeapps_crm\Models\Delivery\Deliveries as DeliveriesModel;

use App\com_zeapps_crm\Models\DocumentRelated;




use Zeapps\Models\Config;

class Invoices extends Controller
{
    public function lists()
    {
        $data = array();
        return view("invoices/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function view()
    {
        $data = array();
        return view("invoices/view", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_line()
    {
        $data = array();
        return view("invoices/form_line", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function lists_partial()
    {
        $data = array();
        return view("invoices/lists_partial", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_modal()
    {
        $data = array();
        return view("invoices/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
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

        $result = InvoicesModel::parseFormat($format, $num);

        echo json_encode($result);
    }


    public function get(Request $request)
    {

        $id = $request->input('id', 0);

        $invoice = InvoicesModel::where('id', $id)->first();
        $lines = InvoiceLines::getFromInvoice($id);
        $tableTaxes = InvoiceTaxes::getTableTaxe($id);

        $documents = InvoiceDocuments::where('id_invoice', $id)->get();
        $activities = InvoiceActivities::where('id_invoice', $id)->get();

        if ($invoice->id_company) {
            $credits = CreditBalances::where('id_company', $invoice->id_company)
                ->where('left_to_pay', '>=', 0.01)
                ->get();
        } else {
            $credits = CreditBalances::where('id_contact', $invoice->id_contact)
                ->where('left_to_pay', '>=', 0.01)
                ->get();
        }

        echo json_encode(array(
            'invoice' => $invoice,
            'lines' => $lines,
            'tableTaxes' => $tableTaxes,
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


        $invoices_rs = InvoicesModel::select("com_zeapps_crm_invoices.*")
            /*->groupBy('com_zeapps_crm_invoices.id') // Attention si on active ça, bug avec le count pour le total*/
            ->orderBy('com_zeapps_crm_invoices.date_creation', 'DESC')
            ->orderBy('com_zeapps_crm_invoices.id', 'DESC');
        foreach ($filters as $key => $value) {
            if ($key == "id_account_family") {
                $invoices_rs = $invoices_rs->join('com_zeapps_contact_companies', 'com_zeapps_contact_companies.id', '=', 'com_zeapps_crm_invoices.id_company');
                $invoices_rs = $invoices_rs->where("com_zeapps_contact_companies.id_account_family", $value);

            } elseif ($key == "unpaid") {
                if ($value) {
                    $invoices_rs = $invoices_rs->where("com_zeapps_crm_invoices.finalized", 1);
                    $invoices_rs = $invoices_rs->where("com_zeapps_crm_invoices.due", "!=", 0);
                    $invoices_rs = $invoices_rs->where("com_zeapps_crm_invoices.date_limit", "<", date("Y-m-d H:i:s"));
                }
            } elseif (strpos($key, " LIKE") !== false) {
                $key = str_replace(" LIKE", "", $key);
                $invoices_rs = $invoices_rs->where("com_zeapps_crm_invoices." . $key, 'like', '%' . $value . '%');
            } elseif (strpos($key, " ") !== false) {
                $tabKey = explode(" ", $key);
                $invoices_rs = $invoices_rs->where("com_zeapps_crm_invoices." . $tabKey[0], $tabKey[1], $value);
            } else {
                $invoices_rs = $invoices_rs->where("com_zeapps_crm_invoices." . $key, $value);
            }
        }

        $total = $invoices_rs->count();

        $invoices_rs_id = $invoices_rs;

        $invoices = $invoices_rs->limit($limit)->offset($offset)->get();;


        if (!$invoices) {
            $invoices = [];
        }


        $ids = [];
        if ($total < 500) {
            $rows = $invoices_rs_id->select(array("com_zeapps_crm_invoices.id"))->get();
            foreach ($rows as $row) {
                array_push($ids, $row->id);
            }
        }

        echo json_encode(array(
            'invoices' => $invoices,
            'total' => $total,
            'ids' => $ids
        ));
    }


    public function export(Request $request)
    {
        $id = $request->input('id', 0);
        $type = $request->input('type', 'company');
        $offset = $request->input('offset', 0);


        $filters = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if ($id != 0) {
            $filters['id_' . $type] = $id;
        }


        $invoices_rs = InvoicesModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC');
        foreach ($filters as $key => $value) {
            if ($key == "unpaid") {
                if ($value) {
                    $invoices_rs = $invoices_rs->where("finalized", 1);
                    $invoices_rs = $invoices_rs->where("due", "!=", 0);
                    $invoices_rs = $invoices_rs->where("date_limit", "<", date("Y-m-d H:i:s"));
                }
            } elseif (strpos($key, " LIKE") !== false) {
                $key = str_replace(" LIKE", "", $key);
                $invoices_rs = $invoices_rs->where($key, 'like', '%' . $value . '%');
            } elseif (strpos($key, " ") !== false) {
                $tabKey = explode(" ", $key);
                $invoices_rs = $invoices_rs->where($tabKey[0], $tabKey[1], $value);
            } else {
                $invoices_rs = $invoices_rs->where($key, $value);
            }
        }

        $total = $invoices_rs->count();
        $invoices_rs_id = $invoices_rs;

        $invoices = $invoices_rs->get();


        if (!$invoices) {
            $invoices = [];
        }


        


        // génération du fichier Excel
        $header = array("string");

        $row1 = array(
            __t("#"), 
            __t("Creation date"), 
            __t("Recipient"), 
            __t("Label"), 
            __t("Total duty"), 
            __t("Total All taxes included"),
            __t("Balance"),
            __t("Deadline"),
            __t("Manager"),
            __t("Status"),
            __t("Terms of Payment")
        );
    
        $writer = new XLSXWriter();

        $this->sheet_name = 'stock';

        $writer->writeSheetHeader($this->sheet_name, $header, $suppress_header_row = true);

        // Formatage
        $format = array('font' => 'Arial',
            'font-size' => 10,
            'font-style' => 'bold,italic',
            'border' => 'top, right, left, bottom',
            'color' => '#000',
            'halign' => 'center');

        $writer->writeSheetRow($this->sheet_name, $row1, $format);

        foreach ($invoices as $invoice) {
            $row3 = array(
                $invoice->numerotation,
                date("d/m/Y", strtotime($invoice->date_creation)),
                $invoice->name_company . ($invoice->name_company != "" && $invoice->name_contact != "" ? "-":"") . $invoice->name_contact,
                $invoice->libelle,
                $invoice->total_ht*1,
                $invoice->total_ttc*1,
                $invoice->due,
                date("d/m/Y", strtotime($invoice->date_limit)),
                $invoice->name_user_account_manager,
                $invoice->finalized ? __t("Closed") : __t("Open"),
                $invoice->label_modality,
            );

            // Formatage
            $format = array();
            $writer->writeSheetRow($this->sheet_name, $row3, $format);
        }

        // Gnérer une url temporaire unique pour le fichier Excel
        // $link = BASEPATH . 'tmp/invoices_' . Storage::generateRandomString() . '.xlsx';
        $link = Storage::getTempFolder() . 'invoices_' . Storage::generateRandomString() . '.xlsx';
        $writer->writeToFile(BASEPATH . $link);


        echo json_encode(array(
            'link' => $link
        ));
    }




    public function due(Request $request) {
        $type_client = $request->input('type_client', '');
        $id_client = $request->input('id_client', 0);

        if ($type_client == "company" || $type_client == "contact") {
            $invoices = InvoicesModel::where("id_" . $type_client, $id_client)
                ->where("due", "!=", 0)
                ->orderBy("date_creation", "ASC")
                ->get();
            echo json_encode($invoices);
        }
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


        $invoices_rs = InvoicesModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC');
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $invoices_rs = $invoices_rs->where($key, 'like', '%' . $value . '%');
            } else {
                $invoices_rs = $invoices_rs->where($key, $value);
            }
        }

        $total = $invoices_rs->count();
        $invoices_rs_id = $invoices_rs;

        $invoices = $invoices_rs->limit($limit)->offset($offset)->get();;


        if (!$invoices) {
            $invoices = [];
        }

        echo json_encode(array(
            'data' => $invoices,
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


        $invoice = new InvoicesModel();
        $createNumber = true;

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $invoice = InvoicesModel::where('id', $data["id"])->first();
            if ($invoice) {
                $createNumber = false;
            }
        }

        foreach ($data as $key => $value) {
            $invoice->$key = $value;
        }

        $invoice->save();

        echo json_encode($invoice->id);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            $invoice = InvoicesModel::where("id", $id)->first() ;
            if ($invoice) {
                if ($invoice->finalized != 1) {
                    InvoiceLines::where('id_invoice', $id)->delete();

                    $documents = InvoiceDocuments::where('id_invoice', $id)->get();

                    $path = BASEPATH;

                    if ($documents && is_array($documents)) {
                        for ($i = 0; $i < sizeof($documents); $i++) {
                            unlink($path . $documents[$i]->path);
                        }
                    }

                    InvoiceDocuments::where('id_invoice', $id)->delete();

                    echo json_encode(InvoicesModel::where("id", $id)->delete());
                } else {
                    echo json_encode("ERROR");
                }
            } else {
                echo json_encode("ERROR");
            }
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

            if ($src = InvoicesModel::where("id", $id)->first()) {
                $src->lines = InvoiceLines::getFromInvoice($id) ;

                if ($data) {
                    foreach ($data as $document => $value) {
                        if ($value == 'true') {
                            $idTo = 0 ;

                            if ($document == "quotes") {
                                $return->quotes = QuotesModel::createFrom($src, InvoicesModel::class);
                                $idTo = $return->quotes["id"];

                            } elseif ($document == "orders") {
                                $return->orders = OrdersModel::createFrom($src, InvoicesModel::class);
                                $idTo = $return->orders["id"];

                            } elseif ($document == "invoices") {
                                $return->invoices = InvoicesModel::createFrom($src, InvoicesModel::class);
                                $idTo = $return->invoices["id"];

                            } elseif ($document == "deposit_invoices") {
                                $invoicesModel = new InvoicesModel();
                                $ecritures = $invoicesModel->getEcritureComptableSimulate($src);
                                

                                $return->invoices = InvoicesModel::createFrom($src, InvoicesModel::class, false, $data, $ecritures);
                                $idTo = $return->invoices["id"];
                                

                            } elseif ($document == "invoice_with_down_payment_deduction") {
                                $ecritures = [];
                                $data["numero_factures_deduites"] = [] ;
                                // recherche les infos sur l'ensemble des factures
                                if (isset($data["invoicesSelected"])) {
                                    foreach ($data["invoicesSelected"] as $idInvoice) {
                                        $invoice = InvoicesModel::find($idInvoice);
                                        if ($invoice) {
                                            $data["numero_factures_deduites"][] = $invoice->numerotation;

                                            $ecrituresInvoice = $invoice->getEcritureComptableSimulate($invoice);

                                            $ecritures = $invoice->fuisionTableTaxe($ecritures, $ecrituresInvoice);
                                        }
                                    }
                                }

                                $return->invoices = InvoicesModel::createFrom($src, InvoicesModel::class, false, $data, $ecritures);
                                $idTo = $return->invoices["id"];

                            } elseif ($document == "credit") {
                                $return->invoices = InvoicesModel::createFrom($src,InvoicesModel::class, true);
                                $idTo = $return->invoices["id"];

                            } elseif ($document == "deliveries") {
                                $return->deliveries = DeliveriesModel::createFrom($src, InvoicesModel::class);
                                $idTo = $return->deliveries["id"];
                            }

                            // les acomptes et factures avec déduction d'acompte sont des factures
                            if ($document == "deposit_invoices" || $document == "invoice_with_down_payment_deduction") {
                                $document = "invoices";
                            }

                            $objDocumentRelated = new DocumentRelated() ;
                            $objDocumentRelated->type_document_from = "invoices" ;
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


    public function finalize(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            if ($invoice = InvoicesModel::where("id", $id)->first()) {
                if ($invoice->id_modality === '0') {
                    echo json_encode(array('error' => 'Modalité de paiement non renseignée'));
                    return;
                }

                $invoice->finalized = 1;
                $invoice->save();

                echo json_encode(array(
                    'numerotation' => $invoice->numerotation,
                    'final_pdf' => $invoice->final_pdf
                ));
            } else {
                echo json_encode(false);
            }
        } else {
            echo json_encode(false);
        }
    }


    public function saveLine()
    {
        // constitution du tableau
        $data = array();
        $id_invoice_line = 0;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }


        if (isset($data)) {
            $id_invoice_line = $this->saveLineData($data, $data["id_invoice"], 0);
        }

        echo json_encode($id_invoice_line);
    }

    private function saveLineData($data, $id_invoice, $id_parent) {

        $idSublineToDelete = array() ;


        $invoiceLine = new InvoiceLines();

        if (isset($data["id"]) && is_numeric($data["id"])) {
            // load subline to check if need to delete
            $sublines = InvoiceLines::where("id_parent", $data["id"])->get() ;

            foreach ($sublines as $subline) {
                $idSublineToDelete[] = $subline->id ;
            }


            // load line
            $invoiceLine = InvoiceLines::where('id', $data["id"])->first();
        }

        if (!isset($data["id_invoice"])) {
            $data["id_invoice"] = $id_invoice ;
        }


        foreach ($data as $key => $value) {
            $invoiceLine->$key = $value;
        }


        // set id parent line
        $invoiceLine->id_parent = $id_parent ;



        if (!isset($invoiceLine->accounting_number)) {
            $invoiceLine->accounting_number = "";
        }


        $invoiceLine->save();



        // save price list
        if (isset($data["priceList"]) && count($data["priceList"])) {
            foreach ($data["priceList"] as $priceList) {

                $invoiceLinePriceList = InvoiceLinePriceList::where("id_invoice_line", $invoiceLine->id)->where("id_price_list", $priceList["id_price_list"])->first();

                if (!$invoiceLinePriceList) {
                    $invoiceLinePriceList = new InvoiceLinePriceList() ;
                    $invoiceLinePriceList->id_invoice_line = $invoiceLine->id ;
                    $invoiceLinePriceList->id_price_list = $priceList["id_price_list"] ;
                }
                $invoiceLinePriceList->accounting_number = $priceList["accounting_number"] ;
                $invoiceLinePriceList->price_ht = $priceList["price_ht"] ;
                $invoiceLinePriceList->price_ttc = $priceList["price_ttc"] ;
                $invoiceLinePriceList->id_taxe = $priceList["id_taxe"] ;
                $invoiceLinePriceList->value_taxe = $priceList["value_taxe"] ;
                $invoiceLinePriceList->percentage_discount = $priceList["percentage_discount"] ;

                $invoiceLinePriceList->save() ;
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

                $this->saveLineData($dataSubline, $data["id_invoice"], $invoiceLine->id);
            }
        }

        if (count($idSublineToDelete)) {
            foreach ($idSublineToDelete as $idToDelete) {
                InvoiceLines::deleteLine($idToDelete);
            }
        }


        return $invoiceLine->id ;
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
            $line = InvoiceLines::where("id", $data['id'])->first();

            InvoiceLines::updateOldTable($line->id_invoice, $data['oldSort']);
            InvoiceLines::updateNewTable($line->id_invoice, $data['sort']);

            $InvoiceLine = InvoiceLines::where("id", $data["id"])->first();
            if ($InvoiceLine) {
                $InvoiceLine->sort = $data['sort'];
            }
            $InvoiceLine->save();
        }

        echo json_encode($data['id']);
    }

    public function deleteLine(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            $line = InvoiceLines::where("id", $id)->first();
            InvoiceLines::updateOldTable($line->id_invoice, $line->sort);

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
            $invoiceActivities = new InvoiceActivities();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $invoiceActivities = InvoiceActivities::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $invoiceActivities->$key = $value;
            }

            $invoiceActivities->save();

            echo json_encode(InvoiceActivities::where("id", $invoiceActivities->id)->first());
        }
    }

    public function del_activity(Request $request)
    {
        $id = $request->input('id', 0);

        echo json_encode(InvoiceActivities::where("id", $id)->delete());
    }

    public function uploadDocuments(Request $request)
    {
        $id = $request->input('id', 0);

        $objInvoiceDocuments = new InvoiceDocuments();

        if ($id) {
            $objInvoiceDocuments = InvoiceDocuments::where('id', $id)->first();

            // Suppression de l'ancien fichier
            if ($objInvoiceDocuments && isset($_FILES["file"])) {
                Storage::deleteFile($objInvoiceDocuments->path);
            }
        } else {
            $objInvoiceDocuments->id_invoice = $request->input('idInvoice', 0);
        }
        
        $objInvoiceDocuments->name = $request->input("name");
        $objInvoiceDocuments->description = $request->input("description");
        $objInvoiceDocuments->id_user = $request->input("id_user");
        $objInvoiceDocuments->user_name = $request->input("user_name");

        if (isset($_FILES["file"])) {
            $objInvoiceDocuments->path = Storage::uploadFile($_FILES["file"]);
        }
        $objInvoiceDocuments->save();

        echo json_encode($objInvoiceDocuments);
    }

    public function del_document(Request $request)
    {
        $id = $request->input('id', 0);

        $objInvoiceDocuments = new InvoiceDocuments();

        if ($id) {
            $objInvoiceDocuments = InvoiceDocuments::where('id', $id)->first();
            if ($objInvoiceDocuments) {
                Storage::deleteFile($objInvoiceDocuments->path);

                $objInvoiceDocuments->delete();

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


        $pdfFilePath = InvoicesModel::makePDF($id, $echo);

        if ($echo) {
            echo json_encode($pdfFilePath);
        }

        return $pdfFilePath;
    }


    public function checkquiltmania() {
        $ecritures = [] ;

        $date_debut = "2014-11-01" ;
        $date_fin = "2015-10-31" ;

        $offset = 0 ;
        $limit = 15 ;
        $invoices = InvoicesModel::where("date_creation", ">=", $date_debut)->where("date_creation", "<=", $date_fin)->where("finalized", 1)->where("deleted_at", null)->limit($limit)->offset($offset)->get();
        while(count($invoices)) {
            foreach ($invoices as $invoice) {
                $objInvoicesModel = new InvoicesModel();
                $ecritures = $objInvoicesModel->fuisionTableTaxe($ecritures, $objInvoicesModel->getEcritureComptableSimulate($invoice));
            }

            $offset += $limit ;
            $invoices = InvoicesModel::where("date_creation", ">=", $date_debut)->where("date_creation", "<=", $date_fin)->limit($limit)->offset($offset)->get();
//            break;
        }


        $ecritureProduit = array();
        $ecritureTVA = array();

        foreach ($ecritures as $ecriture) {
            $numCompte = $this->addZeroEnd($ecriture["accounting_number"]) ;
            if (!isset($ecritureProduit[$numCompte])) {
                $ecritureProduit[$numCompte] = $ecriture["total_ht"] * 1 ;
            } else {
                $ecritureProduit[$numCompte] += $ecriture["total_ht"] * 1 ;
            }


            $numCompteTva = $this->addZeroEnd($ecriture["accounting_number_taxe"]) ;
            if (!isset($ecritureTVA[$numCompteTva])) {
                $ecritureTVA[$numCompteTva] = 0;
            }
            $ecritureTVA[$numCompteTva] += $ecriture["amount_tva"] * 1;
        }



        echo "<style>body{font-family: arial;}</style>";
        echo "<h1>Analyse " . date("d/m/Y", strtotime($date_debut)) . " au " . date("d/m/Y", strtotime($date_fin)) . "</h1>";
        echo "<h2>Compte produit</h2>";
        echo "<table border='1' cellspacing='0' cellpadding='3'>";
        ksort($ecritureProduit);
        $total = 0 ;
        foreach ($ecritureProduit as $key => $ecriture) {
            if ($ecriture != 0) {
                echo "<tr><td>$key</td><td style='text-align: right;'>" . number_format($ecriture, 2, ".", " ") . "</td></tr>";
                $total += $ecriture ;
            }
        }
        echo "<tr><td><b>Total</b></td><td style='text-align: right;'><b>" . number_format($total, 2, ".", " ") . "</b></td></tr>";
        echo "</table>";
        echo "<br>" ;

        echo "<h2>Compte TVA</h2>";
        echo "<table border='1' cellspacing='0' cellpadding='3'>";
        ksort($ecritureTVA);
        $total = 0 ;
        foreach ($ecritureTVA as $key => $ecriture) {
            if ($ecriture != 0) {
                echo "<tr><td>$key</td><td style='text-align: right;'>" . number_format($ecriture, 2, ".", " ") . "</td></tr>";
                $total += $ecriture ;
            }
        }
        echo "<tr><td><b>Total</b></td><td style='text-align: right;'><b>" . number_format($total, 2, ".", " ") . "</b></td></tr>";
        echo "</table>";
    }
    private function addZeroEnd($numeroCompte) {
        $numeroCompte = $numeroCompte . "";
        if (strlen($numeroCompte) < 10) {
            for ($i = strlen($numeroCompte); $i <= 10; $i++) {
                $numeroCompte .= "0";
            }
        }
        return $numeroCompte ;
    }
}