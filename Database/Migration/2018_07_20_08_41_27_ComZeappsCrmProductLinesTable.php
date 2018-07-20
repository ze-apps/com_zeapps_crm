<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmProductLinesTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_product_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_product');
            $table->integer('id_part');
            $table->decimal('quantite', 8, 2);
            $table->decimal('prorata', 8, 2);
            $table->tinyInteger('auto');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_product_lines');
    }
}
