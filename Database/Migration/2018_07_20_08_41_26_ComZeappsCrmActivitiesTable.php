<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmActivitiesTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->default("");
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_activities');
    }
}
