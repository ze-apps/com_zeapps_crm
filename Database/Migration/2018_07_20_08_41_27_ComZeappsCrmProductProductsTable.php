<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmProductProductsTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_product_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_cat');
            $table->integer('id_stock');
            $table->tinyInteger('compose');
            $table->string('ref', 255);
            $table->string('name', 255);
            $table->text('description');
            $table->decimal('price_ht', 8, 2);
            $table->decimal('price_ttc', 8, 2);
            $table->tinyInteger('auto');
            $table->integer('id_taxe');
            $table->decimal('value_taxe', 8, 2);
            $table->string('accounting_number', 255);
            $table->mediumtext('extra');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_product_products');
    }
}
