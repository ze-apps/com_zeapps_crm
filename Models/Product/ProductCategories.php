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
    protected $table;

    protected $fieldModelInfo;


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

        return parent::save($options);
    }


    public static function getRootCategory()
    {
        $root = array();
        $root["name"] = 'racine';
        $root["id"] = '0';
        $root["id_parent"] = '-2';
        $root["open"] = false;

        return $root;
    }

    public static function getArchiveCategory()
    {
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

    public static function removeProductIn($id = array(), $parent = false, $qty = 1)
    {
        if ($id) {
            $category = ProductCategories::where("id", $id)->first();
            if (!$parent) {
                $category->nb_products = $category->nb_products - $qty;
                $category->save();
            } else {
                $category->nb_products_r = $category->nb_products_r - $qty;
                $category->save();
            }

            if ($category->id_parent > 0) {
                self::removeProductIn($category->id_parent, true, $qty);
            }
        }
        return;
    }

    public static function newProductIn($id = array(), $parent = false)
    {
        if ($id) {
            $category = ProductCategories::where("id", $id)->first();
            if (!$parent) {
                $category->nb_products = $category->nb_products + 1;
                $category->save();
            } else {
                $category->nb_products_r = $category->nb_products_r + 1;
                $category->save();
            }

            if ($category->id_parent > 0) {
                self::newProductIn($category->id_parent, true);
            }
        }
        return;
    }


    public static function turnover($dateDebut, $dateFin, $where = array())
    {
        $query = "SELECT SUM(l.total_ht) as total_ht,
                         SUM(l.qty) as qty
                  FROM com_zeapps_crm_product_categories ca
                  LEFT JOIN com_zeapps_crm_products p ON p.id_cat = ca.id
                  LEFT JOIN com_zeapps_crm_invoice_lines l ON l.id_product = p.id
                  LEFT JOIN com_zeapps_crm_invoices i ON i.id = l.id_invoice
                  WHERE i.finalized = '1'
                        AND l.type = 'product'
                        AND i.deleted_at IS NULL
                        AND l.deleted_at IS NULL" ;

        if ($dateDebut) {
            $query .= " AND i.date_creation >= '" . $dateDebut . "'";
        }

        if ($dateFin) {
            $query .= " AND i.date_creation <= '" . $dateFin . "'";
        }

        if (isset($where['id_cat'])) {
            $query .= " AND ca.id IN (" . implode(',', $where['id_cat']) . ")";
        }


        if (isset($where['id_price_list'])) {
            $query .= " AND i.id_price_list = " . $where['id_price_list'];
        }

        if (isset($where['id_origin'])) {
            $query .= " AND i.id_origin = " . $where['id_origin'];
        }

        if (isset($where['delivery_country_id IN'])) {
            $query .= " AND i.delivery_country_id IN (" . $where['delivery_country_id IN'] . ")";
        }

        if (isset($where['delivery_country_id'])) {
            $query .= " AND i.delivery_country_id = " . $where['delivery_country_id'] ;
        }

        return Capsule::select(Capsule::raw($query));
    }

    public static function turnover_details($year = null, $where = array())
    {
        $query = "SELECT SUM(l.total_ht) as total_ht,
                         YEAR(i.date_limit) as year,
                         i.id_origin as id_origin,
                         ca.id as id_cat
                  FROM com_zeapps_crm_product_categories ca
                  LEFT JOIN com_zeapps_crm_products p ON p.id_cat = ca.id
                  LEFT JOIN com_zeapps_crm_invoice_lines l ON l.id_product = p.id
                  LEFT JOIN com_zeapps_crm_invoices i ON i.id = l.id_invoice
                  WHERE i.finalized = '1'
                        AND l.type = 'product'
                        AND i.deleted_at IS NULL
                        AND l.deleted_at IS NULL
                        AND YEAR(i.date_limit) in (" . ($year - 1) . "," . $year . ")";

        if (isset($where['id_origin'])) {
            $query .= " AND i.id_origin = " . $where['id_origin'];
        }
        if (isset($where['id_cat'])) {
            $query .= " AND ca.id IN (" . implode(',', $where['id_cat']) . ")";
        }
        if (isset($where['country_id'])) {
            $query .= " AND i.country_id IN (" . implode(',', $where['country_id']) . ")";
        }

        $query .= " GROUP BY YEAR(i.date_limit), i.id_origin, ca.id";

        return Capsule::select(Capsule::raw($query));
    }
}