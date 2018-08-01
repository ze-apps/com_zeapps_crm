<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class AccountingEntries extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_accounting_entries';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_invoice', false)->default(0);
        $this->fieldModelInfo->string('accounting_number')->default("");
        $this->fieldModelInfo->string('number_document')->default("");
        $this->fieldModelInfo->string('label')->default("");
        $this->fieldModelInfo->float('debit', 9, 2)->default(0.0);
        $this->fieldModelInfo->float('credit', 9, 2)->default(0.0);
        $this->fieldModelInfo->string('code_journal')->default("");
        $this->fieldModelInfo->timestamp('date_writing');
        $this->fieldModelInfo->timestamp('date_limit');
        

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