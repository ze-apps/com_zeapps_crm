<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmDocumentRelatedTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_document_related', function (Blueprint $table) {
            $table->increments('id');

            $table->string('type_document_from', 255)->default("");
            $table->integer('id_document_from')->default(0);

            $table->string('type_document_to', 255)->default("");
            $table->integer('id_document_to')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('id_document_from');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_document_related');
    }
}
