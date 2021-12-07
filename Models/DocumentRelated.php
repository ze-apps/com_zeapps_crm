<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Str;
use Mpdf\Tag\S;
use Zeapps\Core\ModelHelper;

class DocumentRelated extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_document_related';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');

        $this->fieldModelInfo->string('type_document_from', 255)->default("");
        $this->fieldModelInfo->integer('id_document_from')->default(0);

        $this->fieldModelInfo->string('type_document_to', 255)->default("");
        $this->fieldModelInfo->integer('id_document_to')->default(0);


        $this->fieldModelInfo->timestamps();
        $this->fieldModelInfo->softDeletes();

        parent::__construct($attributes);
    }

    public Static function getInvoicesRelatedTo($typeDocumentFrom, $idDocumentFrom) {
        $documents = self::where("type_document_from", $typeDocumentFrom)
            ->where("id_document_from", $idDocumentFrom)
            ->get();

        $invoices = [];
        foreach($documents as $document) {
            if ($document->type_document_to == "invoices") {
                $invoices[] = $document->id_document_to ;
            }

            // appel récursive pour récupère toutes les factures files 
            $invoices = array_merge($invoices, self::getInvoicesRelatedTo($document->type_document_to, $document->id_document_to));
        }

        return $invoices;
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