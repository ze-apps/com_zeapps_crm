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
            $table->integer('id_warehouse')->default(0);
            $table->integer('id_stock')->default(0);
            $table->string('label', 255)->default("");
            $table->decimal('qty', 8, 2)->default(0);
            $table->string('id_table', 255)->default("");
            $table->string('name_table', 255)->default("");
            $table->timestamp('date_mvt')->nullable();
            $table->tinyInteger('ignored')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('id_warehouse');
            $table->index(array('id_warehouse', 'id_stock'));
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_stock_movements');
    }
}