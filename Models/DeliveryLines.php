<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class DeliveryLines extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_delivery_lines';
    protected $table ;

    protected $fieldModelInfo ;


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

    public static function updateOldTable($id_quote, $sort)
    {
        Capsule::statement('UPDATE com_zeapps_crm_delivery_lines SET sort = (sort-1) WHERE id_quote = ' . $id_quote . ' AND sort > ' . $sort);
    }

    public static function updateNewTable($id_quote, $sort)
    {
        Capsule::statement('UPDATE com_zeapps_crm_delivery_lines SET sort = (sort+1) WHERE id_quote = ' . $id_quote . ' AND sort >= ' . $sort);
    }
}