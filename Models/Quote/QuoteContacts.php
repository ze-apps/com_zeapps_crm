<?php

namespace App\com_zeapps_crm\Models\Quote;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class QuoteContacts extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_quote_contacts';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_quote')->default(0);
        $this->fieldModelInfo->integer('id_user_account_manager')->default(0);
        $this->fieldModelInfo->string('name_user_account_manager', 100)->default("");
        $this->fieldModelInfo->integer('id_company')->default(0);
        $this->fieldModelInfo->string('name_company', 255)->default("");
        $this->fieldModelInfo->string('title_name', 30)->default("");
        $this->fieldModelInfo->string('first_name', 50)->default("");
        $this->fieldModelInfo->string('last_name', 50)->default("");
        $this->fieldModelInfo->string('email', 255)->default("");
        $this->fieldModelInfo->string('phone', 25)->default("");
        $this->fieldModelInfo->string('other_phone', 25)->default("");
        $this->fieldModelInfo->string('mobile', 25)->default("");
        $this->fieldModelInfo->string('fax', 25)->default("");
        $this->fieldModelInfo->string('assistant', 70)->default("");
        $this->fieldModelInfo->string('assistant_phone', 25)->default("");
        $this->fieldModelInfo->string('department', 100)->default("");
        $this->fieldModelInfo->string('job', 100)->default("");
        $this->fieldModelInfo->enum('email_opt_out', ['Y','N'])->default("N");
        $this->fieldModelInfo->string('skype_id', 100)->default("");
        $this->fieldModelInfo->string('twitter', 100)->default("");
        $this->fieldModelInfo->date('date_of_birth')->nullable();
        $this->fieldModelInfo->string('address_1', 100)->default("");
        $this->fieldModelInfo->string('address_2', 100)->default("");
        $this->fieldModelInfo->string('address_3', 100)->default("");
        $this->fieldModelInfo->string('city', 100)->default("");
        $this->fieldModelInfo->string('zipcode', 50)->default("");
        $this->fieldModelInfo->string('state', 100)->default("");
        $this->fieldModelInfo->integer('country_id')->default(0);
        $this->fieldModelInfo->string('country_name', 100)->default("");
        $this->fieldModelInfo->text('comment');
        $this->fieldModelInfo->string('website_url', 255)->default("");
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