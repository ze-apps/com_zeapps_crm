<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class ModelEmail extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_model_email';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');

        $this->fieldModelInfo->string('name')->default("");
        $this->fieldModelInfo->string('default_to')->default("");
        $this->fieldModelInfo->string('subject')->default("");
        $this->fieldModelInfo->text('message')->default("");
        $this->fieldModelInfo->text('attachments')->default("");
        $this->fieldModelInfo->tinyInteger('to_quote')->default(0);
        $this->fieldModelInfo->tinyInteger('to_order')->default(0);
        $this->fieldModelInfo->tinyInteger('to_invoice')->default(0);
        $this->fieldModelInfo->tinyInteger('to_delivery')->default(0);

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