<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\PriceList as PriceListModel;

class PriceList extends Controller
{
    public function getAll(Request $request)
    {
        $priceList = PriceListModel::where("active", 1)->get();
        echo json_encode($priceList);
    }
}