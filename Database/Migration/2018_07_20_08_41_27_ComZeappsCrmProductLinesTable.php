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
            $table->integer('id_product')->default(0);
            $table->integer('id_part')->default(0);
            $table->decimal('quantite', 8, 2)->default(0);
            $table->decimal('prorata', 8, 2)->default(0);
            $table->tinyInteger('auto')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_product_lines');
    }
}
