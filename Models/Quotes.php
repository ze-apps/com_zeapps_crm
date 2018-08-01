<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\com_zeapps_crm\Models\QuoteLines;
use App\com_zeapps_crm\Models\QuoteLineDetails;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Models\Config;

use Zeapps\Core\ModelHelper;

class Quotes extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_quotes';
    protected $table ;

    protected $fieldModelInfo ;




    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        parent::__construct($attributes);
    }

    public static function createFrom($src)
    {
        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);




        $quotes = new Quotes();
        foreach (self::getSchema() as $key) {
            if (isset($src->$key)) {
                $quotes->$key = $src->$key;
            }
        }
        $quotes->date_creation = date('Y-m-d');
        $quotes->date_limit = date("Y-m-d", strtotime("+1 month", time()));
        $quotes->save();
        $id = $quotes->id;

        $new_id_lines = [];

        if (isset($src->lines)) {
            foreach ($src->lines as $line) {
                $old_id = $line->id;

                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);




                $quote_line = new QuoteLines();
                foreach (QuoteLines::getSchema() as $key) {
                    if (isset($line->$key)) {
                        $quote_line->$key = $line->$key;
                    }
                }
                $quote_line->id_quote = $id;
                $quote_line->save();


                $new_id_lines[$old_id] = $quote_line->id;
            }
        }

        if (isset($src->line_details)) {
            foreach ($src->line_details as $line) {
                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $quote_line_details = new QuoteLineDetails();
                foreach (QuoteLineDetails::getSchema() as $key) {
                    if (isset($line->$key)) {
                        $quote_line_details->$key = $line->$key;
                    }
                }
                $quote_line_details->id_quote = $id;
                $quote_line_details->id_line = $new_id_lines[$line->id_line];
                $quote_line_details->save();
            }
        }

        return array(
            "id" => $id,
            "numerotation" => $src->numerotation
        );
    }

    public static function get_numerotation($test = false)
    {
        if ($numerotation = Config::where("id", "crm_quote_numerotation")->first()) {
            $valueSend = $numerotation->value ;
            if (!$test) {
                $numerotation->value++;
                $numerotation->save();
            }
            return $valueSend;
        } else {
            if (!$test) {
                $numerotation = new Config() ;
                $numerotation->id = 'crm_quote_numerotation';
                $numerotation->value = 2;
                $numerotation->save();
            }
            return 1;
        }
    }

    public static function parseFormat($result = null, $num = null)
    {
        if ($result && $num) {
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



        /******** special date field **********/
        if ($this->date_creation && $this->date_creation != "") {
            $this->date_creation = str_replace("T", " ", $this->date_creation);
            $this->date_creation = str_replace("Z", "", $this->date_creation);
        }

        if ($this->date_limit && $this->date_limit != "") {
            $this->date_limit = str_replace("T", " ", $this->date_limit);
            $this->date_limit = str_replace("Z", "", $this->date_limit);
        }





        /**** set a document number ****/
        if (!isset($this->numerotation) || !$this->numerotation || $this->numerotation == "") {
            $format = Config::where('id', 'crm_quote_format')->first()->value ;
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