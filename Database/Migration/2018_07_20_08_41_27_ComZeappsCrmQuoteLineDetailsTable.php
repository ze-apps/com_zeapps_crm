<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmQuoteLineDetailsTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_quote_line_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_quote')->default(0);
            $table->integer('id_line')->default(0);
            $table->integer('id_product')->default(0);
            $table->string('label', 255)->default("");
            $table->text('description');
            $table->decimal('qty', 8, 2)->default(0);
            $table->decimal('price_unit', 8, 2)->default(0);
            $table->integer('id_taxe')->default(0);
            $table->decimal('value_taxe', 8, 2)->default(0);
            $table->decimal('total_ht', 8, 2)->default(0);
            $table->decimal('total_ttc', 8, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_quote_line_details');
    }
}
