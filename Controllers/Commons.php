<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;


class Commons extends Controller
{
    public function transform_modal()
    {
        $data = array();
        return view("commons/transform_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }
    public function transformed_modal()
    {
        $data = array();
        return view("commons/transformed_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }
    public function form_comment()
    {
        $data = array();
        return view("commons/form_comment", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }
    public function form_document()
    {
        $data = array();
        return view("commons/form_document", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }
    public function form_activity()
    {
        $data = array();
        return view("commons/form_activity", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }
}