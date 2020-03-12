<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\ModelEmail as ModelEmailModel ;

class ModelEmail extends Controller
{
    public function liste(){
        $data = array();
        return view("model_email/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form()
    {
        $data = array();
        return view("model_email/form", $data, BASEPATH . 'App/com_quiltmania_publicite/views/');
    }


    public function get(Request $request)
    {
        $id = $request->input('id', null);

        $modelEmail = ModelEmailModel::where("id", $id)->first();

        echo json_encode(array('modelEmail' => $modelEmail));
    }

    public function get_all()
    {
        $modelEmails = ModelEmailModel::get();

        echo json_encode($modelEmails);
    }

    public function save()
    {
        // constitution du tableau
        $data = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $modelEmail = new ModelEmailModel();

        if (isset($data["id"])) {
            $modelEmail = ModelEmailModel::where('id', $data["id"])->first();
        }

        foreach ($data as $key => $value) {
            $modelEmail->$key = $value;
        }

        $modelEmail->save();

        echo $modelEmail->id;
    }

    public function delete(Request $request)
    {
        $id = $request->input('id', null);

        ModelEmailModel::where("id", $id)->delete();

        echo json_encode('OK');
    }
}