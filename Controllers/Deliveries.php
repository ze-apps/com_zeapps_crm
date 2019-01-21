<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use Zeapps\Core\Storage;
use Mpdf\Mpdf;

use App\com_zeapps_crm\Models\Delivery\Deliveries as DeliveriesModel;
use App\com_zeapps_crm\Models\Delivery\DeliveryLines;
use App\com_zeapps_crm\Models\Delivery\DeliveryDocuments;
use App\com_zeapps_crm\Models\Delivery\DeliveryActivities;
use App\com_zeapps_crm\Models\Delivery\DeliveryLinePriceList;
use App\com_zeapps_crm\Models\CreditBalances;
use App\com_zeapps_crm\Models\CreditBalanceDetails;

use App\com_zeapps_crm\Models\Order\Orders as OrdersModel;
use App\com_zeapps_crm\Models\Quote\Quotes as QuotesModel;
use App\com_zeapps_crm\Models\Invoice\Invoices as InvoicesModel;

use Zeapps\Models\Config;

class Deliveries extends Controller
{
    public function lists()
    {
        $data = array();
        return view("deliveries/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function view()
    {
        $data = array();
        return view("deliveries/view", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_line()
    {
        $data = array();
        return view("deliveries/form_line", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function lists_partial()
    {
        $data = array();
        return view("deliveries/lists_partial", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_modal()
    {
        $data = array();
        return view("deliveries/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
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

        $result = DeliveriesModel::parseFormat($format, $num);

        echo json_encode($result);
    }


    public function get(Request $request)
    {

        $id = $request->input('id', 0);

        $deliverie = DeliveriesModel::where('id', $id)->first();

        $lines = DeliveryLines::getFromDelivery($id);
        $documents = DeliveryDocuments::where('id_delivery', $id)->get();
        $activities = DeliveryActivities::where('id_delivery', $id)->get();

        if ($deliverie->id_company) {
            $credits = CreditBalances::where('id_company', $deliverie->id_company)
                ->where('left_to_pay', '>=', 0.01)
                ->get();
        } else {
            $credits = CreditBalances::where('id_contact', $deliverie->id_contact)
                ->where('left_to_pay', '>=', 0.01)
                ->get();
        }

        echo json_encode(array(
            'delivery' => $deliverie,
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


        $deliveries_rs = DeliveriesModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC');
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $deliveries_rs = $deliveries_rs->where($key, 'like', '%' . $value . '%');
            } else {
                $deliveries_rs = $deliveries_rs->where($key, $value);
            }
        }

        $total = $deliveries_rs->count();
        $deliveries_rs_id = $deliveries_rs;

        $deliveries = $deliveries_rs->limit($limit)->offset($offset)->get();;


        if (!$deliveries) {
            $deliveries = [];
        }


        $ids = [];
        if ($total < 500) {
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


    public function modal(Request $request)
    {
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);


        $filters = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }


        $deliveries_rs = DeliveriesModel::orderBy('date_creation', 'DESC')->orderBy('id', 'DESC');
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $deliveries_rs = $deliveries_rs->where($key, 'like', '%' . $value . '%');
            } else {
                $deliveries_rs = $deliveries_rs->where($key, $value);
            }
        }

        $total = $deliveries_rs->count();
        $deliveries_rs_id = $deliveries_rs;

        $deliveries = $deliveries_rs->limit($limit)->offset($offset)->get();;


        if (!$deliveries) {
            $deliveries = [];
        }

        echo json_encode(array(
            'data' => $deliveries,
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


        $deliverie = new DeliveriesModel();

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $deliverie = DeliveriesModel::where('id', $data["id"])->first();
        }

        foreach ($data as $key => $value) {
            $deliverie->$key = $value;
        }


        $deliverie->save();

        echo json_encode($deliverie->id);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            DeliveryLines::where('id_delivery', $id)->delete();

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

            if ($src = DeliveriesModel::where("id", $id)->first()) {
                $src->lines = DeliveryLines::getFromDelivery($id);

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
        $id_delivery_line = 0;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }


        if (isset($data)) {
            $id_delivery_line = $this->saveLineData($data, $data["id_delivery"], 0);
        }

        echo json_encode($id_delivery_line);
    }

    private function saveLineData($data, $id_delivery, $id_parent)
    {

        $idSublineToDelete = array();


        $deliveryLine = new DeliveryLines();

        if (isset($data["id"]) && is_numeric($data["id"])) {
            // load subline to check if need to delete
            $sublines = DeliveryLines::where("id_parent", $data["id"])->get();

            foreach ($sublines as $subline) {
                $idSublineToDelete[] = $subline->id;
            }


            // load line
            $deliveryLine = DeliveryLines::where('id', $data["id"])->first();
        }

        if (!isset($data["id_delivery"])) {
            $data["id_delivery"] = $id_delivery;
        }


        foreach ($data as $key => $value) {
            $deliveryLine->$key = $value;
        }


        // set id parent line
        $deliveryLine->id_parent = $id_parent;


        if (!isset($deliveryLine->accounting_number)) {
            $deliveryLine->accounting_number = "";
        }


        $deliveryLine->save();


        // save price list
        if (isset($data["priceList"]) && count($data["priceList"])) {
            foreach ($data["priceList"] as $priceList) {

                $deliveryLinePriceList = DeliveryLinePriceList::where("id_delivery_line", $deliveryLine->id)->where("id_price_list", $priceList["id_price_list"])->first();

                if (!$deliveryLinePriceList) {
                    $deliveryLinePriceList = new DeliveryLinePriceList() ;
                    $deliveryLinePriceList->id_delivery_line = $deliveryLine->id ;
                    $deliveryLinePriceList->id_price_list = $priceList["id_price_list"] ;
                }
                $deliveryLinePriceList->accounting_number = $priceList["accounting_number"] ;
                $deliveryLinePriceList->price_ht = $priceList["price_ht"] ;
                $deliveryLinePriceList->price_ttc = $priceList["price_ttc"] ;
                $deliveryLinePriceList->id_taxe = $priceList["id_taxe"] ;
                $deliveryLinePriceList->value_taxe = $priceList["value_taxe"] ;
                $deliveryLinePriceList->percentage_discount = $priceList["percentage_discount"] ;

                $deliveryLinePriceList->save() ;
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

                $this->saveLineData($dataSubline, $data["id_delivery"], $deliveryLine->id);
            }
        }

        if (count($idSublineToDelete)) {
            foreach ($idSublineToDelete as $idToDelete) {
                DeliveryLines::deleteLine($idToDelete);
            }
        }


        return $deliveryLine->id;
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
            $line = DeliveryLines::where("id", $data['id'])->first();

            DeliveryLines::updateOldTable($line->id_delivery, $data['oldSort']);
            DeliveryLines::updateNewTable($line->id_delivery, $data['sort']);

            $DeliveryLine = DeliveryLines::where("id", $data["id"])->first();
            if ($DeliveryLine) {
                $DeliveryLine->sort = $data['sort'];
            }
            $DeliveryLine->save();
        }

        echo json_encode($data['id']);
    }

    public function deleteLine(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            $line = DeliveryLines::where("id", $id)->first();
            DeliveryLines::updateOldTable($line->id_delivery, $line->sort);

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

    public function del_activity(Request $request)
    {
        $id = $request->input('id', 0);

        echo json_encode(DeliveryActivities::where("id", $id)->delete());
    }

    public function makePDF(Request $request)
    {
        $id = $request->input('id', 0);
        $echo = $request->input('echo', true);

        $data = [];

        $data['delivery'] = DeliveriesModel::where("id", $id)->first();
        $data['lines'] = DeliveryLines::getFromDelivery($id) ;

        $data['showDiscount'] = false;
        $data['tvas'] = [];

        foreach ($data['lines'] as $line) {
            if (floatval($line->discount) > 0)
                $data['showDiscount'] = true;

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
        $html = view("deliveries/PDF", $data, BASEPATH . 'App/com_zeapps_crm/views/')->getContent();

        $nomPDF = $data['delivery']->name_company . '_' . $data['delivery']->numerotation . '_' . $data['delivery']->libelle;
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