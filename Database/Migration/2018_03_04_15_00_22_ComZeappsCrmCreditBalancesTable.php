<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmCreditBalancesTable
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_credit_balances', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('id_invoice', false);
            $table->tinyInteger('numerotation', false);
            $table->tinyInteger('id_company', false);
            $table->tinyInteger('name_company', false);
            $table->tinyInteger('id_contact', false);
            $table->tinyInteger('name_contact', false);
            $table->tinyInteger('due_date', false);
            $table->tinyInteger('total', false);
            $table->tinyInteger('paid', false);
            $table->tinyInteger('left_to_pay', false);

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_credit_balances');
    }
}
