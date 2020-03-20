<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Models\Config;

class ComZeappsCrmV5
{
    public function up()
    {
        Capsule::schema()->table('com_zeapps_crm_quotes', function (Blueprint $table) {
            $table->integer('default_template_email', false, true)->after('reference_client')->default(0);
        });

        Capsule::schema()->table('com_zeapps_crm_deliveries', function (Blueprint $table) {
            $table->integer('default_template_email', false, true)->after('reference_client')->default(0);
        });

        Capsule::schema()->table('com_zeapps_crm_invoices', function (Blueprint $table) {
            $table->integer('default_template_email', false, true)->after('reference_client')->default(0);
        });

        Capsule::schema()->table('com_zeapps_crm_orders', function (Blueprint $table) {
            $table->integer('default_template_email', false, true)->after('reference_client')->default(0);
        });
    }


    public function down()
    {
    }
}
