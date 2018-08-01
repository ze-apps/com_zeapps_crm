<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmInvoiceCompaniesTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_invoice_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_invoice')->default(0);
            $table->integer('id_user_account_manager')->default(0);
            $table->string('name_user_account_manager', 100)->default("");
            $table->string('company_name', 255)->default("");
            $table->integer('id_parent_company')->default(0);
            $table->string('name_parent_company', 255)->default("");
            $table->integer('id_type_account')->default(0);
            $table->string('name_type_account', 255)->default("");
            $table->integer('id_activity_area')->default(0);
            $table->string('name_activity_area', 100)->default("");
            $table->bigInteger('turnover')->default(0);
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
            $table->text('comment');
            $table->string('phone', 25)->default("");
            $table->string('fax', 25)->default("");
            $table->string('website_url', 255)->default("");
            $table->string('code_naf', 15)->default("");
            $table->string('code_naf_libelle', 255)->default("");
            $table->string('company_number', 30)->default("");
            $table->string('accounting_number', 15)->default("");
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_invoice_companies');
    }
}
