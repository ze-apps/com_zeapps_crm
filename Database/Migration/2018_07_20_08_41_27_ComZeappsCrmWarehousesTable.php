<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmWarehousesTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_warehouses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label', 255);
            $table->integer('resupply_delay');
            $table->string('resupply_unit', 63);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_warehouses');
    }
}
