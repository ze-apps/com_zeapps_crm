<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Models\Config;

class ComZeappsCrmV4
{
    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_model_email', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default("");
            $table->string('default_to')->default("");
            $table->string('subject')->default("");
            $table->text('message');
            $table->text('attachments');
            $table->tinyInteger('to_quote')->default(0);
            $table->tinyInteger('to_order')->default(0);
            $table->tinyInteger('to_invoice')->default(0);
            $table->tinyInteger('to_delivery')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_model_email');
    }
}
