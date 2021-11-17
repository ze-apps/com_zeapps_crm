<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Models\Config;

class ComZeappsCrmV7
{
    public function up()
    {
        Capsule::schema()->table('com_zeapps_crm_delivery_documents', function (Blueprint $table) {
            $table->text('description')->after('name');
        });

        Capsule::schema()->table('com_zeapps_crm_invoice_documents', function (Blueprint $table) {
            $table->text('description')->after('name');
        });

        Capsule::schema()->table('com_zeapps_crm_order_documents', function (Blueprint $table) {
            $table->text('description')->after('name');
        });

        Capsule::schema()->table('com_zeapps_crm_quote_documents', function (Blueprint $table) {
            $table->text('description')->after('name');
        });
    }


    public function down()
    {
    }
}
