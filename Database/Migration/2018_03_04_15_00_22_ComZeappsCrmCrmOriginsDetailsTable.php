<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmCrmOriginsDetailsTable
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_crm_origins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_crm_origins');
    }
}
