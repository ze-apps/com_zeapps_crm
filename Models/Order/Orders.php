<?php

namespace App\com_zeapps_crm\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Zeapps\Models\Config;
use App\com_zeapps_crm\Models\Order\OrderLines;
use App\com_zeapps_crm\Models\Order\OrderLinePriceList;
use App\com_zeapps_crm\Models\Order\OrderTaxes;
use App\com_zeapps_crm\Models\Taxes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;
use Zeapps\Core\Event;

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
        $this->fieldModelInfo->integer('id_price_list')->default(0);
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
        $this->fieldModelInfo->integer('id_company_address_billing')->default(0);
        $this->fieldModelInfo->integer('id_contact_address_billing')->default(0);
        $this->fieldModelInfo->integer('id_company_address_delivery')->default(0);
        $this->fieldModelInfo->integer('id_contact_address_delivery')->default(0);

        $this->fieldModelInfo->string('delivery_name_company', false, true)->default("");
        $this->fieldModelInfo->string('delivery_name_contact', false, true)->default("");

        $this->fieldModelInfo->string('billing_address_1', 100)->default("");
        $this->fieldModelInfo->string('billing_address_2', 100)->default("");
        $this->fieldModelInfo->string('billing_address_3', 100)->default("");
        $this->fieldModelInfo->string('billing_city', 100)->default("");
        $this->fieldModelInfo->string('billing_zipcode', 50)->default("");
        $this->fieldModelInfo->integer('billing_state_id', false)->default(0);
        $this->fieldModelInfo->string('billing_state', 100)->default("");
        $this->fieldModelInfo->integer('billing_country_id')->default(0);
        $this->fieldModelInfo->string('billing_country_name', 100)->default("");
        $this->fieldModelInfo->string('delivery_address_1', 100)->default("");
        $this->fieldModelInfo->string('delivery_address_2', 100)->default("");
        $this->fieldModelInfo->string('delivery_address_3', 100)->default("");
        $this->fieldModelInfo->string('delivery_city', 100)->default("");
        $this->fieldModelInfo->string('delivery_zipcode', 50)->default("");
        $this->fieldModelInfo->integer('delivery_state_id', false)->default(0);
        $this->fieldModelInfo->string('delivery_state', 100)->default("");
        $this->fieldModelInfo->integer('delivery_country_id')->default(0);
        $this->fieldModelInfo->string('delivery_country_name', 100)->default("");
        $this->fieldModelInfo->string('accounting_number', 255)->default("");
        $this->fieldModelInfo->decimal('global_discount', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('total_prediscount_ht', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('total_prediscount_ttc', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('total_discount_ht', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('total_discount_ttc', 8, 2)->default(0);
        $this->fieldModelInfo->decimal('total_ht', 9, 2)->default(0);
        $this->fieldModelInfo->decimal('total_tva', 9, 2)->default(0);
        $this->fieldModelInfo->decimal('total_ttc', 9, 2)->default(0);
        $this->fieldModelInfo->timestamp('date_creation')->nullable();
        $this->fieldModelInfo->timestamp('date_limit')->nullable();
        $this->fieldModelInfo->integer('id_modality', false)->default(0);
        $this->fieldModelInfo->string('label_modality', 255)->default("");
        $this->fieldModelInfo->string('bank_check_number', 255)->default("");
        $this->fieldModelInfo->string('check_issuer', 255)->default("");
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
        $order->date_creation = date('Y-m-d');
        $order->finalized = 0;
        $order->save();
        $id = $order->id;


        if (isset($src->lines)) {
            self::createFromLine($src->lines, $id, 0);
        }

        $order->save();

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


                // save price list
                if (isset($line->priceList)) {
                    foreach ($line->priceList as $priceList) {
                        unset($priceList->id);
                        unset($priceList->created_at);
                        unset($priceList->updated_at);
                        unset($priceList->deleted_at);


                        $objDeliveryLinePriceList = new OrderLinePriceList();

                        foreach (OrderLinePriceList::getSchema() as $key) {
                            if (isset($priceList->$key)) {
                                $objDeliveryLinePriceList->$key = $priceList->$key;
                            }
                        }

                        $objDeliveryLinePriceList->id_order_line = $orderLine->id;
                        $objDeliveryLinePriceList->save();
                    }
                }

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


    public function save(array $options = [], $sendEventFinalized = true, $updatePrice = true)
    {
        $isFinalized = false;

        if ((!isset($this->original["finalized"]) && isset($this->finalized) && $this->finalized) || (isset($this->original["finalized"]) && isset($this->finalized) && $this->original["finalized"] != $this->finalized && $this->finalized)) {
            $isFinalized = true;
        }

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

        $result = parent::save($options);

        // update price
        if ($updatePrice) {
            $this->updatePrice($this);
        }

        if ($sendEventFinalized && $isFinalized) {
            $dataEvent = array('id_order' => $this->id);
            Event::sendAction('com_zeapps_crm_order', 'finalized', $dataEvent);
        }

        return $result;
    }


    public static function frequencyOf($id = 0, $src = 'contact')
    {
        $orders = Capsule::table('com_zeapps_crm_orders')
            ->select(Capsule::raw('TIMESTAMPDIFF(DAY, MIN(date_creation), MAX(date_creation)) as total_time, COUNT(id) as nb_orders'))
            ->where("id_" . $src, $id)
            ->where("date_creation", "!=", "0000-00-00 00:00:00")
            ->where("deleted_at", null)
            ->get();

        if ($orders && count($orders)) {
            if (intval($orders[0]->nb_orders) > 0) {
                return intval($orders[0]->total_time) / intval($orders[0]->nb_orders);
            } else {
                return '--';
            }
        } else {
            return '--';
        }
    }




    private function updatePrice($order)
    {
        if ($order && $order->id) {
            $ecritureComptable = [];
            $lines = OrderLines::getFromOrder($order->id);
            $taxes = Taxes::all();

            foreach ($lines as $line) {
                $ecritureComptaRetour = $this->updateLine($order, $line, $taxes);
                $ecritureComptable = $this->fuisionTableTaxe($ecritureComptable, $ecritureComptaRetour);
            }


            $total_ht = 0 ;
            $total_tva = 0 ;
            $total_ttc = 0 ;

            $total_ht_before_discount = 0 ;
            $total_ttc_before_discount = 0 ;



            // sauvegarde les lignes comptables
            OrderTaxes::where("id_order", $order->id)->delete();

            foreach ($ecritureComptable as $ecriture) {
                $objQuoteTaxes = new OrderTaxes();
                $objQuoteTaxes->id_order = $order->id;
                $objQuoteTaxes->base_tax = $ecriture["total_ht"] ;
                $objQuoteTaxes->id_taxe = $ecriture["id_taxe"] ;
                $objQuoteTaxes->value_rate_tax = $ecriture["value_taxe"] ;
                $objQuoteTaxes->amount_tax = $ecriture["amount_tva"] ;
                $objQuoteTaxes->accounting_number = $ecriture["accounting_number"] ;
                $objQuoteTaxes->accounting_number_taxe = $ecriture["accounting_number_taxe"] ;
                $objQuoteTaxes->total_ttc = $ecriture["total_ttc"] ;
                $objQuoteTaxes->save();


                $total_ht += $objQuoteTaxes->base_tax * 1 ;
                $total_tva += $objQuoteTaxes->amount_tax * 1 ;
                $total_ttc += $objQuoteTaxes->base_tax * 1 + $objQuoteTaxes->amount_tax * 1 ;

                $total_ht_before_discount += $ecriture["total_ht_before_discount"] ;
                $total_ttc_before_discount += $ecriture["total_ttc_before_discount"] ;
            }

            // calcul le montant total du document
            self::where('id', $order->id)->update(
                ['total_ht' => $total_ht,
                    'total_tva' => $total_tva,
                    'total_ttc' => $total_ttc,
                    'total_prediscount_ht' => $total_ht_before_discount,
                    'total_prediscount_ttc' => $total_ttc_before_discount,
                    'total_discount_ht' => $total_ht_before_discount - $total_ht,
                    'total_discount_ttc' => $total_ttc_before_discount - $total_ttc
                ]);
        }
    }

    private function updateLine($order, $line, $taxes, $discount_prohibited = 0) {
        $ecritureComptable = [] ;


        // si c'est une ligne composée
        if (isset($line->sublines) && count($line->sublines)) {
            foreach ($line->sublines as $subline) {
                $ecritureComptable = $this->fuisionTableTaxe($ecritureComptable, $this->updateLine($order, $subline, $taxes, $discount_prohibited || $line->discount_prohibited));
            }

            // recalcul le tableau en fonction du montant souhaité sur la ligne
            if ($line->update_price_from_subline == 0) {
                $total_ttc = 0 ;
                foreach ($ecritureComptable as $ecriture) {
                    $total_ttc += $ecriture["total_ttc"] * 1 ;
                }

                $ecritureComptable = $this->appliqueCoef($ecritureComptable, $line->price_unit_ttc_subline / $total_ttc);
            }


            // applique la remise
            $ecritureComptable = $this->appliqueRemise($ecritureComptable, $line->discount, $discount_prohibited || $line->discount_prohibited);


            // si c'est une ligne simple
        } else {
            $ecritureComptable[] = $this->getEcritureLigne($line, $taxes) ;

            // applique la remise de la ligne
            if ($line->discount > 0) {
                $ecritureComptable = $this->appliqueRemise($ecritureComptable, $line->discount, $discount_prohibited || $line->discount_prohibited);
            }
        }






        // Calcul le prix unitaire
        $price_unit = $line->price_unit ;
        if (isset($line->sublines) && count($line->sublines)) {
            $price_unit = 0 ;
            foreach ($ecritureComptable as &$ecriture) {
                $price_unit += $ecriture["total_ht"] * 1 ;
            }
        }




        // applique la quantité
        if ($line->qty != 1) {
            $ecritureComptable = $this->appliqueCoef($ecritureComptable, $line->qty);
        }




        // applique la remise du document si la ligne à un parent = 0
        if ($order->global_discount > 0 && $line->id_parent == 0) {
            $ecritureComptable = $this->appliqueRemise($ecritureComptable, $order->global_discount, $discount_prohibited || $line->discount_prohibited);
        }



        // calcul le prix total de la ligne
        $total_ht = 0 ;
        $total_ttc = 0 ;
        foreach ($ecritureComptable as &$ecriture) {
            $total_ht += $ecriture["total_ht"] * 1 ;
            $total_ttc += $ecriture["total_ttc"] * 1 ;
        }


        // Sauvergarder le prix unitaire
        $line = OrderLines::find($line->id) ;
        if ($line) {
            $line->price_unit = $price_unit ;
            $line->total_ht = $total_ht ;
            $line->total_ttc = $total_ttc ;
            $line->save() ;
        }

        return $ecritureComptable ;
    }


    private function appliqueCoef($ecritureComptable = [], $coef = 0) {
        foreach ($ecritureComptable as &$ecriture) {
            $ecriture["total_ht"] = round($ecriture["total_ht"] * 1 * $coef, 2);
            $ecriture["amount_tva"] = round($ecriture["total_ht"] * (($ecriture["value_taxe"]*1) / 100), 2) ;
            $ecriture["total_ttc"] = $ecriture["total_ht"] + $ecriture["amount_tva"] ;

            $ecriture["total_ht_before_discount"] = $ecriture["total_ht"] ;
            $ecriture["total_ttc_before_discount"] = $ecriture["total_ht"] + $ecriture["amount_tva"] ;

        }

        return $ecritureComptable ;
    }

    private function appliqueRemise($ecritureComptable = [], $remise = 0, $discount_prohibited = 0) {
        if ($discount_prohibited) {
            $remise = 0 ;
        }

        foreach ($ecritureComptable as &$ecriture) {
            $ecriture["total_ht"] = round($ecriture["total_ht"] * 1 * (1 - $remise / 100), 2);
            $ecriture["amount_tva"] = round($ecriture["total_ht"] * (($ecriture["value_taxe"]*1) / 100), 2) ;
            $ecriture["total_ttc"] = $ecriture["total_ht"] + $ecriture["amount_tva"] ;
        }

        return $ecritureComptable ;
    }

    private function getEcritureLigne($line, $taxes) {
        // recherche le taux de TVA
        $accounting_number_taxe = "" ;
        $value_taxe = $line->value_taxe * 1 ;
        foreach ($taxes as $taxe) {
            if ($taxe->id == $line->id_taxe) {
                $accounting_number_taxe = $taxe->accounting_number ;
                $value_taxe = $taxe->value * 1 ;
                break;
            }
        }

        $dataLine = [] ;
        $dataLine["accounting_number"] = $line->accounting_number ;
        $dataLine["accounting_number_taxe"] = $accounting_number_taxe ;
        $dataLine["id_taxe"] = $line->id_taxe ;
        $dataLine["value_taxe"] = $value_taxe ;
        $dataLine["total_ht"] = $line->price_unit * 1 ;
        $dataLine["amount_tva"] = round($dataLine["total_ht"] * 1 * ($value_taxe / 100), 2) ;
        $dataLine["total_ttc"] = $dataLine["total_ht"] + $dataLine["amount_tva"] ;

        $dataLine["total_ht_before_discount"] = $dataLine["total_ht"] ;
        $dataLine["total_ttc_before_discount"] = $dataLine["total_ttc"] ;

        return $dataLine ;
    }

    private function fuisionTableTaxe($source, $ajout) {
        foreach ($ajout as $lineAdd) {
            $addToTable = true;

            foreach ($source as &$line) {
                if ($line["accounting_number"] == $lineAdd["accounting_number"]
                    && $line["accounting_number_taxe"] == $lineAdd["accounting_number_taxe"]
                    && $line["id_taxe"] == $lineAdd["id_taxe"]
                    && $line["value_taxe"] == $lineAdd["value_taxe"]
                ) {
                    $addToTable = false ;

                    $line["total_ht"] += $lineAdd["total_ht"] * 1 ;

                    // calcul du montant de la TVA
                    $line["amount_tva"] = round($line["total_ht"] * ($line["value_taxe"] / 100), 2) ;
                    $line["total_ttc"] = $line["total_ht"] + $line["amount_tva"] ;

                    $line["total_ht_before_discount"] += $lineAdd["total_ht_before_discount"] ;
                    $line["total_ttc_before_discount"] += $lineAdd["total_ttc_before_discount"] ;

                    break;
                }
            }

            if ($addToTable) {
                $dataLine = [] ;
                $dataLine["accounting_number"] = $lineAdd["accounting_number"] ;
                $dataLine["accounting_number_taxe"] = $lineAdd["accounting_number_taxe"] ;
                $dataLine["id_taxe"] = $lineAdd["id_taxe"] ;
                $dataLine["value_taxe"] = $lineAdd["value_taxe"] * 1 ;
                $dataLine["total_ht"] = $lineAdd["total_ht"] * 1 ;
                $dataLine["amount_tva"] = round($dataLine["total_ht"] * ($dataLine["value_taxe"] / 100), 2) ;
                $dataLine["total_ttc"] = $dataLine["total_ht"] + $dataLine["amount_tva"] ;

                $dataLine["total_ht_before_discount"] = $lineAdd["total_ht_before_discount"] ;
                $dataLine["total_ttc_before_discount"] = $lineAdd["total_ttc_before_discount"] ;

                $source[] = $dataLine ;
            }
        }

        return $source ;
    }

}