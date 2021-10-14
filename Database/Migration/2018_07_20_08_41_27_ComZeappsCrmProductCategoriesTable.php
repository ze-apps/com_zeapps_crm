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
            $table->integer('id_parent')->default(0);
            $table->string('name', 255)->default("");
            $table->integer('nb_products')->default(0);
            $table->integer('nb_products_r')->default(0);
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('id_parent');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_product_categories');
    }
}
