<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\ProductCategories as CategoriesModel;

class AccountingNumbers extends Controller
{
    public function form_modal(){
        $data = array();
        return view("accounting_numbers/form_modal", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

}