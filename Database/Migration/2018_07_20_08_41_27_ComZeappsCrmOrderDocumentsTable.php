<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmOrderDocumentsTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_order_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_order')->default(0);
            $table->string('name', 255)->default("");
            $table->string('path', 255)->default("");
            $table->timestamps();
            $table->softDeletes();

            $table->index('id_order');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_order_documents');
    }
}
