<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComZeappsCrmInvoiceActivitiesTable extends Migration
{

    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_invoice_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_invoice')->default(0);
            $table->integer('id_user', false, true)->default(0);
            $table->string('name_user', 255)->default("");
            $table->string('libelle', 255)->default("");
            $table->text('description');
            $table->string('status')->default("");
            $table->integer('id_type', false, true)->default(0);
            $table->string('label_type')->default("");
            $table->timestamp('date')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->timestamp('reminder')->nullable();
            $table->timestamp('validation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('com_zeapps_crm_invoice_activities');
    }
}
