<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class InvoiceActivities extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_invoice_activities';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_invoice')->default(0);
        $this->fieldModelInfo->integer('id_user', false, true)->default(0);
        $this->fieldModelInfo->string('name_user', 255)->default("");
        $this->fieldModelInfo->string('libelle', 255)->default("");
        $this->fieldModelInfo->text('description');
        $this->fieldModelInfo->string('status')->default("");
        $this->fieldModelInfo->integer('id_type', false, true)->default(0);
        $this->fieldModelInfo->string('label_type')->default("");
        $this->fieldModelInfo->timestamp('date')->nullable();
        $this->fieldModelInfo->timestamp('deadline')->nullable();
        $this->fieldModelInfo->timestamp('reminder')->nullable();
        $this->fieldModelInfo->timestamp('validation')->nullable();
        $this->fieldModelInfo->timestamps();
        $this->fieldModelInfo->softDeletes();

        parent::__construct($attributes);
    }

    public static function getSchema() {
        return $schema = Capsule::schema()->getColumnListing(self::$_table) ;
    }

    public function save(array $options = []) {

        /******** clean data **********/
        $this->fieldModelInfo->cleanData($this) ;


        /**** to delete unwanted field ****/
        $this->fieldModelInfo->removeFieldUnwanted($this) ;

        return parent::save($options);
    }
}