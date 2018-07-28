<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

class ProductStocks extends Model
{
    use SoftDeletes;

    protected $table = 'com_zeapps_crm_product_stocks';


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