<?php

namespace App\com_zeapps_crm\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;
use App\com_zeapps_contact\Models\Modalities;
use App\com_zeapps_contact\Models\ModalitiesLang;
use App\com_zeapps_crm\Models\Deposit\DepositCheck;

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
        $this->fieldModelInfo->string('type_payment_label', 255)->default("");
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


    public function save(array $options = [], $saveCheck = true)
    {
        $savePaymentCheck = false ;
        $id_cheque = "" ;
        $objModalities = Modalities::find($this->type_payment) ;
        $objModalitiesLang = ModalitiesLang::where("id_modality", $this->type_payment)->where("id_lang", 1)->first() ;
        if ($objModalities) {
            if ($objModalities->type_modality == 1 && $objModalities->situation == 1) {
                $savePaymentCheck = true;
                $id_cheque = $objModalities->id_cheque ;
            }
        }
        if ($objModalitiesLang) {
            $this->type_payment_label = $objModalitiesLang->label ;
        }


        /******** clean data **********/
        $this->fieldModelInfo->cleanData($this);


        /**** to delete unwanted field ****/
        $this->fieldModelInfo->removeFieldUnwanted($this);

        $return = parent::save($options);



        /************** save deposit if payment is check ***************/
        if ($saveCheck && $savePaymentCheck) {
            $this->id_deposit_checks = DepositCheck::addCheck($this->check_issuer, $this->bank_check_number, $this->total, $id_cheque, $this->id, 0);
            $return = parent::save($options);
        }



        return $return;
    }
}