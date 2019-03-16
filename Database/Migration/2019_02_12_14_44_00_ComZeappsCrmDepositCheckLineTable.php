<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\PriceList;

class ComZeappsCrmDepositCheckLineTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_deposit_check_line', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_deposit')->default(0);
            $table->integer('id_invoice')->default(0);
            $table->integer('id_payment')->default(0);
            $table->string('check_issuer', 255)->default("");
            $table->string('bank_check_number', 255)->default("");
            $table->decimal('amount', 9, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('id_deposit');
            $table->index('id_invoice');
            $table->index('id_payment');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_deposit_check_line');
    }
}
