<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Zeapps\Models\Config;
use App\com_zeapps_crm\Models\OrderLines;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class Orders extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_orders';
    protected $table;

    protected $fieldModelInfo;

    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->string('libelle', 255)->default("");
        $this->fieldModelInfo->string('numerotation', 255)->default("");
        $this->fieldModelInfo->integer('id_origin', false)->default(0);
        $this->fieldModelInfo->integer('status', false)->default(0);
        $this->fieldModelInfo->tinyInteger('finalized')->default(0);
        $this->fieldModelInfo->string('final_pdf', 1023)->default("");
        $this->fieldModelInfo->integer('id_user_account_manager')->default(0);
        $this->fieldModelInfo->string('name_user_account_manager')->default("");
        $this->fieldModelInfo->integer('id_warehouse')->default(0);
        $this->fieldModelInfo->integer('id_company')->default(0);
        $this->fieldModelInfo->string('name_company')->default("");
        $this->fieldModelInfo->integer('id_contact')->default(0);
        $this->fieldModelInfo->string('name_contact')->default("");
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
        $this->fieldModelInfo->string('accounting_number', 255)->default("");
        $this->fieldModelInfo->decimal('global_discount', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('total_prediscount_ht', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('total_prediscount_ttc', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('total_discount', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('total_ht', 9, 2)->default(0);
        $this->fieldModelInfo->decimal('total_tva', 9, 2)->default(0);
        $this->fieldModelInfo->decimal('total_ttc', 9, 2)->default(0);
        $this->fieldModelInfo->timestamp('date_creation')->nullable();
        $this->fieldModelInfo->timestamp('date_limit')->nullable();
        $this->fieldModelInfo->string('id_modality', 255)->default("");
        $this->fieldModelInfo->string('label_modality', 255)->default("");
        $this->fieldModelInfo->string('reference_client', 255)->default("");
        $this->fieldModelInfo->timestamps();
        $this->fieldModelInfo->softDeletes();

        parent::__construct($attributes);
    }

    public static function createFrom($src)
    {
        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);


        $src->date_creation = date('Y-m-d');
        $src->date_limit = date("Y-m-d", strtotime("+1 month", time()));


        $order = new Orders();
        foreach (self::getSchema() as $key) {
            if (isset($src->$key)) {
                $order->$key = $src->$key;
            }
        }
        $order->save();
        $id = $order->id;


        if (isset($src->lines)) {
            self::createFromLine($src->lines, $id, 0) ;
        }

        return array(
            "id" => $id,
            "numerotation" => $order->numerotation
        );
    }


    private static function createFromLine($lines, $idDocument, $idParent)
    {
        if ($lines) {
            foreach ($lines as $line) {
                if (isset($line->sublines)) {
                    $sublines = $line->sublines;
                } else {
                    $sublines = false;
                }

                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);


                $orderLine = new OrderLines();
                foreach (OrderLines::getSchema() as $key) {
                    if (isset($line->$key)) {
                        $orderLine->$key = $line->$key;
                    }
                }
                $orderLine->id_order = $idDocument;
                $orderLine->id_parent = $idParent;
                $orderLine->save();

                if ($sublines) {
                    self::createFromLine($sublines, $idDocument, $orderLine->id);
                }
            }
        }
    }

    public static function get_numerotation($test = false)
    {
        if ($numerotation = Config::where("id", "crm_order_numerotation")->first()) {
            $valueSend = $numerotation->value;
            if (!$test) {
                $numerotation->value++;
                $numerotation->save();
            }
            return $valueSend;
        } else {
            if (!$test) {
                $numerotation = new Config();
                $numerotation->id = 'crm_order_numerotation';
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


    public static function getSchema()
    {
        return $schema = Capsule::schema()->getColumnListing(self::$_table);
    }


    public function save(array $options = [])
    {


        /******** clean data **********/
        $this->fieldModelInfo->cleanData($this);


        /**** set a document number ****/
        if (!isset($this->numerotation) || !$this->numerotation || $this->numerotation == "") {
            $format = Config::where('id', 'crm_order_format')->first()->value;
            $num = self::get_numerotation();
            $this->numerotation = self::parseFormat($format, $num);
        }


        /**** to delete unwanted field ****/
        $this->fieldModelInfo->removeFieldUnwanted($this);

        return parent::save($options);
    }

}