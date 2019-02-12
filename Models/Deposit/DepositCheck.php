<?php

namespace App\com_zeapps_crm\Models\Deposit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class DepositCheck extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_deposit_check';
    protected $table;

    protected $fieldModelInfo;

    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('deposit_number')->default(0);
        $this->fieldModelInfo->string('deposit_number_of_bank', 255)->default("");
        $this->fieldModelInfo->date('date_deposit')->default(null);
        $this->fieldModelInfo->date('date_in_bank')->default(null);
        $this->fieldModelInfo->string('status', 2)->default("");
        $this->fieldModelInfo->string('type_deposit', 20)->default("");
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