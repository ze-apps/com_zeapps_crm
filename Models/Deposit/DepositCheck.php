<?php

namespace App\com_zeapps_crm\Models\Deposit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;
use App\com_zeapps_crm\Models\Deposit\DepositCheckLine ;

class DepositCheck extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_deposit_check';
    protected $table;

    protected $fieldModelInfo;

    public static $listStatus = array("EN" => "Encaissé", "TE" => "Terminé", "OU" => "Ouvert", "AT" => "A transférer à la banque") ;

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


    public static function addCheck($check_issuer, $bank_check_number, $amount, $type_deposit, $id_payment, $id_invoice = 0) {
        $idDeposit = 0 ;
        $nbMaximumCheck = 50 ;
        $createDeposit = true ;


        // recherche la remise de chèque
        $deposit = self::where("type_deposit", $type_deposit)->where("status", "OU")->first();

        if ($deposit) {
            $nbCheck = DepositCheckLine::where("id_deposit", $deposit->id)->count() ;
            $idDeposit = $deposit->id;

            if ($nbCheck < $nbMaximumCheck) {
                $createDeposit = false ;
            } else {
                $deposit->status = 'AT' ;
                $deposit->save() ;
            }
        }


        if ($createDeposit) {
            $deposit_number = 1 ;
            $lastNumber = self::orderBy("deposit_number", "DESC")->first();
            if ($lastNumber) {
                $deposit_number = $lastNumber->deposit_number + 1 ;
            }


            $objDeposit = new self();
            $objDeposit->deposit_number = $deposit_number ;
            $objDeposit->deposit_number_of_bank = "" ;
            $objDeposit->date_deposit = date("Y-m-d") ;
            $objDeposit->date_in_bank = null ;
            $objDeposit->status = "OU" ;
            $objDeposit->type_deposit = $type_deposit ;
            $objDeposit->save();

            $idDeposit = $objDeposit->id;
        }


        // save check
        $objDepositCheckLine = new DepositCheckLine() ;
        $objDepositCheckLine->id_deposit = $idDeposit ;
        $objDepositCheckLine->id_invoice = $id_invoice ;
        $objDepositCheckLine->id_payment = $id_payment ;
        $objDepositCheckLine->check_issuer = $check_issuer ;
        $objDepositCheckLine->bank_check_number = $bank_check_number ;
        $objDepositCheckLine->amount = $amount ;
        $objDepositCheckLine->save() ;


        return $idDeposit ;
    }
}