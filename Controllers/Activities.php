<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use Zeapps\Core\Event;

use App\com_zeapps_crm\Models\Activity\ActivityConnection;

use Zeapps\Models\Config;

class Activities extends Controller
{
    public function lists()
    {
        $data = array();
        return view("activities/lists", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }

    public function getAll(Request $request)
    {
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);


        $filters = array();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }


        $activities_rs = ActivityConnection::orderBy('deadline', 'DESC');
            
        foreach ($filters as $key => $value) {
            if (strpos($key, " LIKE")) {
                $key = str_replace(" LIKE", "", $key);
                $activities_rs = $activities_rs->where($key, 'like', '%' . $value . '%');
            } elseif (strpos($key, " ") !== false) {
                $tabKey = explode(" ", $key);
                $activities_rs = $activities_rs->where($tabKey[0], $tabKey[1], $value);
            } else {
                $activities_rs = $activities_rs->where($key, $value);
            }
        }

        $total = $activities_rs->count();
        
        $activities_src = $activities_rs->limit($limit)->offset($offset)->get();;


        $activities = [];

        if ($activities_src) {
            foreach ($activities_src as $activity) {
                $activity->url_to_document = "" ;
                $activity->info_source = "" ;

                // appel les observateurs pour obtenir les informations ci-dessus
                Event::sendAction('com_zeapps_crm_activites', 'getInfoSource', $activity);

                $activities[] = $activity;
            }
        }

        echo json_encode(array(
            'activities' => $activities,
            'total' => $total
        ));

    }
}