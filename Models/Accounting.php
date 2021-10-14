<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class Accounting extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_accounting';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;


        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_invoice')->default(0);
        $this->fieldModelInfo->string('accounting_number', 255)->default("");
        $this->fieldModelInfo->string('label', 1023)->default("");
        $this->fieldModelInfo->string('invoice_num', 255)->default("");
        $this->fieldModelInfo->decimal('debit', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('credit', 8, 2)->default(0);
        $this->fieldModelInfo->string('journal', 32)->default("");
        $this->fieldModelInfo->timestamp('date_invoice')->nullable();
        $this->fieldModelInfo->timestamps();
        
        

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