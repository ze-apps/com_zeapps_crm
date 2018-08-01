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
        $this->fieldModelInfoincrements('id');
        $this->fieldModelInfointeger('id_invoice', false)->default(0);
        $this->fieldModelInfostring('accounting_number')->default("");
        $this->fieldModelInfostring('number_document')->default("");
        $this->fieldModelInfostring('label')->default("");
        $this->fieldModelInfofloat('debit', 9, 2)->default(0.0);
        $this->fieldModelInfofloat('credit', 9, 2)->default(0.0);
        $this->fieldModelInfostring('code_journal')->default("");
        $this->fieldModelInfotimestamp('date_writing');
        $this->fieldModelInfotimestamp('date_limit');
        

        parent::__construct($attributes);
    }

    public static function getSchema() {
        return $schema = Capsule::schema()->getColumnListing(self::$_table) ;
    }

    public function save(array $options = []) {

        /**** to delete unwanted field ****/
        $schema = self::getSchema();
        foreach ($this->getAttributes() as $key => $value) {
            if (!in_array($key, $schema)) {
                //echo $key . "\n" ;
                unset($this->$key);
            }
        }
        /**** end to delete unwanted field ****/

        return parent::save($options);
    }
}