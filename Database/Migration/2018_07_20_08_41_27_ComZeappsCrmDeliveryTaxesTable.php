<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmDeliveryTaxesTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_delivery_taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_delivery')->default(0);
            $table->decimal('base_tax', 8, 2)->default(0);
            $table->decimal('value_rate_tax', 8, 2)->default(0);
            $table->decimal('amount_tax', 8, 2)->default(0);
            $table->string('accounting_number')->default("");

            $table->string('accounting_number_taxe')->default("");
            $table->integer('id_taxe')->default(0);
            $table->decimal('total_ttc', 8, 2)->default(0);


            $table->timestamps();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_delivery_taxes');
    }
}
