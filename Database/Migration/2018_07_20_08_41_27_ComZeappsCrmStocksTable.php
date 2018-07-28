<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmStocksTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_stock', false);
            $table->string('label', 255);
            $table->string('ref', 255);
            $table->decimal('value_ht', 9, 2);
            $table->integer('id_warehouse', false);
            $table->string('warehouse');
            $table->integer('resupply_delay');
            $table->integer('resupply_unit');
            $table->decimal('total', 9, 2);

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_stocks');
    }
}
