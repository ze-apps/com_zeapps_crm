<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\Warehouses ;


class Warehouse extends Controller
{
    public function get($id){
        echo json_encode(Warehouses::where("id", $id)->first());
    }

    public function getAll(){
        echo json_encode(Warehouses::get());
    }
}