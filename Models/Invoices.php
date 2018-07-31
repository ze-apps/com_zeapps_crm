<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Zeapps\Models\Config;
use App\com_zeapps_crm\Models\InvoiceLines;
use App\com_zeapps_crm\Models\InvoiceLineDetails;
use App\com_zeapps_contact\Models\Modalities;

use Illuminate\Database\Capsule\Manager as Capsule;

class Invoices extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_invoices';
    protected $table ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        parent::__construct($attributes);
    }


    public static function createFrom($src){
        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);

        $src->date_creation = date('Y-m-d');
        $src->finalized = 0;

        if (isset($src->id_modality)) {
            if ($modality = Modalities::where("id", $src->id_modality)->first()) {
                if ($modality->settlement_type === '0') {
                    $src->date_limit = date("Y-m-d", strtotime("+" . $modality->settlement_delay . " day", time()));
                } elseif ($modality->settlement_type === '1') {
                    $year = date("Y", strtotime("+" . $modality->settlement_delay . " day", time()));
                    $month = date("m", strtotime("+" . $modality->settlement_delay . " day", time()));
                    $day = date("d", strtotime("+" . $modality->settlement_delay . " day", time()));
                    if (intval($day) <= $modality->settlement_date) {
                        $src->date_limit = $year . "-" . $month . "-" . $modality->settlement_date;
                    } else {
                        $date = date("Y-m", strtotime("+1 month", strtotime("+" . $modality->settlement_delay . " day", time())));
                        $src->date_limit = $date . "-" . $modality->settlement_date;
                    }
                }
            }
        }


        $invoice = new Invoices() ;
        foreach (self::getSchema() as $key) {
            if (isset($src->$key)) {
                $invoice->$key = $src->$key;
            }
        }
        $invoice->save() ;
        $id = $invoice->id;


        $new_id_lines = [];

        if(isset($src->lines) && is_array($src->lines)){
            foreach($src->lines as $line){
                $old_id = $line->id;

                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_invoice = $id;


                $invoiceLine = new InvoiceLines() ;
                foreach ($line as $key => $value) {
                    $invoiceLine->$key = $value ;
                }
                $invoiceLine->save() ;
                $new_id_lines[$old_id] = $invoiceLine->id;
            }
        }

        if(isset($src->line_details) && is_array($src->line_details)){
            foreach($src->line_details as $line){
                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_invoice = $id;
                $line->id_line = $new_id_lines[$line->id_line];


                $invoiceLineDetail = new InvoiceLineDetails() ;
                foreach ($line as $key => $value) {
                    $invoiceLineDetail->$key = $value ;
                }
                $invoiceLineDetail->save() ;
            }
        }

        return array(
            "id" =>$id,
            "numerotation" => $src->numerotation
        );
    }

    public static function get_numerotation($test = false){
        if($numerotation = Config::where("id", "crm_invoice_numerotation")->first()) {
            $valueSend = $numerotation->value ;
            if(!$test) {
                $numerotation->value++;
                $numerotation->save();
            }
            return $valueSend;
        } else{
            if(!$test) {
                $numerotation = new Config() ;
                $numerotation->id = 'crm_invoice_numerotation' ;
                $numerotation->value = 2 ;
                $numerotation->save() ;
            }
            return 1;
        }
    }

    public static function parseFormat($result = null, $num = null)
    {
        if ($result && $num){
            $result = preg_replace_callback('/[[dDjzmMnyYgGhH\-_]*(x+)[dDjzmMnyYgGhH\-_]*]/',
                function ($matches) use ($num) {
                    return str_replace($matches[1], substr($num, -strlen($matches[1])), $matches[0]);
                },
                $result);

            $result = preg_replace_callback('/[[dDjzmMnyYgGhH\-_]*(X+)[dDjzmMnyYgGhH\-_]*]/',
                function ($matches) use ($num) {
                    if (strlen($matches[1]) > strlen($num)) {
                        return str_replace($matches[1], str_pad($num, strlen($matches[1]), '0', STR_PAD_LEFT), $matches[0]);
                    } else {
                        return str_replace($matches[1], substr($num, -strlen($matches[1])), $matches[0]);
                    }
                },
                $result);

            $timestamp = time();

            $result = preg_replace_callback('/[[xX0-9\-_]*([dDjzmMnyYgGhH]+)[xX0-9\-_]*[]\/\-_]/',
                function ($matches) use ($timestamp) {
                    foreach ($matches as $match) {
                        return date($match, $timestamp);
                    }
                    return true;
                },
                $result);

            $result = str_replace(array('[', ']'), '', $result);

            return $result;
        }
        return false;
    }



    public static function getSchema() {
        return $schema = Capsule::schema()->getColumnListing(self::$_table) ;
    }

    public function save(array $options = []) {


        /**** set a document number ****/
        if (!isset($this->numerotation) || !$this->numerotation || $this->numerotation == "") {
            $format = Config::where('id', 'crm_invoice_format')->first()->value;
            $num = self::get_numerotation();
            $this->numerotation = self::parseFormat($format, $num);
        }





        /**** to delete unwanted field ****/
        $schema = self::getSchema();
        foreach ($this->getAttributes() as $key => $value) {
            if (!in_array($key, $schema)) {
                //echo $key . "\n" ;
                unset($this->$key);
            }
        }
        /**** end to delete unwanted field ****/

        return parent::save($options);
    }
}