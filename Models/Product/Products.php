<?php

namespace App\com_zeapps_crm\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class Products extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_products';
    protected $table;

    protected $fieldModelInfo;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_cat')->default(0);
        $this->fieldModelInfo->integer('id_parent')->default(0);
        $this->fieldModelInfo->integer('id_product')->default(0);
        $this->fieldModelInfo->string('type_product', 255)->default("");
        $this->fieldModelInfo->string('ref', 255)->default("");
        $this->fieldModelInfo->string('name', 255)->default("");
        $this->fieldModelInfo->text('description');
        $this->fieldModelInfo->decimal('price_ht', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('price_ttc', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('quantite', 8, 2)->default(0);
        $this->fieldModelInfo->tinyInteger('auto')->default(0);
        $this->fieldModelInfo->integer('id_taxe')->default(0);
        $this->fieldModelInfo->decimal('value_taxe', 8, 2)->default(0);
        $this->fieldModelInfo->string('accounting_number', 255)->default("");
        $this->fieldModelInfo->tinyInteger('update_price_from_subline', false)->default(0);
        $this->fieldModelInfo->tinyInteger('show_subline', false)->default(0);
        $this->fieldModelInfo->decimal('price_unit_ttc_subline', 8, 2)->default(0);
        $this->fieldModelInfo->integer('sort')->default(0);
        $this->fieldModelInfo->mediumtext('extra');
        $this->fieldModelInfo->tinyInteger('active')->default(1);
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

    public static function archive_products($id_arr = NULL)
    {
        if ($id_arr) {
            foreach ($id_arr as $id) {
                self::where("id_cat", $id)->update(array('id_cat' => -1));
            }
        }
        return;
    }

    public static function top10($year = null, $where = array())
    {
        $query = "SELECT SUM(l.total_ht) as total_ht,
                         p.name as name
                  FROM com_zeapps_crm_product_categories ca
                  LEFT JOIN com_zeapps_crm_products p ON p.id_cat = ca.id
                  LEFT JOIN com_zeapps_crm_invoice_lines l ON l.id_product = p.id
                  LEFT JOIN com_zeapps_crm_invoices i ON i.id = l.id_invoice
                  WHERE i.finalized = '1'
                        AND l.type = 'product'
                        AND i.deleted_at IS NULL
                        AND l.deleted_at IS NULL
                        AND YEAR(i.date_limit) = " . $year;


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








        $query .= " GROUP BY p.id ORDER BY total_ht DESC LIMIT 10";

        return Capsule::select(Capsule::raw($query));
    }
}