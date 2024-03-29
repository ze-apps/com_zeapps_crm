<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\PriceList;

class ComZeappsCrmPriceRateTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_price_list_rate', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_pricelist')->default(0);
            $table->integer('id_category')->default(0);
            $table->double('percentage')->default(0);
            $table->string('accounting_number', 255)->default("");
            $table->integer('id_taxe')->default(0);
            $table->decimal('value_taxe', 8, 2)->default(0);
            $table->tinyInteger('is_updated')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('id_pricelist');
            $table->index('id_category');
            $table->index(array('id_pricelist', 'id_category'));
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_price_list_rate');
    }
}
