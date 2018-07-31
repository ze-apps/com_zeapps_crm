<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

class ProductStocks extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_product_stocks';
    protected $table ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        parent::__construct($attributes);
    }

    public static function getSchema() {
        return $schema = Capsule::schema()->getColumnListing(self::$_table) ;
    }

    public function save(array $options = []) {

        /**** to delete unwanted field ****/
        $schema = self::getSchema();
        foreach ($this->getAttributes() as $key => $value) {
            if (!in_array($key, $schema)) {
                //echo $key . "\n" ;
                unset($this->$key);
            }
        }
        /**** end to delete unwanted field ****/

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