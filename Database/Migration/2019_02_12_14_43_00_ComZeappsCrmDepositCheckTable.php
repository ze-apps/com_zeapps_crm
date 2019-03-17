<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\PriceList;

class ComZeappsCrmDepositCheckTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_deposit_check', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('deposit_number')->default(0);
            $table->string('deposit_number_of_bank', 255)->default("");
            $table->date('date_deposit')->default(null)->nullable();
            $table->date('date_in_bank')->default(null)->nullable();
            $table->string('status', 2)->default("");
            $table->string('type_deposit', 20)->default("");
            $table->decimal('amount', 9, 2)->default(0);
            $table->integer('nb_lines')->default(0);
            $table->string('pdf', 255)->default("");

            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_deposit_check');
    }
}
