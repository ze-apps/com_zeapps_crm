<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Models\Config;

class ComZeappsCrmV2
{
    public function up()
    {
        Capsule::schema()->table('com_zeapps_contact_companies', function (Blueprint $table) {
            $table->decimal('outstanding_amount', 8, 2)->after('discount')->default(0);
        });

        Capsule::schema()->table('com_zeapps_contact_contacts', function (Blueprint $table) {
            $table->decimal('outstanding_amount', 8, 2)->after('discount')->default(0);
        });

        $objConfig = new Config() ;
        $objConfig->id = "crm_outstanding_amount" ;
        $objConfig->value = "1000" ;
        $objConfig->save() ;
    }


    public function down()
    {
    }
}
