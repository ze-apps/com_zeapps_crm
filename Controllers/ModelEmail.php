<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;
use Zeapps\Core\Storage;

use App\com_zeapps_crm\Models\ModelEmail as ModelEmailModel ;

class ModelEmail extends Controller
{
    public function liste(){
        $data = array();
        return view("model_email/list", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function form()
    {
        $data = array();
        return view("model_email/form", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }


    public function get(Request $request)
    {
        $id = $request->input('id', null);

        $modelEmail = ModelEmailModel::find($id);

        if ($modelEmail) {
            if (isset($modelEmail->attachments) && $modelEmail->attachments != "") {
                $modelEmail->attachments = json_decode($modelEmail->attachments, true);
            } else {
                $modelEmail->attachments = [];
            }
        }

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


            // faire un comparatif avant après pour voir s'il faut supprimer des pièces jointes
            if ($modelEmail) {
                $tabAttachments = [] ;
                if (isset($data["attachments"]) && is_array($data["attachments"])) {
                    $tabAttachments = $data["attachments"] ;
                }

                if (isset($modelEmail->attachments) && $modelEmail->attachments != "") {
                    $tabAncienAttachments = json_decode($modelEmail->attachments, true);
                    foreach ($tabAncienAttachments as $ancienAttachments) {
                        $trouve = false ;
                        foreach ($tabAttachments as $attachments) {
                            if ($attachments["path"] == $ancienAttachments["path"]) {
                                $trouve = true ;
                            }
                        }

                        if ($trouve == false) {
                            Storage::deleteFile($ancienAttachments["path"]);
                        }
                    }
                }
            }


        }

        foreach ($data as $key => $value) {
            if ($key == "attachments" && is_array($value)) {
                $value = json_encode($value);
            }
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

    public function uploadFile() {
        $file = Storage::uploadFile($_FILES["file"]);
        echo json_encode($file);
        exit();
    }
}