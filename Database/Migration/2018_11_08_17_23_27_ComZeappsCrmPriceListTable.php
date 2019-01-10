<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\PriceList;

class ComZeappsCrmPriceListTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_price_list', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label', 255)->default("");
            $table->tinyInteger('default')->default(0);
            $table->tinyInteger('type_pricelist')->default(0);
            $table->double('percentage')->default(0);
            $table->tinyInteger('active')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // insert default PriceList
        $objPriceList = new PriceList() ;
        $objPriceList->label = "Standard" ;
        $objPriceList->default = 1 ;
        $objPriceList->active = 1 ;
        $objPriceList->save() ;
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_price_list');
    }
}
