<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

class StockMovements extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_stock_movements';
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


    public static function avg($where = array())
    {
        $ret = Capsule::table('com_zeapps_crm_stock_movements') ;
        $ret = $ret->selectRaw("sum(zeapps_stock_movements.qty) as average");

        $queryWhere = "deleted_at is null
                and qty < 0
                and ignored = '0'
                and date_mvt BETWEEN CURDATE() - INTERVAL 90 DAY AND CURDATE() + INTERVAL 1 DAY
                and id_stock = " . $where['id_stock'];

        if (isset($where['id_warehouse'])) {
            $queryWhere .= " and id_warehouse = " . $where['id_warehouse'];
        }

        $ret = $ret->whereRaw($queryWhere);
        $res = $ret->get();


        if ($res) {
            $w = array('deleted_at' => null, 'id_stock' => $where['id_stock'], 'qty <' => 0);
            if (isset($where['id_warehouse'])) {
                $w['id_warehouse'] = $where['id_warehouse'];
            }


            $ret = StockMovements::select("date_mvt") ;

            foreach ($w as $key => $value) {
                $ret = $ret->where($key, $value) ;
            }
            $ret = $ret->get();

            if ($ret) {
                $first = $ret[0]->date_mvt;
                $now = time();
                $first = strtotime($first);
                $diff = (($now - $first) / 86400) < 90 ? (($now - $first) / 86400) : 90; // 86400 = 60*60*24
                $diff = $diff < 1 ? 1 : $diff;
            } else {
                $diff = 90;
            }

            return abs($res[0]->average / $diff);
        } else {
            return 0;
        }
    }

    public static function last_year($where = array())
    {
        $ret = Capsule::table('com_zeapps_crm_stock_movements') ;
        $ret = $ret->selectRaw("date_mvt, qty");

        $queryWhere = "date_mvt > date_sub(CURDATE(),INTERVAL 12 MONTH) 
                  and deleted_at is null 
                  and id_stock = " . $where['id_stock'] ;

        if (isset($where['id_warehouse'])) {
            $queryWhere .= ' and id_warehouse = ' . $where['id_warehouse'];
        }

        $ret = $ret->whereRaw($queryWhere);
        $res = $ret->get();


        return $res->get();
    }

    public static function last_months($where = array())
    {
        $ret = Capsule::table('com_zeapps_crm_stock_movements') ;
        $ret = $ret->selectRaw("date_mvt, qty");

        $queryWhere = "date_mvt > date_sub(CURDATE(),INTERVAL 90 DAY) 
                  and deleted_at is null 
                  and id_stock = " . $where['id_stock'] ;

        if (isset($where['id_warehouse'])) {
            $queryWhere .= ' and id_warehouse = ' . $where['id_warehouse'];
        }

        $ret = $ret->whereRaw($queryWhere);
        $res = $ret->get();


        return $res->get();
    }

    public static function last_month($where = array())
    {
        $ret = Capsule::table('com_zeapps_crm_stock_movements') ;
        $ret = $ret->selectRaw("date_mvt, qty");

        $queryWhere = "date_mvt > date_sub(CURDATE(),INTERVAL 30 DAY) 
                  and deleted_at is null 
                  and id_stock = " . $where['id_stock'] ;

        if (isset($where['id_warehouse'])) {
            $queryWhere .= ' and id_warehouse = ' . $where['id_warehouse'];
        }

        $ret = $ret->whereRaw($queryWhere);
        $res = $ret->get();


        return $res->get();
    }

    public static function last_week($where = array())
    {
        $ret = Capsule::table('com_zeapps_crm_stock_movements') ;
        $ret = $ret->selectRaw("date_mvt, qty");

        $queryWhere = "date_mvt > date_sub(CURDATE(),INTERVAL 7 DAY) 
                  and deleted_at is null 
                  and id_stock = " . $where['id_stock'] ;

        if (isset($where['id_warehouse'])) {
            $queryWhere .= ' and id_warehouse = ' . $where['id_warehouse'];
        }

        $ret = $ret->whereRaw($queryWhere);
        $res = $ret->get();


        return $res->get();
    }
}