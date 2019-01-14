<?php

namespace App\com_zeapps_crm\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class ProductCategories extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_product_categories';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_parent')->default(0);
        $this->fieldModelInfo->string('name', 255)->default("");
        $this->fieldModelInfo->integer('nb_products')->default(0);
        $this->fieldModelInfo->integer('nb_products_r')->default(0);
        $this->fieldModelInfo->integer('sort')->default(0);
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


    public static function getRootCategory(){
        $root = array();
        $root["name"] = 'racine';
        $root["id"] = '0';
        $root["id_parent"] = '-2';
        $root["open"] = false;

        return $root;
    }

    public static function getArchiveCategory(){
        $archive = array();
        $archive["name"] = 'archive';
        $archive["id"] = '-1';
        $archive["id_parent"] = '-2';
        $archive["open"] = false;

        return $archive;
    }


    public static function getSubCatIds_r($id = null)
    {
        $ids = [];
        $ids[] = $id;

        if ($categories = ProductCategories::where("id_parent", $id)->get()) {
            foreach ($categories as $category) {
                if ($tmp = self::getSubCatIds_r($category->id)) {
                    $ids = array_merge($ids, $tmp);
                }
            }
        }

        return $ids;
    }

    public static function delete_r($id = NULL, $categories = NULL)
    {
        if ($id) {
            if (!$categories) {
                $categories = ProductCategories::get();
            }
            $id_arr = array($id);
            foreach ($categories as $category) {
                if ($category->id_parent == $id) {
                    $res = self::delete_r($category->id, $categories);
                    foreach ($res as $entry) {
                        array_push($id_arr, $entry);
                    }
                }
            }

            ProductCategories::where("id", $id)->delete();
            return $id_arr;
        }
        return false;
    }

    public static function removeProductIn($id = array(), $parent = false, $qty = 1){
        if($id) {
            $category = ProductCategories::where("id", $id)->first();
            if(!$parent) {
                $category->nb_products = $category->nb_products - $qty ;
                $category->save() ;
            } else{
                $category->nb_products_r = $category->nb_products_r - $qty ;
                $category->save() ;
            }

            if($category->id_parent > 0){
                self::removeProductIn($category->id_parent, true, $qty);
            }
        }
        return;
    }

    public static function newProductIn($id = array(), $parent = false){
        if($id) {
            $category = ProductCategories::where("id", $id)->first();
            if(!$parent) {
                $category->nb_products = $category->nb_products + 1 ;
                $category->save();
            } else {
                $category->nb_products_r = $category->nb_products_r + 1 ;
                $category->save();
            }

            if($category->id_parent > 0){
                self::newProductIn($category->id_parent, true);
            }
        }
        return;
    }
}