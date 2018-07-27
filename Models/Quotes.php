<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\com_zeapps_crm\Models\QuoteLines;
use App\com_zeapps_crm\Models\QuoteLineDetails;

use Zeapps\Models\Config;

class Quotes extends Model
{
    use SoftDeletes;

    protected $table = 'com_zeapps_crm_quotes';

    public function createFrom($src)
    {
        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);

        $format = Config::where('id', "crm_quote_format")->first()->value;
        $num = self::get_numerotation();
        $src->numerotation = self::parseFormat($format, $num);
        $src->date_creation = date('Y-m-d');
        $src->date_limit = date("Y-m-d", strtotime("+1 month", time()));

        $quotes = new Quotes();
        foreach ($src as $key => $value) {
            $quotes->$key = $value;
        }
        $quotes->save();
        $id = $quotes->id;

        $new_id_lines = [];

        if (isset($src->lines) && is_array($src->lines)) {
            foreach ($src->lines as $line) {
                $old_id = $line->id;

                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_quote = $id;


                $quote_line = new QuoteLines();
                foreach ($line as $key => $value) {
                    $quote_line->$key = $value;
                }
                $quote_line->save();
                $new_id_lines[$old_id] = $quote_line->id;
            }
        }

        if (isset($src->line_details) && is_array($src->line_details)) {
            foreach ($src->line_details as $line) {
                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_quote = $id;
                $line->id_line = $new_id_lines[$line->id_line];


                $quote_line_details = new QuoteLineDetails();
                foreach ($line as $key => $value) {
                    $quote_line_details->$key = $value;
                }
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
            if (!$test) {
                $numerotation->value++;
                $numerotation->save();
            }
            return $numerotation->value;
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
}