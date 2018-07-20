<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmStockMovementsTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_stock_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_warehouse');
            $table->integer('id_stock');
            $table->string('label', 255);
            $table->decimal('qty', 8, 2);
            $table->string('id_table', 255);
            $table->string('name_table', 255);
            $table->timestamp('date_mvt');
            $table->tinyInteger('ignored');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_stock_movements');
    }
}
