<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\CrmOrigins as CrmOriginsModel ;


class CrmOrigins extends Controller
{
    public function get($id)
    {
        echo json_encode(CrmOriginsModel::where("id", $id)->first());
    }

    public function getAll()
    {
        echo json_encode(CrmOriginsModel::get());
    }
}