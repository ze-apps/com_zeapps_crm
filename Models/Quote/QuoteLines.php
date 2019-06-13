<?php

namespace App\com_zeapps_crm\Models\Quote;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\Quote\QuoteLinePriceList;

use Zeapps\Core\ModelHelper;

class QuoteLines extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_quote_lines';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_quote')->default(0);
        $this->fieldModelInfo->integer('id_parent')->default(0);
        $this->fieldModelInfo->string('type', 255)->default("");
        $this->fieldModelInfo->integer('id_product')->default(0);
        $this->fieldModelInfo->string('ref', 255)->default("");
        $this->fieldModelInfo->string('designation_title', 255)->default("");
        $this->fieldModelInfo->text('designation_desc')->default("");
        $this->fieldModelInfo->decimal('qty', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('discount', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('price_unit', 8, 2)->default(0);
        $this->fieldModelInfo->integer('id_taxe', false)->default(0);
        $this->fieldModelInfo->decimal('value_taxe', 8, 2)->default(0);
        $this->fieldModelInfo->string('accounting_number')->default("");
        $this->fieldModelInfo->decimal('total_ht', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('total_ttc', 8, 2)->default(0);
        $this->fieldModelInfo->tinyInteger('update_price_from_subline', false)->default(0);
        $this->fieldModelInfo->tinyInteger('show_subline', false)->default(0);
        $this->fieldModelInfo->decimal('price_unit_ttc_subline', 8, 2)->default(0);
        $this->fieldModelInfo->integer('sort')->default(0);
        $this->fieldModelInfo->tinyInteger('discount_prohibited')->default(0);
        $this->fieldModelInfo->text('json')->default('');

        $this->fieldModelInfo->timestamps();
        $this->fieldModelInfo->softDeletes();

        parent::__construct($attributes);
    }

    public static function getFromQuote($id_quote) {
        $lines = QuoteLines::where('id_quote', $id_quote)
            ->where("id_parent", 0)
            ->orderBy("sort")
            ->orderBy("sort")->get() ;

        foreach ($lines as &$line) {
            // load sublines
            $line->sublines = self::getSubLine($line->id) ;


            // search if line has multiple taxe in subline
            if ($line->sublines && count($line->sublines)) {
                $hasMultipleTaxe = self::_hasMultipleTaxe($line) ;

                $line->hasMultipleTaxe = $hasMultipleTaxe["hasMultipleTaxe"] ;
                $line->listIdTaxe = $hasMultipleTaxe["listIdTaxe"] ;
                $line->listTaxe = $hasMultipleTaxe["listTaxe"] ;

                if ($line->hasMultipleTaxe) {
                    $line->taxeLabel = "*" ;
                } else {
                    $line->taxeLabel = $line->listTaxe[0]["value_taxe"];
                }

            } else {
                $line->hasMultipleTaxe = false ;
                $line->listIdTaxe = array($line->id_taxe) ;
                $line->taxeLabel = $line->value_taxe ;
            }


            // load price list
            $line->priceList = QuoteLinePriceList::where("id_quote_line", $line->id)->get();
        }

        return $lines ;
    }

    public static function getSubLine($idLine) {
        $sublines = QuoteLines::where("id_parent", $idLine)
            ->orderBy("sort")
            ->get() ;

        foreach ($sublines as &$subline) {
            $subline->sublines = self::getSubLine($subline->id) ;

            // search if line has multiple taxe in subline
            if ($subline->sublines && count($subline->sublines)) {
                $hasMultipleTaxe = self::_hasMultipleTaxe($subline) ;
                $subline->hasMultipleTaxe = $hasMultipleTaxe["hasMultipleTaxe"] ;
                $subline->listIdTaxe = $hasMultipleTaxe["listIdTaxe"] ;
                $subline->listTaxe = $hasMultipleTaxe["listTaxe"] ;
                if ($subline->hasMultipleTaxe) {
                    $subline->taxeLabel = "*" ;
                } else {
                    $subline->taxeLabel = $subline->listTaxe[0]["value_taxe"];
                }
            } else {
                $subline->hasMultipleTaxe = false ;
                $subline->listIdTaxe = array($subline->id_taxe) ;
                $subline->taxeLabel = array($subline->value_taxe) ;
            }
        }

        return $sublines ;
    }

    private static function _hasMultipleTaxe($line)
    {
        $listIdTaxe = array();
        $listTaxe = array();
        $hasMultipleTaxe = false ;

        if (isset($line->sublines) && count($line->sublines)) {
            foreach ($line->sublines as $subline) {
                $_hasMultipleTaxe = self::_hasMultipleTaxe($subline) ;
                $listIdTaxe = array_merge($listIdTaxe, $_hasMultipleTaxe["listIdTaxe"]);
                $listTaxe = array_merge($listTaxe, $_hasMultipleTaxe["listTaxe"]);
            }


            $listIdTaxe = array_unique($listIdTaxe);

            if (count($listIdTaxe) > 1) {
                $hasMultipleTaxe = true ;
            }

        } else {
            $listIdTaxe[] = $line->id_taxe ;
            $listTaxe[] = array("id_taxe" => $line->id_taxe, "value_taxe" => $line->value_taxe) ;
        }


        return array("listIdTaxe" => $listIdTaxe, "listTaxe" => $listTaxe, "hasMultipleTaxe" => $hasMultipleTaxe);
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

    public static function updateOldTable($id_quote, $sort)
    {
        Capsule::statement('UPDATE com_zeapps_crm_quote_lines SET sort = (sort-1) WHERE id_quote = ' . $id_quote . ' AND sort > ' . $sort);
    }

    public static function updateNewTable($id_quote, $sort)
    {
        Capsule::statement('UPDATE com_zeapps_crm_quote_lines SET sort = (sort+1) WHERE id_quote = ' . $id_quote . ' AND sort >= ' . $sort);
    }


    public static function deleteLine($id) {
        $sublines = QuoteLines::where("id_parent", $id)->get() ;

        foreach ($sublines as $subline) {
            self::deleteLine($subline->id);
        }

        QuoteLines::where("id", $id)->delete() ;
    }
}