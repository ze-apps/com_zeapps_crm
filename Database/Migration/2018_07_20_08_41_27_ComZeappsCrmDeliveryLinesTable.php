<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmDeliveryLinesTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_delivery_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_delivery');
            $table->string('type', 255);
            $table->integer('id_product');
            $table->string('designation_title', 255);
            $table->text('designation_desc');
            $table->decimal('qty', 8, 2);
            $table->decimal('discount', 8, 2);
            $table->decimal('price_unit', 8, 2);
            $table->decimal('taxe', 8, 2);
            $table->decimal('total_ht', 8, 2);
            $table->decimal('total_ttc', 8, 2);
            $table->integer('sort');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_delivery_lines');
    }
}
