<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Models\Config;

class ComZeappsCrmV8
{
    public function up()
    {
        Capsule::schema()->table('com_zeapps_crm_delivery_documents', function (Blueprint $table) {
            $table->integer('id_user', false, true)->after('description')->default(0);
            $table->string('user_name', 255)->after('id_user')->default("");
        });

        Capsule::schema()->table('com_zeapps_crm_invoice_documents', function (Blueprint $table) {
            $table->integer('id_user', false, true)->after('description')->default(0);
            $table->string('user_name', 255)->after('id_user')->default("");
        });

        Capsule::schema()->table('com_zeapps_crm_order_documents', function (Blueprint $table) {
            $table->integer('id_user', false, true)->after('description')->default(0);
            $table->string('user_name', 255)->after('id_user')->default("");
        });

        Capsule::schema()->table('com_zeapps_crm_quote_documents', function (Blueprint $table) {
            $table->integer('id_user', false, true)->after('description')->default(0);
            $table->string('user_name', 255)->after('id_user')->default("");
        });
    }


    public function down()
    {
    }
}
