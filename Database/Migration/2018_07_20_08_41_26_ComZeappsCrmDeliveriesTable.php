<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmDeliveriesTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('libelle', 255);
            $table->string('numerotation', 255);
            $table->integer('id_origin', false);
            $table->integer('status', false);
            $table->tinyInteger('finalized');
            $table->string('final_pdf', 1023);
            $table->integer('id_user_account_manager');
            $table->string('name_user_account_manager');
            $table->integer('id_warehouse');
            $table->integer('id_company');
            $table->string('name_company');
            $table->integer('id_contact');
            $table->string('name_contact');
            $table->string('billing_address_1', 100);
            $table->string('billing_address_2', 100);
            $table->string('billing_address_3', 100);
            $table->string('billing_city', 100);
            $table->string('billing_zipcode', 50);
            $table->string('billing_state', 100);
            $table->integer('billing_country_id');
            $table->string('billing_country_name', 100);
            $table->string('delivery_address_1', 100);
            $table->string('delivery_address_2', 100);
            $table->string('delivery_address_3', 100);
            $table->string('delivery_city', 100);
            $table->string('delivery_zipcode', 50);
            $table->string('delivery_state', 100);
            $table->integer('delivery_country_id');
            $table->string('delivery_country_name', 100);
            $table->string('accounting_number', 255);
            $table->decimal('global_discount', 8, 2);
            $table->decimal('total_prediscount_ht', 8, 2);
            $table->decimal('total_prediscount_ttc', 8, 2);
            $table->decimal('total_discount', 8, 2);
            $table->decimal('total_ht', 9, 2);
            $table->decimal('total_tva', 9, 2);
            $table->decimal('total_ttc', 9, 2);
            $table->timestamp('date_creation');
            $table->timestamp('date_limit');
            $table->string('id_modality', 255);
            $table->string('label_modality', 255);
            $table->string('reference_client', 255);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_deliveries');
    }
}
