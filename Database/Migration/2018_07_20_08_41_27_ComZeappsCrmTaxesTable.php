<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmTaxesTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label', 255)->default("");
            $table->decimal('value', 8, 2)->default(0);
            $table->string('accounting_number', 255)->default("");
            $table->tinyInteger('active')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_taxes');
    }
}
