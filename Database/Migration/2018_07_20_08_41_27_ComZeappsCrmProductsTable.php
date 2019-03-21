<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmProductsTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_cat')->default(0);
            $table->integer('id_parent')->default(0);
            $table->integer('id_product')->default(0);
            $table->string('type_product', 255)->default("");
            $table->string('ref', 255)->default("");
            $table->string('name', 255)->default("");
            $table->text('description');
            $table->decimal('price_unit_stock', 8, 2)->default(0);
            $table->decimal('price_ht', 8, 2)->default(0);
            $table->decimal('price_ttc', 8, 2)->default(0);
            $table->decimal('quantite', 8, 2)->default(0);
            $table->tinyInteger('auto')->default(0);
            $table->integer('id_taxe')->default(0);
            $table->decimal('value_taxe', 8, 2)->default(0);
            $table->string('accounting_number', 255)->default("");
            $table->tinyInteger('update_price_from_subline', false)->default(0);
            $table->tinyInteger('show_subline', false)->default(0);
            $table->decimal('price_unit_ttc_subline', 8, 2)->default(0);
            $table->integer('sort')->default(0);
            $table->tinyInteger('active')->default(1);
            $table->mediumtext('extra');
            $table->timestamps();
            $table->softDeletes();

            $table->index('id_parent');
            $table->index('id_product');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_products');
    }
}