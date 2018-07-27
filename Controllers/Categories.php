<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\ProductCategories as CategoriesModel;

class Categories extends Controller
{
    public function form(){
        $data = array();
        return view("product/form_category", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }




    public function get_tree(){
        $categories = CategoriesModel::orderBy("sort", "ASC")->get();

        if ($categories == false) {
            echo json_encode(array());
        } else {
            $result = $this->_build_tree($categories);
            echo json_encode($result);
        }
    }

    private function _build_tree($categories, $id = -2){
        $result = array();

        foreach($categories as $category){
            if($category->id_parent == $id){

                $tmp = $category;
                $res = $this->_build_tree($categories, $category->id);
                if(!empty($res)) {
                    $tmp->branches = $res;
                }
                $tmp->open = false;
                $result[] = $tmp;
            }
        }

        return $result;
    }

}