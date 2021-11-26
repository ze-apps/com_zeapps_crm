<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use Zeapps\Core\Event;

use Zeapps\Core\Storage;
use Mpdf\Mpdf;

use App\com_zeapps_crm\Models\Order\Orders as OrdersModel;
use App\com_zeapps_crm\Models\Order\OrderLines;
use App\com_zeapps_crm\Models\Order\OrderDocuments;
use App\com_zeapps_crm\Models\Order\OrderActivities;
use App\com_zeapps_crm\Models\Order\OrderLinePriceList;
use App\com_zeapps_crm\Models\CreditBalances;
use App\com_zeapps_crm\Models\CreditBalanceDetails;
use App\com_zeapps_crm\Models\Order\OrderTaxes;

use App\com_zeapps_crm\Models\Invoice\Invoices as InvoicesModel;
use App\com_zeapps_crm\Models\Quote\Quotes as QuotesModel;
use App\com_zeapps_crm\Models\Delivery\Deliveries as DeliveriesModel;

use App\com_zeapps_crm\Models\DocumentRelated;

use Zeapps\Models\Config;

class Orders extends Controller
{
    public function lists()
    {
        $data = array();
        return view("orders/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function view()
    {
        $data = array();
        return view("orders/view", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_line()
    {
        $data = array();
        return view("orders/form_line", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function lists_partial()
    {
        $data = array();
        return view("orders/lists_partial", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_modal()
    {
        $data = array();
        return view("orders/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
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

        $result = OrdersModel::parseFormat($format, $num);

        echo json_encode($result);
    }


    public function get(Request $request)
    {
        $id = $request->input('id', 0);

        $order = OrdersModel::where('id', $id)->first();
        $lines = OrderLines::getFromOrder($id);
        $tableTaxes = OrderTaxes::getTableTaxe($id);

        $documents = OrderDocuments::where('id_order', $id)->get();
        $activities = OrderActivities::where('id_order', $id)->get();

        if ($order->id_company) {
            $credits = CreditBalances::where('id_company', $order->id_company)
                ->where('left_to_pay', '>=', 0.01)
                ->get();
        } else {
            $credits = CreditBalances::where('id_contact', $order->id_contact)
                ->where('left_to_pay', '>=', 0.01)
                ->get();
        }

        echo json_encode(array(
            'order' => $order,
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


        $orders_rs = OrdersModel::select("com_zeapps_crm_orders.*")
            ->groupBy('com_zeapps_crm_orders.id')
            ->orderBy('com_zeapps_crm_orders.date_creation', 'DESC')
            ->orderBy('com_zeapps_crm_orders.id', 'DESC');
        foreach ($filters as $key => $value) {
            if ($key == "id_account_family") {
                $orders_rs = $orders_rs->join('com_zeapps_contact_companies', 'com_zeapps_contact_companies.id', '=', 'com_zeapps_crm_orders.id_company');
                $orders_rs = $orders_rs->where("com_zeapps_contact_companies.id_account_family", $value);

            } elseif (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $orders_rs = $orders_rs->where("com_zeapps_crm_orders." . $key, 'like', '%' . $value . '%');
            } elseif (strpos($key, " ") !== false) {
                $tabKey = explode(" ", $key);
                $orders_rs = $orders_rs->where("com_zeapps_crm_orders." . $tabKey[0], $tabKey[1], $value);
            } else {
                $orders_rs = $orders_rs->where("com_zeapps_crm_orders." . $key, $value);
            }
        }

        $total = $orders_rs->count();
        $orders_rs_id = $orders_rs;

        $orders = $orders_rs->limit($limit)->offset($offset)->get();;


        if (!$orders) {
            $orders = [];
        }


        $ids = [];
        if ($total < 500) {
            $rows = $orders_rs_id->select(array("com_zeapps_crm_orders.id"))->get();
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


    public function modal(Request $request)
    {
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);


        $filters = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }


        $orders_rs = OrdersModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC');
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $orders_rs = $orders_rs->where($key, 'like', '%' . $value . '%');
            } else {
                $orders_rs = $orders_rs->where($key, $value);
            }
        }

        $total = $orders_rs->count();
        $orders_rs_id = $orders_rs;

        $orders = $orders_rs->limit($limit)->offset($offset)->get();;


        if (!$orders) {
            $orders = [];
        }

        echo json_encode(array(
            'data' => $orders,
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


        $order = new OrdersModel();

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $order = OrdersModel::where('id', $data["id"])->first();
        }

        foreach ($data as $key => $value) {
            $order->$key = $value;
        }

        $order->save();

        echo json_encode($order->id);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            $order = OrdersModel::where("id", $id)->first() ;
            if ($order) {
                if ($order->finalized != 1) {
                    OrderLines::where('id_order', $id)->delete();

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

            if ($src = OrdersModel::where("id", $id)->first()) {
                $src->lines = OrderLines::getFromOrder($id);

                if ($data) {
                    foreach ($data as $document => $value) {
                        if ($value == 'true') {
                            $idTo = 0 ;

                            if ($document == "quotes") {
                                $return->quotes = QuotesModel::createFrom($src, OrdersModel::class);
                                $idTo = $return->quotes["id"];

                            } elseif ($document == "orders") {
                                $return->orders = OrdersModel::createFrom($src, OrdersModel::class);
                                $idTo = $return->orders["id"];

                            } elseif ($document == "invoices") {
                                $return->invoices = InvoicesModel::createFrom($src, OrdersModel::class);
                                $idTo = $return->invoices["id"];

                            } elseif ($document == "deliveries") {
                                $return->deliveries = DeliveriesModel::createFrom($src, OrdersModel::class);
                                $idTo = $return->deliveries["id"];
                            }

                            $objDocumentRelated = new DocumentRelated() ;
                            $objDocumentRelated->type_document_from = "orders" ;
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
            if ($order = OrdersModel::where("id", $id)->first()) {
                /*if ($invoice->id_modality === '0') {
                    echo json_encode(array('error' => 'Modalité de paiement non renseignée'));
                    return;
                }*/

                $order->finalized = 1;
                $order->save();

                echo json_encode(array(
                    'numerotation' => $order->numerotation
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
        $id_order_line = 0;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }


        if (isset($data)) {
            $id_order_line = $this->saveLineData($data, $data["id_order"], 0);
        }

        echo json_encode($id_order_line);
    }

    private function saveLineData($data, $id_order, $id_parent)
    {
        $idSublineToDelete = array();


        $orderLine = new OrderLines();

        if (isset($data["id"]) && is_numeric($data["id"])) {
            // load subline to check if need to delete
            $sublines = OrderLines::where("id_parent", $data["id"])->get();

            foreach ($sublines as $subline) {
                $idSublineToDelete[] = $subline->id;
            }


            // load line
            $orderLine = OrderLines::where('id', $data["id"])->first();
        }

        if (!isset($data["id_order"])) {
            $data["id_order"] = $id_order;
        }


        foreach ($data as $key => $value) {
            $orderLine->$key = $value;
        }


        // set id parent line
        $orderLine->id_parent = $id_parent;


        if (!isset($orderLine->accounting_number)) {
            $orderLine->accounting_number = "";
        }

        $orderLine->save();


        // save price list
        if (isset($data["priceList"]) && count($data["priceList"])) {
            foreach ($data["priceList"] as $priceList) {

                $orderLinePriceList = OrderLinePriceList::where("id_order_line", $orderLine->id)->where("id_price_list", $priceList["id_price_list"])->first();

                if (!$orderLinePriceList) {
                    $orderLinePriceList = new OrderLinePriceList();
                    $orderLinePriceList->id_order_line = $orderLine->id;
                    $orderLinePriceList->id_price_list = $priceList["id_price_list"];
                }
                $orderLinePriceList->accounting_number = $priceList["accounting_number"];
                $orderLinePriceList->price_ht = $priceList["price_ht"];
                $orderLinePriceList->price_ttc = $priceList["price_ttc"];
                $orderLinePriceList->id_taxe = $priceList["id_taxe"];
                $orderLinePriceList->value_taxe = $priceList["value_taxe"];
                $orderLinePriceList->percentage_discount = $priceList["percentage_discount"];

                $orderLinePriceList->save();
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

                $this->saveLineData($dataSubline, $data["id_order"], $orderLine->id);
            }
        }

        if (count($idSublineToDelete)) {
            foreach ($idSublineToDelete as $idToDelete) {
                OrderLines::deleteLine($idToDelete);
            }
        }


        return $orderLine->id;
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
            $line = OrderLines::where("id", $data['id'])->first();

            OrderLines::updateOldTable($line->id_order, $data['oldSort']);
            OrderLines::updateNewTable($line->id_order, $data['sort']);

            $OrderLine = OrderLines::where("id", $data["id"])->first();
            if ($OrderLine) {
                $OrderLine->sort = $data['sort'];
            }
            $OrderLine->save();
        }

        echo json_encode($data['id']);
    }

    public function deleteLine(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            $line = OrderLines::where("id", $id)->first();
            OrderLines::updateOldTable($line->id_order, $line->sort);

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

    public function del_activity(Request $request)
    {
        $id = $request->input('id', 0);

        echo json_encode(OrderActivities::where("id", $id)->delete());
    }


    public function uploadDocuments(Request $request)
    {
        $id = $request->input('id', 0);

        $objOrderDocuments = new OrderDocuments();

        if ($id) {
            $objOrderDocuments = OrderDocuments::where('id', $id)->first();

            // Suppression de l'ancien fichier
            if ($objOrderDocuments && isset($_FILES["file"])) {
                Storage::deleteFile($objOrderDocuments->path);
            }
        } else {
            $objOrderDocuments->id_order = $request->input('idOrder', 0);
        }
        
        $objOrderDocuments->name = $request->input("name");
        $objOrderDocuments->description = $request->input("description");
        $objOrderDocuments->id_user = $request->input("id_user");
        $objOrderDocuments->user_name = $request->input("user_name");

        if (isset($_FILES["file"])) {
            $objOrderDocuments->path = Storage::uploadFile($_FILES["file"]);
        }
        $objOrderDocuments->save();

        echo json_encode($objOrderDocuments);
    }

    public function del_document(Request $request)
    {
        $id = $request->input('id', 0);

        $objOrderDocuments = new OrderDocuments();

        if ($id) {
            $objOrderDocuments = OrderDocuments::where('id', $id)->first();
            if ($objOrderDocuments) {
                Storage::deleteFile($objOrderDocuments->path);

                $objOrderDocuments->delete();

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

        $data['text_before_lines'] = Config::find("crm_order_text_before_lines") ;
        $data['text_after_lines'] = Config::find("crm_order_text_after_lines") ;

        $data['order'] = OrdersModel::where("id", $id)->first();
        $data['lines'] = OrderLines::getFromOrder($id);
        $data['tableTaxes'] = OrderTaxes::getTableTaxe($id);

        $data['showDiscount'] = false;
        $data['tvas'] = [];
        foreach ($data['lines'] as $line) {
            if (floatval($line->discount) > 0) {
                $data['showDiscount'] = true;
            }
        }

        //load the view and saved it into $html variable
        $html = view("orders/PDF", $data, BASEPATH . 'App/com_zeapps_crm/views/')->getContent();

        $nomPDF = $data['order']->name_company . '_' . $data['order']->numerotation . '_' . $data['order']->libelle;
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