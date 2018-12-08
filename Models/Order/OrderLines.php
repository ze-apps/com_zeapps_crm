<?php

namespace App\com_zeapps_crm\Models\Order;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class OrderLines extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_order_lines';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_order')->default(0);
        $this->fieldModelInfo->integer('id_parent')->default(0);
        $this->fieldModelInfo->string('type', 255)->default("");
        $this->fieldModelInfo->integer('id_product')->default(0);
        $this->fieldModelInfo->string('ref', 255)->default("");
        $this->fieldModelInfo->string('designation_title', 255);
        $this->fieldModelInfo->text('designation_desc');
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

        $this->fieldModelInfo->timestamps();
        $this->fieldModelInfo->softDeletes();

        parent::__construct($attributes);
    }


    public static function getFromOrder($id_order) {
        $lines = OrderLines::where('id_order', $id_order)
            ->where("id_parent", 0)
            ->get() ;

        foreach ($lines as &$line) {
            $line->sublines = self::getSubLine($line->id) ;
        }

        return $lines ;
    }

    public static function getSubLine($idLine) {
        $sublines = OrderLines::where("id_parent", $idLine)->orderBy("sort")->get() ;

        foreach ($sublines as &$subline) {
            $subline->sublines = self::getSubLine($subline->id) ;
        }

        return $sublines ;
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

    public static function updateOldTable($id_order, $sort)
    {
        Capsule::statement('UPDATE com_zeapps_crm_order_lines SET sort = (sort-1) WHERE id_order = ' . $id_order . ' AND sort > ' . $sort);
    }

    public static function updateNewTable($id_order, $sort)
    {
        Capsule::statement('UPDATE com_zeapps_crm_order_lines SET sort = (sort+1) WHERE id_order = ' . $id_order . ' AND sort >= ' . $sort);
    }

    public static function deleteLine($id) {
        $sublines = OrderLines::where("id_parent", $id)->get() ;

        foreach ($sublines as $subline) {
            self::deleteLine($subline->id);
        }

        OrderLines::where("id", $id)->delete() ;
    }
}