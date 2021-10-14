<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\PriceList;

class ComZeappsCrmPaymentLineTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_payment_line', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_payment')->default(0);
            $table->integer('id_invoice')->default(0);
            $table->decimal('amount', 9, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('id_payment');
            $table->index('id_invoice');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_payment_line');
    }
}
