<?php

namespace App\com_zeapps_crm\Models\Invoice;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Capsule\Manager as Capsule;


use Zeapps\Core\ModelHelper;

class InvoiceTaxes extends Model
{
    static protected $_table = 'com_zeapps_crm_invoice_taxes';
    protected $table;

    protected $fieldModelInfo;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_invoice')->default(0);
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

    public static function getAccountingFromInvoice($id_invoice) {
        return self::where("id_invoice", $id_invoice)->get() ;
    }

    public static function getTableTaxe($id_invoice) {
        $arrTaxes = [];

        $accountingLines = self::getAccountingFromInvoice($id_invoice);

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