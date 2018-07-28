<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\Activities as Activities;

class ActivityTypes extends Controller
{
    public function all()
    {
        echo json_encode(array("activity_types" => Activities::get()));
    }
}