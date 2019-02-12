<?php

namespace App\com_zeapps_crm\Models\Delivery;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Capsule\Manager as Capsule;


use Zeapps\Core\ModelHelper;

class DeliveryTaxes extends Model
{

    static protected $_table = 'com_zeapps_crm_delivery_taxes';
    protected $table;

    protected $fieldModelInfo;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_delivery')->default(0);
        $this->fieldModelInfo->decimal('base_tax', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('value_rate_tax', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('amount_tax', 8, 2)->default(0);
        $this->fieldModelInfo->string('accounting_number')->default("");

        $this->fieldModelInfo->string('accounting_number_taxe')->default("");
        $this->fieldModelInfo->integer('id_taxe')->default(0);
        $this->fieldModelInfo->decimal('total_ttc', 8, 2)->default(0);

        $this->fieldModelInfo->timestamps();

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

        return parent::save($options);
    }


    public static function getAccountingFromDelivery($id_delivery) {
        return self::where("id_delivery", $id_delivery)->get() ;
    }

    public static function getTableTaxe($id_delivery) {
        $arrTaxes = [];

        $accountingLines = self::getAccountingFromDelivery($id_delivery);

        // calcul le résumé des taxes
        foreach ($accountingLines as $accountingLine) {
            $vat_not_found = true ;
            foreach ($arrTaxes as $arrTaxe) {
                if ($arrTaxe["rate_tax"] == $accountingLine["value_rate_tax"]) {
                    $vat_not_found = false ;

                    $arrTaxe["base_tax"] += $accountingLine["base_tax"] ;
                    $arrTaxe["amount_tax"] = round($arrTaxe["base_tax"] * ($arrTaxe["rate_tax"] * 1) / 100, 2) ;
                    $arrTaxe["total_with_tax"] = $arrTaxe["base_tax"] + $arrTaxe["base_tax"] ;

                    break;
                }
            }

            if ($vat_not_found) {
                $arrTaxe = array();
                $arrTaxe["rate_tax"] = $accountingLine["value_rate_tax"] ;
                $arrTaxe["base_tax"] = $accountingLine["base_tax"] ;
                $arrTaxe["amount_tax"] = round($arrTaxe["base_tax"] * ($arrTaxe["rate_tax"] * 1) / 100, 2) ;
                $arrTaxe["total_with_tax"] = $arrTaxe["base_tax"] + $arrTaxe["base_tax"] ;

                $arrTaxes[] = $arrTaxe ;
            }
        }


        return $arrTaxes ;
    }
}