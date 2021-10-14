<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class CreditBalances extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_credit_balances';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->tinyInteger('id_invoice', false)->default(0);
        $this->fieldModelInfo->tinyInteger('numerotation', false)->default(0);
        $this->fieldModelInfo->tinyInteger('id_company', false)->default(0);
        $this->fieldModelInfo->tinyInteger('name_company', false)->default(0);
        $this->fieldModelInfo->tinyInteger('id_contact', false)->default(0);
        $this->fieldModelInfo->tinyInteger('name_contact', false)->default(0);
        $this->fieldModelInfo->tinyInteger('due_date', false)->default(0);
        $this->fieldModelInfo->tinyInteger('total', false)->default(0);
        $this->fieldModelInfo->tinyInteger('paid', false)->default(0);
        $this->fieldModelInfo->tinyInteger('left_to_pay', false)->default(0);
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