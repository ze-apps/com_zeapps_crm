<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Models\Config;

class ComZeappsCrmV3
{
    public function up()
    {
        Capsule::schema()->table('com_zeapps_crm_products', function (Blueprint $table) {
            $table->decimal('maximum_discount_allowed', 5, 2)->after('is_updated')->default(100);
            $table->decimal('weight', 11, 2)->after('maximum_discount_allowed')->default(0);
        });



        Capsule::schema()->table('com_zeapps_crm_deliveries', function (Blueprint $table) {
            $table->decimal('weight', 11, 2)->after('global_discount')->default(0);
        });
        Capsule::schema()->table('com_zeapps_crm_delivery_lines', function (Blueprint $table) {
            $table->decimal('maximum_discount_allowed', 5, 2)->after('discount')->default(100);
            $table->decimal('weight', 11, 2)->after('maximum_discount_allowed')->default(0);
        });
        Capsule::schema()->table('com_zeapps_crm_delivery_line_price_list', function (Blueprint $table) {
            $table->decimal('maximum_discount_allowed', 5, 2)->after('percentage_discount')->default(100);
            $table->decimal('weight', 11, 2)->after('maximum_discount_allowed')->default(0);
        });


        Capsule::schema()->table('com_zeapps_crm_invoices', function (Blueprint $table) {
            $table->decimal('weight', 11, 2)->after('global_discount')->default(0);
        });
        Capsule::schema()->table('com_zeapps_crm_invoice_lines', function (Blueprint $table) {
            $table->decimal('maximum_discount_allowed', 5, 2)->after('discount')->default(100);
            $table->decimal('weight', 11, 2)->after('maximum_discount_allowed')->default(0);
        });
        Capsule::schema()->table('com_zeapps_crm_invoice_line_price_list', function (Blueprint $table) {
            $table->decimal('maximum_discount_allowed', 5, 2)->after('percentage_discount')->default(100);
            $table->decimal('weight', 11, 2)->after('maximum_discount_allowed')->default(0);
        });


        Capsule::schema()->table('com_zeapps_crm_orders', function (Blueprint $table) {
            $table->decimal('weight', 11, 2)->after('global_discount')->default(0);
        });
        Capsule::schema()->table('com_zeapps_crm_order_lines', function (Blueprint $table) {
            $table->decimal('maximum_discount_allowed', 5, 2)->after('discount')->default(100);
            $table->decimal('weight', 11, 2)->after('maximum_discount_allowed')->default(0);
        });
        Capsule::schema()->table('com_zeapps_crm_order_line_price_list', function (Blueprint $table) {
            $table->decimal('maximum_discount_allowed', 5, 2)->after('percentage_discount')->default(100);
            $table->decimal('weight', 11, 2)->after('maximum_discount_allowed')->default(0);
        });


        Capsule::schema()->table('com_zeapps_crm_quotes', function (Blueprint $table) {
            $table->decimal('weight', 11, 2)->after('global_discount')->default(0);
        });
        Capsule::schema()->table('com_zeapps_crm_quote_lines', function (Blueprint $table) {
            $table->decimal('maximum_discount_allowed', 5, 2)->after('discount')->default(100);
            $table->decimal('weight', 11, 2)->after('maximum_discount_allowed')->default(0);
        });
        Capsule::schema()->table('com_zeapps_crm_quote_line_price_list', function (Blueprint $table) {
            $table->decimal('maximum_discount_allowed', 5, 2)->after('percentage_discount')->default(100);
            $table->decimal('weight', 11, 2)->after('maximum_discount_allowed')->default(0);
        });
    }


    public function down()
    {
    }
}
