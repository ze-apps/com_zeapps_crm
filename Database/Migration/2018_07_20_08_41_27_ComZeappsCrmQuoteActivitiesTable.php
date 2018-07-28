<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmQuoteActivitiesTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_quote_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_quote', false, true);
            $table->integer('id_user', false, true);
            $table->string('name_user', 255);
            $table->string('libelle', 255);
            $table->text('description');
            $table->string('status');
            $table->integer('id_type', false, true);
            $table->string('label_type');
            $table->timestamp('date');
            $table->timestamp('deadline');
            $table->timestamp('reminder');
            $table->timestamp('validation');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_quote_activities');
    }
}
