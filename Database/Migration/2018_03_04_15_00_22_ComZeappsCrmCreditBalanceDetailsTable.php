<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmCreditBalanceDetailsTable
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_credit_balance_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_invoice', false)->default(0);
            $table->float('paid', 9, 2)->default(0.0);
            $table->integer('id_modality', false);
            $table->string('label_modality');
            $table->timestamp('date_payment');

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_credit_balance_details');
    }
}
