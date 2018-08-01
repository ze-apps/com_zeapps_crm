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
            $table->integer('id_cat')->default(0);
            $table->integer('id_stock')->default(0);
            $table->tinyInteger('compose')->default(0);
            $table->string('ref', 255)->default("");
            $table->string('name', 255)->default("");
            $table->text('description');
            $table->decimal('price_ht', 8, 2)->default(0);
            $table->decimal('price_ttc', 8, 2)->default(0);
            $table->tinyInteger('auto')->default(0);
            $table->integer('id_taxe')->default(0);
            $table->decimal('value_taxe', 8, 2)->default(0);
            $table->string('accounting_number', 255)->default("");
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
