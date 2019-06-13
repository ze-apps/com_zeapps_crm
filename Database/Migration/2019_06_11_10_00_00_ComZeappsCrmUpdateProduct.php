<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmUpdateProduct
{

    public function up()
    {
        Capsule::schema()->table('com_zeapps_crm_products', function (Blueprint $table) {
            $table->tinyInteger('discount_prohibited')->after('active')->default(0);
        });

        Capsule::schema()->table('com_zeapps_crm_delivery_lines', function (Blueprint $table) {
            $table->tinyInteger('discount_prohibited')->after('sort')->default(0);
        });

        Capsule::schema()->table('com_zeapps_crm_invoice_lines', function (Blueprint $table) {
            $table->tinyInteger('discount_prohibited')->after('sort')->default(0);
        });

        Capsule::schema()->table('com_zeapps_crm_order_lines', function (Blueprint $table) {
            $table->tinyInteger('discount_prohibited')->after('sort')->default(0);
        });

        Capsule::schema()->table('com_zeapps_crm_quote_lines', function (Blueprint $table) {
            $table->tinyInteger('discount_prohibited')->after('sort')->default(0);
        });
    }


    public function down()
    {
    }
}
