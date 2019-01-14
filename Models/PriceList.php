<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class PriceList extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_price_list';
    protected $table;

    protected $fieldModelInfo;

    static protected $typePriceListPrice = array(0=>"Prix", 1=>"Pourcentage");


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->string('label', 255)->default("");
        $this->fieldModelInfo->tinyInteger('default')->default(0);
        $this->fieldModelInfo->tinyInteger('type_pricelist')->default(0);
        $this->fieldModelInfo->double('percentage')->default(0);
        $this->fieldModelInfo->tinyInteger('active')->default(0);
        $this->fieldModelInfo->tinyInteger('is_updated')->default(0);
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


        /**** to launch the price update ****/
        $this->is_updated = 1 ;

        return parent::save($options);
    }

    public static function getPriceListTypes() {
        return self::$typePriceListPrice;
    }

    public static function getPriceListType($id) {
        $label = "" ;

        if (isset(self::$typePriceListPrice[$id])) {
            $label = self::$typePriceListPrice[$id] ;
        }

        return $label;
    }
}