<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductProducts extends Model {
    use SoftDeletes;

    protected $table = 'com_zeapps_crm_product_products';

    public static function archive_products($id_arr = NULL){
        if($id_arr){
            foreach($id_arr as $id){
                self::where("id_cat", $id)->update(array('id_cat' => -1)) ;
            }
        }
        return;
    }
}