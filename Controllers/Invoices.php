<?php

namespace App\com_zeapps_crm\Controllers;

use App\com_zeapps_crm\Models\AccountingEntries;
use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;


use App\com_zeapps_crm\Models\Invoices as InvoicesModel;
use App\com_zeapps_crm\Models\InvoiceLines;
use App\com_zeapps_crm\Models\InvoiceDocuments;
use App\com_zeapps_crm\Models\InvoiceActivities;
use App\com_zeapps_crm\Models\CreditBalances;
use App\com_zeapps_crm\Models\CreditBalanceDetails;
use App\com_zeapps_contact\Models\Modalities;

use App\com_zeapps_crm\Models\Orders as OrdersModel;
use App\com_zeapps_crm\Models\Quotes as QuotesModel;
use App\com_zeapps_crm\Models\Deliveries as DeliveriesModel;

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


        $ids = [];
        if ($total < 500) {
            $rows = $invoices_rs_id->select(array("id"))->get();
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

            if ($src = InvoicesModel::where("id", $id)->first()) {
                $src->lines = InvoiceLines::getFromInvoice($id) ;

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

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }


        if (isset($data)) {
            $invoiceLine = new InvoiceLines();

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $invoiceLine = InvoiceLines::where('id', $data["id"])->first();
            }

            foreach ($data as $key => $value) {
                $invoiceLine->$key = $value;
            }

            if (!isset($invoiceLine->accounting_number)) {
                $invoiceLine->accounting_number = "";
            }


            $invoiceLine->save();
        }

        echo json_encode($invoiceLine->id);
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


}