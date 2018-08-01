<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class ProductProducts extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_product_products';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_cat')->default(0);
        $this->fieldModelInfo->integer('id_stock')->default(0);
        $this->fieldModelInfo->tinyInteger('compose')->default(0);
        $this->fieldModelInfo->string('ref', 255)->default("");
        $this->fieldModelInfo->string('name', 255)->default("");
        $this->fieldModelInfo->text('description');
        $this->fieldModelInfo->decimal('price_ht', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('price_ttc', 8, 2)->default(0);
        $this->fieldModelInfo->tinyInteger('auto')->default(0);
        $this->fieldModelInfo->integer('id_taxe')->default(0);
        $this->fieldModelInfo->decimal('value_taxe', 8, 2)->default(0);
        $this->fieldModelInfo->string('accounting_number', 255)->default("");
        $this->fieldModelInfo->mediumtext('extra');
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

    public static function archive_products($id_arr = NULL){
        if($id_arr){
            foreach($id_arr as $id){
                self::where("id_cat", $id)->update(array('id_cat' => -1)) ;
            }
        }
        return;
    }
}