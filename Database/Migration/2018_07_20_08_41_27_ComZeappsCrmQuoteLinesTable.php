<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmQuoteLinesTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_quote_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_quote')->default(0);
            $table->integer('id_parent')->default(0);
            $table->string('type', 255)->default("");
            $table->integer('id_product')->default(0);
            $table->string('ref', 255)->default("");
            $table->string('designation_title', 255)->default("");
            $table->text('designation_desc');
            $table->decimal('qty', 8, 2)->default(0);
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('price_unit', 8, 2)->default(0);
            $table->integer('id_taxe', false)->default(0);
            $table->decimal('value_taxe', 8, 2)->default(0);
            $table->string('accounting_number')->default("");
            $table->decimal('total_ht', 8, 2)->default(0);
            $table->decimal('total_ttc', 8, 2)->default(0);
            $table->tinyInteger('update_price_from_subline', false)->default(0);
            $table->tinyInteger('show_subline', false)->default(0);
            $table->decimal('price_unit_ttc_subline', 8, 2)->default(0);
            $table->integer('sort')->default(0);
            $table->text('json');

            $table->timestamps();
            $table->softDeletes();

            $table->index('id_quote');
            $table->index('id_parent');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_quote_lines');
    }
}
