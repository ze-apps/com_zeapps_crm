<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmDeliveryContactsTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_delivery_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_delivery');
            $table->integer('id_user_account_manager');
            $table->string('name_user_account_manager', 100);
            $table->integer('id_company');
            $table->string('name_company', 255);
            $table->string('title_name', 30);
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 255);
            $table->string('phone', 25);
            $table->string('other_phone', 25);
            $table->string('mobile', 25);
            $table->string('fax', 25);
            $table->string('assistant', 70);
            $table->string('assistant_phone', 25);
            $table->string('department', 100);
            $table->string('job', 100);
            $table->enum('email_opt_out', ['Y','N']);
            $table->string('skype_id', 100);
            $table->string('twitter', 100);
            $table->date('date_of_birth');
            $table->string('address_1', 100);
            $table->string('address_2', 100);
            $table->string('address_3', 100);
            $table->string('city', 100);
            $table->string('zipcode', 50);
            $table->string('state', 100);
            $table->integer('country_id');
            $table->string('country_name', 100);
            $table->text('comment');
            $table->string('website_url', 255);
            $table->string('accounting_number', 15);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_delivery_contacts');
    }
}
