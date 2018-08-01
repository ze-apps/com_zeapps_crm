<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class ProductStocks extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_product_stocks';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->string('ref', 255)->default("");
        $this->fieldModelInfo->string('label', 255)->default("");
        $this->fieldModelInfo->decimal('value_ht', 8, 2)->default(0);
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


    public static function getStock($where = array())
    {
        $where['com_zeapps_crm_product_stocks.deleted_at'] = null;

        $stock = Capsule::table('com_zeapps_crm_product_stocks')
            ->leftJoin('com_zeapps_crm_stocks', 'com_zeapps_crm_stocks.id_stock', '=', 'com_zeapps_crm_product_stocks.id')
            ->selectRaw('com_zeapps_crm_product_stocks.*, 
                com_zeapps_crm_product_stocks.id as id,
                com_zeapps_crm_product_stocks.label as label,
                sum(com_zeapps_crm_stocks.total) as total');

        foreach ($where as $key => $value) {
            $stock = $stock->whereRaw($key . " = '" . str_replace("'", "''", $value) . "'") ;
        }

        return $stock->first();
    }
}