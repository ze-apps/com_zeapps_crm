<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmInvoiceLineDetailsTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_invoice_line_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_invoice');
            $table->integer('id_line');
            $table->integer('id_product');
            $table->string('label', 255);
            $table->text('description');
            $table->decimal('qty', 8, 2);
            $table->decimal('price_unit', 8, 2);
            $table->integer('id_taxe');
            $table->decimal('value_taxe', 8, 2);
            $table->decimal('total_ht', 8, 2);
            $table->decimal('total_ttc', 8, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_invoice_line_details');
    }
}
