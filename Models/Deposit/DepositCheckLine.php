<?php

namespace App\com_zeapps_crm\Models\Deposit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

use App\com_zeapps_crm\Models\Deposit\DepositCheck;

class DepositCheckLine extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_deposit_check_line';
    protected $table;

    protected $fieldModelInfo;

    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_deposit')->default(0);
        $this->fieldModelInfo->integer('id_invoice')->default(0);
        $this->fieldModelInfo->integer('id_payment')->default(0);
        $this->fieldModelInfo->string('check_issuer', 255)->default("");
        $this->fieldModelInfo->string('bank_check_number', 255)->default("");
        $this->fieldModelInfo->decimal('amount', 9, 2)->default(0);
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

        DepositCheck::updateAmount($this->id_deposit);


        return $return;
    }

    public function delete() {

        if (is_null($this->getKeyName())) {
            throw new Exception('No primary key defined on model.');
        }

        $DepositCheckLine = self::find($this->id) ;


        $return = parent::delete();

        if ($DepositCheckLine) {
            DepositCheck::updateAmount($DepositCheckLine->id_deposit);
        }

        return $return;
    }
}