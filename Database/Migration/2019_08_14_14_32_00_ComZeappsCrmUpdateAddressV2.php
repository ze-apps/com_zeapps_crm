<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmUpdateAddressV2
{
    public function up()
    {
        Capsule::schema()->table('com_zeapps_crm_quotes', function (Blueprint $table) {
            $table->integer('id_company_delivery')->after('name_contact')->default(0);
            $table->string('name_company_delivery', 255)->after('id_company_delivery')->default("");
            $table->integer('id_contact_delivery')->after('name_company_delivery')->default(0);
            $table->string('name_contact_delivery', 255)->after('id_contact_delivery')->default("");
            $table->text('delivery_address_full_text')->after('name_contact_delivery');
            $table->text('billing_address_full_text')->after('name_contact');
        });

        Capsule::schema()->table('com_zeapps_crm_orders', function (Blueprint $table) {
            $table->integer('id_company_delivery')->after('name_contact')->default(0);
            $table->string('name_company_delivery', 255)->after('id_company_delivery')->default("");
            $table->integer('id_contact_delivery')->after('name_company_delivery')->default(0);
            $table->string('name_contact_delivery', 255)->after('id_contact_delivery')->default("");
            $table->text('delivery_address_full_text')->after('name_contact_delivery');
            $table->text('billing_address_full_text')->after('name_contact');
        });

        Capsule::schema()->table('com_zeapps_crm_invoices', function (Blueprint $table) {
            $table->integer('id_company_delivery')->after('name_contact')->default(0);
            $table->string('name_company_delivery', 255)->after('id_company_delivery')->default("");
            $table->integer('id_contact_delivery')->after('name_company_delivery')->default(0);
            $table->string('name_contact_delivery', 255)->after('id_contact_delivery')->default("");
            $table->text('delivery_address_full_text')->after('name_contact_delivery');
            $table->text('billing_address_full_text')->after('name_contact');
        });

        Capsule::schema()->table('com_zeapps_crm_deliveries', function (Blueprint $table) {
            $table->integer('id_company_delivery')->after('name_contact')->default(0);
            $table->string('name_company_delivery', 255)->after('id_company_delivery')->default("");
            $table->integer('id_contact_delivery')->after('name_company_delivery')->default(0);
            $table->string('name_contact_delivery', 255)->after('id_contact_delivery')->default("");
            $table->text('delivery_address_full_text')->after('name_contact_delivery');
            $table->text('billing_address_full_text')->after('name_contact');
        });
    }


    public function down()
    {
    }
}
