<?php

namespace App\com_zeapps_crm\Models\Delivery;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class DeliveryCompanies extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_delivery_companies';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_delivery')->default(0);
        $this->fieldModelInfo->integer('id_user_account_manager')->default(0);
        $this->fieldModelInfo->string('name_user_account_manager', 100)->default("");
        $this->fieldModelInfo->string('company_name', 255)->default("");
        $this->fieldModelInfo->integer('id_parent_company')->default(0);
        $this->fieldModelInfo->string('name_parent_company', 255)->default("");
        $this->fieldModelInfo->integer('id_type_account')->default(0);
        $this->fieldModelInfo->string('name_type_account')->default("");
        $this->fieldModelInfo->integer('id_activity_area')->default(0);
        $this->fieldModelInfo->string('name_activity_area', 100)->default("");
        $this->fieldModelInfo->bigInteger('turnover')->default(0);
        $this->fieldModelInfo->string('billing_address_1', 100)->default("");
        $this->fieldModelInfo->string('billing_address_2', 100)->default("");
        $this->fieldModelInfo->string('billing_address_3', 100)->default("");
        $this->fieldModelInfo->string('billing_city', 100)->default("");
        $this->fieldModelInfo->string('billing_zipcode', 50)->default("");
        $this->fieldModelInfo->string('billing_state', 100)->default("");
        $this->fieldModelInfo->integer('billing_country_id')->default(0);
        $this->fieldModelInfo->string('billing_country_name', 100)->default("");
        $this->fieldModelInfo->string('delivery_address_1', 100)->default("");
        $this->fieldModelInfo->string('delivery_address_2', 100)->default("");
        $this->fieldModelInfo->string('delivery_address_3', 100)->default("");
        $this->fieldModelInfo->string('delivery_city', 100)->default("");
        $this->fieldModelInfo->string('delivery_zipcode', 50)->default("");
        $this->fieldModelInfo->string('delivery_state', 100)->default("");
        $this->fieldModelInfo->integer('delivery_country_id')->default(0);
        $this->fieldModelInfo->string('delivery_country_name', 100)->default("");
        $this->fieldModelInfo->text('comment');
        $this->fieldModelInfo->string('phone', 25)->default("");
        $this->fieldModelInfo->string('fax', 25)->default("");
        $this->fieldModelInfo->string('website_url', 255)->default("");
        $this->fieldModelInfo->string('code_naf', 15)->default("");
        $this->fieldModelInfo->string('code_naf_libelle', 255)->default("");
        $this->fieldModelInfo->string('company_number', 30)->default("");
        $this->fieldModelInfo->string('accounting_number', 15)->default("");
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
}