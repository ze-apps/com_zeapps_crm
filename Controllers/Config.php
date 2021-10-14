<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\Activities as Activities;
use Zeapps\Models\Config as ConfigModel;
use App\com_zeapps_crm\Models\Quote\Quotes;
use App\com_zeapps_crm\Models\Invoice\Invoices;
use App\com_zeapps_crm\Models\Delivery\Deliveries;
use App\com_zeapps_crm\Models\Order\Orders;

class Config extends Controller
{
    public function quotes()
    {
        $data = array();
        return view("quotes/config", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function orders()
    {
        $data = array();
        return view("orders/config", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function invoice()
    {
        $data = array();
        return view("invoices/config", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function delivery()
    {
        $data = array();
        return view("deliveries/config", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }







    public function crm_quote_numerotation()
    {
        echo json_encode(Quotes::get_numerotation(true));
    }

    public function crm_quote_format()
    {
        $numerotation = ConfigModel::where("id", "crm_quote_format")->first();
        echo json_encode($numerotation);
    }


    public function crm_invoice_numerotation()
    {
        echo json_encode(Invoices::get_numerotation(true));
    }

    public function crm_invoice_format()
    {
        $numerotation = ConfigModel::where("id", "crm_invoice_format")->first();
        echo json_encode($numerotation);
    }


    public function crm_order_numerotation()
    {
        echo json_encode(Orders::get_numerotation(true));
    }

    public function crm_order_format()
    {
        $numerotation = ConfigModel::where("id", "crm_order_format")->first();
        echo json_encode($numerotation);
    }


    public function crm_delivery_numerotation()
    {
        echo json_encode(Deliveries::get_numerotation(true));
    }

    public function crm_delivery_format()
    {
        $numerotation = ConfigModel::where("id", "crm_delivery_format")->first();
        echo json_encode($numerotation);
    }
}