<?php

use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\Taxes;
use App\com_zeapps_crm\Models\Activities;
use App\com_zeapps_crm\Models\CrmOrigins;
use App\com_zeapps_crm\Models\Warehouses;


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


        // import des activités
        Capsule::table('com_zeapps_crm_activities')->truncate();
        $activities = json_decode(file_get_contents(dirname(__FILE__) . "/activities.json"));
        foreach ($activities as $activity_json) {
            $activity = new Activities();

            foreach ($activity_json as $key => $value) {
                $activity->$key = $value ;
            }

            $activity->save();
        }



        // import de origins
        Capsule::table('com_zeapps_crm_crm_origins')->truncate();
        $origins = json_decode(file_get_contents(dirname(__FILE__) . "/crm_origins.json"));
        foreach ($origins as $origin_json) {
            $crmOrigin = new CrmOrigins();

            foreach ($origin_json as $key => $value) {
                $crmOrigin->$key = $value ;
            }

            $crmOrigin->save();
        }





        // import de warehouses
        Capsule::table('com_zeapps_crm_warehouses')->truncate();
        $warehouses = json_decode(file_get_contents(dirname(__FILE__) . "/warehouses.json"));
        foreach ($warehouses as $warehouse_json) {
            $warehouse = new Warehouses();

            foreach ($warehouse_json as $key => $value) {
                $warehouse->$key = $value ;
            }

            $warehouse->save();
        }





    }
}
