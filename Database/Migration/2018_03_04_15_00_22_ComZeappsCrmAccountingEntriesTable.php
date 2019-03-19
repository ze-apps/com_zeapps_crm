<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmAccountingEntriesTable
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_accounting_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_invoice', false)->default(0);
            $table->integer('n_export', false)->default(0);
            $table->string('accounting_number')->default("");
            $table->string('number_document')->default("");
            $table->string('label')->default("");
            $table->float('debit', 9, 2)->default(0.0);
            $table->float('credit', 9, 2)->default(0.0);
            $table->string('code_journal')->default("");
            $table->timestamp('date_export');
            $table->timestamp('date_writing');
            $table->timestamp('date_limit');

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_accounting_entries');
    }
}
