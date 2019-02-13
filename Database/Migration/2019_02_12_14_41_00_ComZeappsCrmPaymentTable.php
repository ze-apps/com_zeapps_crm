<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\PriceList;

class ComZeappsCrmPaymentTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_company')->default(0);
            $table->integer('id_contact')->default(0);
            $table->decimal('total', 9, 2)->default(0);
            $table->date('date_payment')->default(null)->nullable();
            $table->tinyInteger('type_payment')->default(0);
            $table->string('type_payment_label', 255)->default("");
            $table->string('bank_check_number', 255)->default("");
            $table->string('check_issuer', 255)->default("");
            $table->integer('id_deposit_checks')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_payment');
    }
}
