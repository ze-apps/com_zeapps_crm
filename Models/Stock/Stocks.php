<?php

namespace App\com_zeapps_crm\Models\Stock;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class Stocks extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_stocks';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_stock', false)->default(0);
        $this->fieldModelInfo->string('label', 255)->default("");
        $this->fieldModelInfo->string('ref', 255)->default("");
        $this->fieldModelInfo->decimal('value_ht', 9, 2)->default(0);
        $this->fieldModelInfo->integer('id_warehouse', false)->default(0);
        $this->fieldModelInfo->string('warehouse')->default("");
        $this->fieldModelInfo->integer('resupply_delay')->default(0);
        $this->fieldModelInfo->integer('resupply_unit')->default(0);
        $this->fieldModelInfo->decimal('total', 9, 2)->default(0);
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