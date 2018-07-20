<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmDeliveryCompaniesTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_delivery_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_delivery');
            $table->integer('id_user_account_manager');
            $table->string('name_user_account_manager', 100);
            $table->string('company_name', 255);
            $table->integer('id_parent_company');
            $table->string('name_parent_company', 255);
            $table->integer('id_type_account');
            $table->integer('name_type_account');
            $table->integer('id_activity_area');
            $table->string('name_activity_area', 100);
            $table->bigInteger('turnover');
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
            $table->text('comment');
            $table->string('phone', 25);
            $table->string('fax', 25);
            $table->string('website_url', 255);
            $table->string('code_naf', 15);
            $table->string('code_naf_libelle', 255);
            $table->string('company_number', 30);
            $table->string('accounting_number', 15);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_delivery_companies');
    }
}
