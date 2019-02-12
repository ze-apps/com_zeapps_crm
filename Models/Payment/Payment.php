<?php

namespace App\com_zeapps_crm\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class Payment extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_payment';
    protected $table;

    protected $fieldModelInfo;

    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_company')->default(0);
        $this->fieldModelInfo->integer('id_contact')->default(0);
        $this->fieldModelInfo->decimal('total', 9, 2)->default(0);
        $this->fieldModelInfo->date('date_payment')->default(null);
        $this->fieldModelInfo->tinyInteger('type_payment')->default(0);
        $this->fieldModelInfo->string('bank_check_number', 255)->default("");
        $this->fieldModelInfo->string('check_issuer', 255)->default("");
        $this->fieldModelInfo->integer('id_deposit_checks')->default(0);
        $this->fieldModelInfo->timestamps();
        $this->fieldModelInfo->softDeletes();

        parent::__construct($attributes);
    }

    public static function getSchema()
    {
        return $schema = Capsule::schema()->getColumnListing(self::$_table);
    }


    public function save(array $options = [])
    {
        /******** clean data **********/
        $this->fieldModelInfo->cleanData($this);


        /**** to delete unwanted field ****/
        $this->fieldModelInfo->removeFieldUnwanted($this);

        $return = parent::save($options);

        return $return;
    }
}