<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\PriceList;

class ComZeappsCrmOrderLinePriceListTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_order_line_price_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_order_line')->default(0);
            $table->integer('id_price_list')->default(0);

            $table->string('accounting_number', 255)->default("");
            $table->decimal('price_ht', 8, 2)->default(0);
            $table->decimal('price_ttc', 8, 2)->default(0);
            $table->integer('id_taxe')->default(0);
            $table->decimal('value_taxe', 8, 2)->default(0);
            $table->decimal('percentage_discount', 8, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('id_order_line');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_order_line_price_list');
    }
}
