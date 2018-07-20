<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmProductStocksTable extends Migration
{

    public function up()
    {
       Capsule::schema()->create('com_zeapps_crm_product_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ref', 255);
            $table->string('label', 255);
            $table->decimal('value_ht', 8, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_product_stocks');
    }
}
