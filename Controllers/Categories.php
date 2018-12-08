<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\Product\ProductCategories as CategoriesModel;
use App\com_zeapps_crm\Models\Product\Products;

class Categories extends Controller
{
    public function form()
    {
        $data = array();
        return view("product/form_category", $data, BASEPATH . 'App/com_zeapps_crm/views/');
    }


    public function get_tree()
    {
        $categories = CategoriesModel::orderBy("sort", "ASC")->get()->toArray();

        array_unshift($categories, CategoriesModel::getRootCategory(), CategoriesModel::getArchiveCategory());

        if ($categories == false) {
            echo json_encode(array());
        } else {
            $result = $this->_build_tree($categories);
            echo json_encode($result);
        }
    }

    public function get(Request $request)
    {
        $id = $request->input('id', NULL);

        if (isset($id)) {
            echo json_encode(CategoriesModel::where("id", $id)->first());
        }
    }

    public function save()
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);


            $category = new CategoriesModel();
            $createCategory = true;

            if (isset($data["id"]) && is_numeric($data["id"])) {
                $category = CategoriesModel::where('id', $data["id"])->first();
                if ($category) {
                    $createCategory = false;
                }
            }

            foreach ($data as $key => $value) {
                $category->$key = $value;
            }

            $category->save();

            if ($createCategory) {
                if (!isset($data['id_parent'])) {
                    $data['id_parent'] = 0 ;
                }
                CategoriesModel::newProductIn($data['id_parent']);
            }

            echo json_encode($category->id);
        }
    }

    public function update_order()
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);

            if (count($data['categories']) > 1) {
                foreach ($data['categories'] as $category) {
                    CategoriesModel::where("id", intval($category['id']))->update(array('sort' => intval($category['sort'])));
                }
            }
        }

        echo json_encode('OK');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id', NULL);
        $force_delete = $request->input('force', NULL);



        if (isset($id)) {
            $category = CategoriesModel::where("id", $id)->first();
            if ((intval($category->nb_products_r) > 0 || intval($category->nb_products) > 0) && !isset($force_delete)) {
                echo json_encode(array('hasProducts' => true));
            } else {
                if ((intval($category->nb_products_r) == 0 && intval($category->nb_products) == 0) || (isset($force_delete) && $force_delete === "true")) {
                    $this->_force_delete($id);
                } else if (isset($force_delete) && $force_delete === "false") {
                    $this->_safe_delete($id);
                }
                $parent = $this->categories->get($category->id_parent);
                echo json_encode($parent);
            }
        }
    }




    private function _force_delete($id = NULL){
        if($id){
            $category = CategoriesModel::where("id", $id)->first();


            $id_arr = CategoriesModel::delete_r($id);
            if( intval($category->nb_products_r) > 0 || intval($category->nb_products) > 0 ) {
                CategoriesModel::removeProductIn($category->id_parent, true, intval($category->nb_products_r) + intval($category->nb_products));
                foreach($id_arr as $id) {
                    Products::where("category", $id)->delete();
                }
            }
            return;
        }
        return;
    }

    private function _safe_delete($id = NULL){
        if($id){
            $category = CategoriesModel::where("id", $id)->first();
            $id_arr = CategoriesModel::delete_r($id);
            if( intval($category->nb_products_r) > 0 || intval($category->nb_products) > 0 ) {
                CategoriesModel::removeProductIn($category->id_parent, true, intval($category->nb_products_r) + intval($category->nb_products));
                Products::archive_products($id_arr);
            }
            return;
        }
        return;
    }

    private function _build_tree($categories, $id = -2)
    {
        $result = array();

        foreach ($categories as $category) {
            if ($category["id_parent"] == $id) {

                $tmp = $category;
                $res = $this->_build_tree($categories, $category["id"]);
                if (!empty($res)) {
                    $tmp["branches"] = $res;
                }
                $tmp["open"] = false;
                $result[] = $tmp;
            }
        }

        return $result;
    }

}