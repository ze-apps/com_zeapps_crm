<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_contact\Models\AddressFormat;
use App\com_zeapps_contact\Models\Country;
use App\com_zeapps_contact\Models\CountryLang;
use App\com_zeapps_contact\Models\States;
use App\com_zeapps_contact\Models\ZoneAddress;

class ComZeappsCrmInit
{

    public function up()
    {
        /*Capsule::schema()->create('zeapps_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user_account_manager', false, true)->default(0);
            $table->string('name_user_account_manager', 100)->default("");
            $table->string('company_name', 255)->default("");
            $table->integer('id_parent_company', false, true)->default(0);
            $table->string('name_parent_company', 255)->default("");
            $table->integer('id_account_family', false, true)->default(0);
            $table->string('name_account_family', 100)->default("");
            $table->integer('id_topology', false, true)->default(0);
            $table->string('name_topology', 100)->default("");
            $table->integer('id_activity_area', false, true)->default(0);
            $table->string('name_activity_area', 100)->default("");
            $table->bigInteger('turnover')->default(0);
            $table->string('billing_address_1', 100)->default("");
            $table->string('billing_address_2', 100)->default("");
            $table->string('billing_address_3', 100)->default("");
            $table->string('billing_city', 100)->default("");
            $table->string('billing_zipcode', 50)->default("");
            $table->integer('billing_state_id')->default(0);
            $table->string('billing_state', 100)->default("");
            $table->integer('billing_country_id', false, true)->default(0);
            $table->string('billing_country_name', 100)->default("");
            $table->string('delivery_address_1', 100)->default("");
            $table->string('delivery_address_2', 100)->default("");
            $table->string('delivery_address_3', 100)->default("");
            $table->string('delivery_city', 100)->default("");
            $table->string('delivery_zipcode', 50)->default("");
            $table->integer('delivery_state_id', false, true)->default(0);
            $table->string('delivery_state', 100)->default("");
            $table->integer('delivery_country_id', false, true)->default(0);
            $table->string('delivery_country_name', 100)->default("");
            $table->text('comment')->default("");
            $table->string('email', 255)->default("");
            $table->tinyInteger('opt_out', false, true)->default(0);
            $table->string('phone', 25)->default("");
            $table->string('fax', 25)->default("");
            $table->string('website_url', 255)->default("");
            $table->string('code_naf', 15)->default("");
            $table->string('code_naf_libelle', 255)->default("");
            $table->string('company_number', 30)->default("");
            $table->string('accounting_number', 15)->default("");
            $table->float('discount', 5,2)->default(0);
            $table->integer('id_modality', false, true)->default(0);
            $table->string('label_modality', 255)->default("");

            $table->timestamps();
            $table->softDeletes();
        });*/


    }


    public function down()
    {
        //Capsule::schema()->dropIfExists('com_zeapps_contact_companies');

    }
}
