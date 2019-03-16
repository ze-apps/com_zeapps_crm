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
            $table->integer('id_stock', false)->default(0);
            $table->string('label', 255)->default("");
            $table->string('ref', 255)->default("");
            $table->decimal('value_ht', 9, 2)->default(0);
            $table->integer('id_warehouse', false)->default(0);
            $table->string('warehouse')->default("");
            $table->integer('resupply_delay')->default(0);
            $table->integer('resupply_unit')->default(0);
            $table->decimal('total', 9, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('id_stock');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_stocks');
    }
}
