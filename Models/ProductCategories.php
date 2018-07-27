<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategories extends Model {
    use SoftDeletes;

    protected $table = 'com_zeapps_crm_product_categories';


    public static function getSubCatIds_r($id = null){
        $ids = [];
        $ids[] = $id;

        if($categories = ProductCategories::where("id_parent", $id)->get()){
            foreach($categories as $category){
                if($tmp = self::getSubCatIds_r($category->id)){
                    $ids = array_merge($ids, $tmp);
                }
            }
        }

        return $ids;
    }
}