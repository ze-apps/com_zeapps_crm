<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmUpdateAddress
{

    public function up()
    {
        Capsule::schema()->table('com_zeapps_crm_quotes', function (Blueprint $table) {
            $table->string('delivery_name_company', 255)->after('id_company_address_delivery')->default("");
            $table->string('delivery_name_contact', 255)->after('id_contact_address_delivery')->default("");
        });

        Capsule::schema()->table('com_zeapps_crm_orders', function (Blueprint $table) {
            $table->string('delivery_name_company', 255)->after('id_company_address_delivery')->default("");
            $table->string('delivery_name_contact', 255)->after('id_contact_address_delivery')->default("");
        });

        Capsule::schema()->table('com_zeapps_crm_invoices', function (Blueprint $table) {
            $table->string('delivery_name_company', 255)->after('id_company_address_delivery')->default("");
            $table->string('delivery_name_contact', 255)->after('id_contact_address_delivery')->default("");
        });

        Capsule::schema()->table('com_zeapps_crm_deliveries', function (Blueprint $table) {
            $table->string('delivery_name_company', 255)->after('id_company_address_delivery')->default("");
            $table->string('delivery_name_contact', 255)->after('id_contact_address_delivery')->default("");
        });
    }


    public function down()
    {
    }
}
