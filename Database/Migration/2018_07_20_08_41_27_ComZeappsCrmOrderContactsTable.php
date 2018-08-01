<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmOrderContactsTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_order_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_order')->default(0);
            $table->integer('id_user_account_manager')->default(0);
            $table->string('name_user_account_manager', 100)->default("");
            $table->integer('id_company')->default(0);
            $table->string('name_company', 255)->default("");
            $table->string('title_name', 30)->default("");
            $table->string('first_name', 50)->default("");
            $table->string('last_name', 50)->default("");
            $table->string('email', 255)->default("");
            $table->string('phone', 25)->default("");
            $table->string('other_phone', 25)->default("");
            $table->string('mobile', 25)->default("");
            $table->string('fax', 25)->default("");
            $table->string('assistant', 70)->default("");
            $table->string('assistant_phone', 25)->default("");
            $table->string('department', 100)->default("");
            $table->string('job', 100)->default("");
            $table->enum('email_opt_out', ['Y','N'])->default("N");
            $table->string('skype_id', 100)->default("");
            $table->string('twitter', 100)->default("");
            $table->date('date_of_birth')->nullable();
            $table->string('address_1', 100)->default("");
            $table->string('address_2', 100)->default("");
            $table->string('address_3', 100)->default("");
            $table->string('city', 100)->default("");
            $table->string('zipcode', 50)->default("");
            $table->string('state', 100)->default("");
            $table->integer('country_id')->default(0);
            $table->string('country_name', 100)->default("");
            $table->text('comment');
            $table->string('website_url', 255)->default("");
            $table->string('accounting_number', 15)->default("");
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_order_contacts');
    }
}
