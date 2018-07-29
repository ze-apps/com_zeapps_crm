<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\com_zeapps_crm\Models\DeliveryLines;
use App\com_zeapps_crm\Models\DeliveryLineDetails;
use App\com_zeapps_crm\Models\StockMovements;
use App\com_zeapps_crm\Models\ProductProducts as Product ;
use Zeapps\Models\Config;

class Deliveries extends Model {
    use SoftDeletes;

    protected $table = 'com_zeapps_crm_deliveries';

    public function createFrom($src){
        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);

        $format = Config::where("id", "crm_delivery_format")->first()->value ;
        $num = self::get_numerotation();
        $src->numerotation = self::parseFormat($format, $num);
        $src->date_creation = date('Y-m-d');

        $delivery = new Deliveries ;

        foreach ($src as $key => $value) {
            $delivery->$key = $value ;
        }

        $delivery->save();
        $id = $delivery->id ;



        $new_id_lines = [];

        if(isset($src->lines) && is_array($src->lines)){
            foreach($src->lines as $line){
                $old_id = $line->id;

                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_delivery = $id;


                $deliveryLine = DeliveryLines() ;
                foreach ($line as $key => $value) {
                    $deliveryLine->$key = $value ;
                }
                $deliveryLine->save();
                $new_id_lines[$old_id] = $deliveryLine->id ;

                if($line->type === 'product'){
                    $product = Product::where("id", $line->id_product)->first() ;

                    $stockMovement = StockMovements() ;
                    $stockMovement->id_warehouse = $src->id_warehouse;
                    $stockMovement->id_stock = $product->id_stock; // TODO : le stock ne doit pas être associé au produit mais ID Stock du document source
                    $stockMovement->label = "Bon de livraison n° " . $src->numerotation;
                    $stockMovement->qty = -1 * floatval($line->qty);
                    $stockMovement->id_table = $src->id;
                    $stockMovement->name_table = "com_zeapps_crm_deliveries";
                    $stockMovement->date_mvt = $src->date_creation;
                    $stockMovement->ignored = 0;
                    $stockMovement->save();
                }
            }
        }

        if(isset($src->line_details) && is_array($src->line_details)){
            foreach($src->line_details as $line){
                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_delivery = $id;
                $line->id_line = $new_id_lines[$line->id_line];

                $deliveryLineDetail = DeliveryLineDetails() ;
                foreach ($line as $key => $value) {
                    $deliveryLineDetail->$key = $value ;
                }
                $deliveryLineDetail->save();

                if($line->type === 'product'){

                    $stockMovement = StockMovements() ;
                    $stockMovement->id_warehouse = $src->id_warehouse;
                    $stockMovement->id_stock = $product->id_stock; // TODO : le stock ne doit pas être associé au produit mais ID Stock du document source
                    $stockMovement->label = "Bon de livraison n° " . $src->numerotation;
                    $stockMovement->qty = -1 * floatval($line->qty);
                    $stockMovement->id_table = $src->id;
                    $stockMovement->name_table = "com_zeapps_crm_deliveries";
                    $stockMovement->date_mvt = $src->date_creation;
                    $stockMovement->ignored = 0;
                    $stockMovement->save();
                }
            }
        }

        return array(
            "id" =>$id,
            "numerotation" => $src->numerotation
        );
    }

    public static function get_numerotation($test = false){
        if($numerotation = Config::where("id", "crm_delivery_numerotation")->first()) {
            $valueSend = $numerotation->value ;
            if(!$test) {
                $numerotation->value++;
                $numerotation->save();
            }
            return $valueSend;
        } else{
            if(!$test) {
                $numerotation = new Config() ;
                $numerotation->id = 'crm_delivery_numerotation' ;
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
}