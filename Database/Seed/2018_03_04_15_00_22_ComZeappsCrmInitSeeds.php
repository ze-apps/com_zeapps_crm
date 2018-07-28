<?php

use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\Taxes;
use App\com_zeapps_crm\Models\Activities;


class ComZeappsCrmInitSeeds
{
    public function run()
    {
        // import de compagnies
        Capsule::table('com_zeapps_crm_taxes')->truncate();
        $compagnies = json_decode(file_get_contents(dirname(__FILE__) . "/taxes.json"));
        foreach ($compagnies as $compagny_json) {
            $taxe = new Taxes();

            foreach ($compagny_json as $key => $value) {
                $taxe->$key = $value ;
            }

            $taxe->save();
        }


        // import de compagnies
        Capsule::table('com_zeapps_crm_activities')->truncate();
        $activities = json_decode(file_get_contents(dirname(__FILE__) . "/activities.json"));
        foreach ($activities as $activity_json) {
            $activity = new Activities();

            foreach ($activity_json as $key => $value) {
                $activity->$key = $value ;
            }

            $activity->save();
        }
    }
}
