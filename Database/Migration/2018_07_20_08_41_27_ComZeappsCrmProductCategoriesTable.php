<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmProductCategoriesTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_product_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_parent');
            $table->string('name', 255);
            $table->integer('nb_products');
            $table->integer('nb_products_r');
            $table->integer('sort');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_product_categories');
    }
}
