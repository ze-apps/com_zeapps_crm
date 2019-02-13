<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;

use App\com_zeapps_crm\Models\Payment\Payment as PaymentModel;
use App\com_zeapps_crm\Models\Payment\PaymentLine;
use App\com_zeapps_crm\Models\Invoice\Invoices;

class Payment extends Controller
{
    public function view_modal()
    {
        $data = array();
        return view("payment/view_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function lists_partial()
    {
        $data = array();
        return view("payment/lists_partial", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form_modal()
    {
        $data = array();
        return view("payment/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }



    public function get(Request $request)
    {
        $id = $request->input('id', 0);

        $payment = PaymentModel::where('id', $id)->first();



        // charge les lignes de l'encaissemenet
        $lines = PaymentLine::where("id_payment", $id)->get() ;

        foreach ($lines as $line) {
            $line->invoice_data = Invoices::find($line->id_invoice);
        }


        echo json_encode(array(
            'payment' => $payment,
            'payment_lines' => $lines,
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


        $payments_rs = PaymentModel::orderBy('date_payment', 'DESC')->orderBy('id', 'DESC');
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $payments_rs = $payments_rs->where($key, 'like', '%' . $value . '%');
            } else {
                $payments_rs = $payments_rs->where($key, $value);
            }
        }

        $total = $payments_rs->count();

        $payments = $payments_rs->limit($limit)->offset($offset)->get();;


        if (!$payments) {
            $payments = [];
        }

        echo json_encode(array(
            'payments' => $payments,
            'total' => $total
        ));

    }

    public function delete(Request $request)
    {
        $id = $request->input('id', 0);

        if ($id) {
            $lines = PaymentLine::where('id_payment', $id)->get();

            foreach ($lines as $line) {
                $line->delete();
            }

            echo json_encode(PaymentModel::where("id", $id)->delete());
        } else {
            echo json_encode("ERROR");
        }
    }
}