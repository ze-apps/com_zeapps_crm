<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmCreditBalancesTable
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_credit_balances', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('id_invoice', false)->default(0);
            $table->tinyInteger('numerotation', false)->default(0);
            $table->tinyInteger('id_company', false)->default(0);
            $table->tinyInteger('name_company', false)->default(0);
            $table->tinyInteger('id_contact', false)->default(0);
            $table->tinyInteger('name_contact', false)->default(0);
            $table->tinyInteger('due_date', false)->default(0);
            $table->tinyInteger('total', false)->default(0);
            $table->tinyInteger('paid', false)->default(0);
            $table->tinyInteger('left_to_pay', false)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_credit_balances');
    }
}
