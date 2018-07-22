<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmQuoteDocumentsTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_quote_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_quote');
            $table->string('name', 255);
            $table->string('path', 255);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_quote_documents');
    }
}