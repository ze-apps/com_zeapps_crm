<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmAccountingTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_accounting', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_invoice')->default(0);
            $table->string('accounting_number', 255)->default("");
            $table->string('label', 1023)->default("");
            $table->string('invoice_num', 255)->default("");
            $table->decimal('debit', 8, 2)->default(0);
            $table->decimal('credit', 8, 2)->default(0);
            $table->string('journal', 32)->default("");
            $table->timestamp('date_invoice')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_accounting');
    }
}
