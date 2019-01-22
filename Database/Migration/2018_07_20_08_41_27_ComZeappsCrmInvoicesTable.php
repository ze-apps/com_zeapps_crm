<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmInvoicesTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_price_list')->default(0);
            $table->string('libelle', 255)->default("");
            $table->string('numerotation', 255)->default("");
            $table->integer('id_origin', false)->default(0);
            $table->integer('status', false)->default(0);
            $table->tinyInteger('finalized')->default(0);
            $table->string('final_pdf', 1023)->default("");
            $table->decimal('due', 8, 2)->default(0);
            $table->integer('id_user_account_manager')->default(0);
            $table->string('name_user_account_manager')->default("");
            $table->integer('id_warehouse')->default(0);
            $table->integer('id_company')->default(0);
            $table->string('name_company')->default("");
            $table->integer('id_contact')->default(0);
            $table->string('name_contact')->default("");
            $table->string('billing_address_1', 100)->default("");
            $table->string('billing_address_2', 100)->default("");
            $table->string('billing_address_3', 100)->default("");
            $table->string('billing_city', 100)->default("");
            $table->string('billing_zipcode', 50)->default("");
            $table->string('billing_state', 100)->default("");
            $table->integer('billing_country_id')->default(0);
            $table->string('billing_country_name', 100)->default("");
            $table->string('delivery_address_1', 100)->default("");
            $table->string('delivery_address_2', 100)->default("");
            $table->string('delivery_address_3', 100)->default("");
            $table->string('delivery_city', 100)->default("");
            $table->string('delivery_zipcode', 50)->default("");
            $table->string('delivery_state', 100)->default("");
            $table->integer('delivery_country_id')->default(0);
            $table->string('delivery_country_name', 100)->default("");
            $table->string('accounting_number', 255)->default("");
            $table->decimal('global_discount', 8, 2)->default(0);
            $table->decimal('total_prediscount_ht', 8, 2)->default(0);
            $table->decimal('total_prediscount_ttc', 8, 2)->default(0);
            $table->decimal('total_discount_ht', 8, 2)->default(0);
            $table->decimal('total_discount_ttc', 8, 2)->default(0);
            $table->decimal('total_ht', 9, 2)->default(0);
            $table->decimal('total_tva', 9, 2)->default(0);
            $table->decimal('total_ttc', 9, 2)->default(0);
            $table->timestamp('date_creation')->nullable();
            $table->timestamp('date_limit')->nullable();
            $table->string('id_modality', 255)->default("");
            $table->string('label_modality', 255)->default("");
            $table->string('reference_client', 255)->default("");
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_invoices');
    }
}
