<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Models\Config;

class ComZeappsCrmV9
{
    public function up()
    {
        Capsule::schema()->create('com_zeapps_crm_activity_connection', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table', 255)->default("");
            $table->integer('id_table', false, true)->default(0);
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
    }
}
