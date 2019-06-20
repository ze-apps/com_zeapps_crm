<?php

namespace App\com_zeapps_crm\Models\Invoice;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Zeapps\Core\Storage;
use Mpdf\Mpdf;

use Zeapps\Models\Config;
use App\com_zeapps_crm\Models\Invoice\InvoiceLines;
use App\com_zeapps_crm\Models\Invoice\InvoiceLinePriceList;
use App\com_zeapps_crm\Models\Invoice\InvoiceTaxes;
use App\com_zeapps_contact\Models\Modalities;
use App\com_zeapps_contact\Models\ModalitiesLang;
use App\com_zeapps_crm\Models\Payment\Payment;
use App\com_zeapps_crm\Models\Payment\PaymentLine;
use App\com_zeapps_crm\Models\CreditBalanceDetails;
use App\com_zeapps_crm\Models\AccountingEntries;
use App\com_zeapps_crm\Models\Taxes;
use App\com_zeapps_contact\Models\Companies;
use App\com_zeapps_contact\Models\Contacts;

use Zeapps\Core\Event;

use Zeapps\Core\ModelHelper;

use Illuminate\Database\Capsule\Manager as Capsule;

class Invoices extends Model
{
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_invoices';
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
        $this->fieldModelInfo->decimal('due', 8, 2)->default(0);
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


        parent::__construct($attributes);
    }


    public static function createFrom($src, $isCredit = false)
    {
        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);

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


        $invoice = new Invoices();
        foreach (self::getSchema() as $key) {
            if (isset($src->$key)) {
                $invoice->$key = $src->$key;
            }
        }
        $invoice->date_creation = date('Y-m-d');
        $invoice->finalized = 0;
        $invoice->save();
        $id = $invoice->id;


        if (isset($src->lines) && $src->lines) {
            self::createFromLine($src->lines, $id, 0, $isCredit);
        }

        $invoice->save();

        return array(
            "id" => $id,
            "numerotation" => $invoice->numerotation
        );
    }

    private static function createFromLine($lines, $idDocument, $idParent, $isCredit = false)
    {
        if ($lines) {
            foreach ($lines as $line) {
                //$old_id = $line->id;
                if (isset($line->sublines)) {
                    $sublines = $line->sublines;
                } else {
                    $sublines = false;
                }

                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);


                $invoiceLine = new InvoiceLines();
                foreach (InvoiceLines::getSchema() as $key) {
                    if (isset($line->$key)) {
                        $invoiceLine->$key = $line->$key;
                    }
                }


                if (!isset($invoiceLine->qty)) {
                    $invoiceLine->qty = 1 ;
                }

                if ($isCredit) {
                    $invoiceLine->qty *= -1 ;
                }

                $invoiceLine->id_invoice = $idDocument;
                $invoiceLine->id_parent = $idParent;
                $invoiceLine->save();


                // save price list
                if (isset($line->priceList)) {
                    foreach ($line->priceList as $priceList) {
                        unset($priceList->id);
                        unset($priceList->created_at);
                        unset($priceList->updated_at);
                        unset($priceList->deleted_at);


                        $objInvoiceLinePriceList = new InvoiceLinePriceList() ;

                        foreach (InvoiceLinePriceList::getSchema() as $key) {
                            if (isset($priceList->$key)) {
                                $objInvoiceLinePriceList->$key = $priceList->$key;
                            }
                        }

                        $objInvoiceLinePriceList->id_invoice_line = $invoiceLine->id ;
                        $objInvoiceLinePriceList->save() ;
                    }
                }

                if ($sublines) {
                    self::createFromLine($sublines, $idDocument, $invoiceLine->id);
                }
            }
        }
    }


    public static function get_numerotation($test = false)
    {
        if ($numerotation = Config::where("id", "crm_invoice_numerotation")->first()) {
            $valueSend = $numerotation->value;
            if (!$test) {
                $numerotation->value++;
                $numerotation->save();
            }
            return $valueSend;
        } else {
            if (!$test) {
                $numerotation = new Config();
                $numerotation->id = 'crm_invoice_numerotation';
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

    public function save(array $options = [], $savePayment = true, $finalized = true, $updatePrice = true)
    {
        $isSavePayment = false ;
        $objModalities = Modalities::find($this->id_modality) ;
        $objModalitiesLang = ModalitiesLang::where("id_modality", $this->id_modality)->where("id_lang", 1)->first() ;
        if ($objModalities) {
            if ($objModalities->situation >= 1 && $this->finalized == 1) {
                $isSavePayment = true;
            }
        }
        if ($objModalitiesLang) {
            $this->label_modality = $objModalitiesLang->label ;
        }




        $finalized_orignal = $this->getOriginal("finalized");


        /******** clean data **********/
        $this->fieldModelInfo->cleanData($this);


        /**** to delete unwanted field ****/
        $this->fieldModelInfo->removeFieldUnwanted($this);
        /**** end to delete unwanted field ****/





        /**** set a document number ****/
        if ($this->finalized == 1 && (!isset($this->numerotation) || !$this->numerotation || $this->numerotation == "")) {
            $format = Config::where('id', 'crm_invoice_format')->first()->value;
            $num = self::get_numerotation();
            $this->numerotation = self::parseFormat($format, $num);

            /**** to delete unwanted field ****/
            $this->fieldModelInfo->removeFieldUnwanted($this);
            /**** end to delete unwanted field ****/
            parent::save($options);
        }




        if ($finalized && $this->finalized == 1 && $finalized_orignal != 1 && $finalized_orignal !== null) {
            if ($isSavePayment == false) {
                $this->due = $this->total_ttc ;
            }


            parent::save($options);
            $this->finalize();
        }








        // action before save
        Event::sendAction('com_zeapps_crm_invoice', 'beforeSave', $this);


        $return = parent::save($options);

        // action before save
        Event::sendAction('com_zeapps_crm_invoice', 'afterSave', $this);

        // update price
        if ($updatePrice) {
            $this->updatePrice($this);
        }



        if ($savePayment && $isSavePayment) {
            $objPayment = new Payment() ;
            $objPayment->id_company = $this->id_company ;
            $objPayment->id_contact = $this->id_contact ;
            $objPayment->total = $this->total_ttc ;
            $objPayment->date_payment = $this->date_creation ;
            $objPayment->type_payment = $this->id_modality ;
            $objPayment->type_payment_label = $this->label_modality ;
            $objPayment->bank_check_number = $this->bank_check_number ;
            $objPayment->check_issuer = $this->check_issuer ;
            $objPayment->id_deposit_checks = 0 ;
            $objPayment->save() ;

            $objPaymentLine = new PaymentLine() ;
            $objPaymentLine->id_payment = $objPayment->id ;
            $objPaymentLine->id_invoice = $this->id ;
            $objPaymentLine->amount = $this->total_ttc ;
            $objPaymentLine->save() ;
        }

        return $return;
    }


    public function delete()
    {
        parent::delete();
    }




    private function finalize()
    {
        $pdf = self::makePDF($this->id, false);


        // save accounting entry
        $this->getEcritureComptable($this);
    }


    public static function makePDF($id, $echo = true)
    {
        $data = [];

        $data['invoice'] = self::where("id", $id)->first();

        if ($data['invoice']->finalized == 1 && $data['invoice']->final_pdf != "" && Storage::isFileExists($data['invoice']->final_pdf)) {
            $pdfFilePath = $data['invoice']->final_pdf;
        } else {

            // load contact
            if ($data['invoice']->id_contact) {
                $data['contact'] = Contacts::find($data['invoice']->id_contact);
            }



            // load company
            if ($data['invoice']->id_company) {
                $data['company'] = Companies::find($data['invoice']->id_company);
            }






            $data['lines'] = InvoiceLines::getFromInvoice($id) ;
            $data['tableTaxes'] = InvoiceTaxes::getTableTaxe($id);

            $data['showDiscount'] = false;
            $data['tvas'] = [];
            foreach ($data['lines'] as $line) {
                if (floatval($line->discount) > 0) {
                    $data['showDiscount'] = true;
                }
            }

            //load the view and saved it into $html variable
            $html = view("invoices/PDF", $data, BASEPATH . 'App/com_zeapps_crm/views/')->getContent();

            $nomPDF = $data['invoice']->name_company . '_' . $data['invoice']->numerotation . '_' . $data['invoice']->libelle;
            $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
            $nomPDF = trim($nomPDF, '_');


            //this the the PDF filename that user will get to download
            if ($data['invoice']->finalized == 1) {
                $pdfFilePath = Storage::getFolder("crm_invoice") . $nomPDF . '.pdf';
            } else {
                $pdfFilePath = Storage::getTempFolder() . $nomPDF . '.pdf';
            }


            //set the PDF header
            $mpdf = new Mpdf();

            //generate the PDF from the given html
            $mpdf->WriteHTML($html);

            //download it.
            $mpdf->Output(BASEPATH . $pdfFilePath, "F");

            if ($data['invoice']->finalized == 1) {
                $data['invoice']->final_pdf = $pdfFilePath;
                $data['invoice']->save();
            }
        }


        return $pdfFilePath;
    }


    public static function turnoverByYearsOf($id = 0, $src = 'contact')
    {
        $invoices = Capsule::table('com_zeapps_crm_invoices')
            ->select(Capsule::raw('SUM(total_ht) as total_ht, YEAR(date_creation) as year'))
            ->where("finalized", 1)
            ->where("id_" . $src, $id)
            ->where("deleted_at", null)
            ->groupBy(Capsule::raw("YEAR(date_creation)"))
            ->orderBy(Capsule::raw("YEAR(date_creation)"), "DESC")
            ->get();

        return $invoices;
    }



    private function getEcritureComptable($invoice) {
        if ($invoice && $invoice->id) {
            $ecritureComptable = [];
            $lines = InvoiceLines::getFromInvoice($invoice->id);
            $taxes = Taxes::all();

            foreach ($lines as $line) {
                $ecritureComptaRetour = $this->updateLine($invoice, $line, $taxes);
                $ecritureComptable = $this->fuisionTableTaxe($ecritureComptable, $ecritureComptaRetour);
            }



            $ecritureProduit = array();
            $ecritureTVA = array();

            foreach ($ecritureComptable as $ecriture) {

                if (!isset($ecritureProduit[$ecriture["accounting_number"]])) {
                    $ecritureProduit[$ecriture["accounting_number"]] = $ecriture["total_ht"] * 1 ;
                } else {
                    $ecritureProduit[$ecriture["accounting_number"]] += $ecriture["total_ht"] * 1 ;
                }


                if (!isset($ecritureTVA[$ecriture["accounting_number_taxe"]])) {
                    $ecritureTVA[$ecriture["accounting_number_taxe"]] = array();
                }

                $value_taxe = $ecriture["value_taxe"] . "" ;
                if (!isset($ecritureTVA[$ecriture["accounting_number_taxe"]][$value_taxe])) {
                    $ecritureTVA[$ecriture["accounting_number_taxe"]][$value_taxe] = $ecriture["total_ht"] * 1 ;
                } else {
                    $ecritureTVA[$ecriture["accounting_number_taxe"]][$value_taxe] += $ecriture["total_ht"] * 1 ;
                }
            }



            $total = 0 ;

            // accounting entries for products
            foreach ($ecritureProduit as $accounting_number => $amount) {
                $debit = 0 ;
                $credit = 0 ;

                $label = "" ;

                if (trim($invoice->name_company) != "") {
                    $label = trim($invoice->name_company) ;
                } elseif (trim($invoice->name_contact) != "") {
                    $label = trim($invoice->name_contact) ;
                }

                if ($label != "" && trim($invoice->libelle) != "") {
                    $label .= " - " ;
                }

                $label .= trim($invoice->libelle);

                if (strlen($label) > 255) {
                    $label = substr($label, 0, 255);
                }

                if ($amount != 0) {
                    if ($amount > 0) {
                        $credit = $amount;
                    } else {
                        $debit = $amount;
                    }

                    $objAccountingEntries = new AccountingEntries();
                    $objAccountingEntries->id_invoice = $invoice->id;
                    $objAccountingEntries->accounting_number = $accounting_number;
                    $objAccountingEntries->number_document = $invoice->numerotation;
                    $objAccountingEntries->label = $label;
                    $objAccountingEntries->debit = $debit;
                    $objAccountingEntries->credit = $credit;
                    $objAccountingEntries->code_journal = "VE";
                    $objAccountingEntries->date_writing = $invoice->date_creation;
                    $objAccountingEntries->date_limit = $invoice->date_limit;
                    $objAccountingEntries->save();

                    $total += $amount;
                }
            }

            // accounting entries for taxes
            foreach ($ecritureTVA as $accounting_number => $value) {
                foreach ($value as $value_taxe => $amount) {
                    $value_taxe *= 1 ;
                    $debit = 0;
                    $credit = 0;

                    $amount_taxe = round($amount * $value_taxe / 100,2) ;

                    if ($amount_taxe != 0) {
                        if ($amount_taxe > 0) {
                            $credit = $amount_taxe;
                        } else {
                            $debit = $amount_taxe * -1;
                        }

                        $objAccountingEntries = new AccountingEntries();
                        $objAccountingEntries->id_invoice = $invoice->id;
                        $objAccountingEntries->accounting_number = $accounting_number;
                        $objAccountingEntries->number_document = $invoice->numerotation;
                        $objAccountingEntries->label = $label;
                        $objAccountingEntries->debit = $debit;
                        $objAccountingEntries->credit = $credit;
                        $objAccountingEntries->code_journal = "VE";
                        $objAccountingEntries->date_writing = $invoice->date_creation;
                        $objAccountingEntries->date_limit = $invoice->date_limit;
                        $objAccountingEntries->save();

                        $total += $amount_taxe;
                    }
                }
            }

            // accounting entries for customer
            if ($total != 0) {
                $debit = 0;
                $credit = 0;

                if ($amount < 0) {
                    $credit = $total * -1;
                } else {
                    $debit = $total;
                }

                $objAccountingEntries = new AccountingEntries();
                $objAccountingEntries->id_invoice = $invoice->id;
                $objAccountingEntries->accounting_number = $invoice->accounting_number;
                $objAccountingEntries->number_document = $invoice->numerotation;
                $objAccountingEntries->label = $label;
                $objAccountingEntries->debit = $debit;
                $objAccountingEntries->credit = $credit;
                $objAccountingEntries->code_journal = "VE";
                $objAccountingEntries->date_writing = $invoice->date_creation;
                $objAccountingEntries->date_limit = $invoice->date_limit;
                $objAccountingEntries->save();
            }
        }
    }


    private function updatePrice($invoice)
    {
        if ($invoice && $invoice->id) {
            $ecritureComptable = [];
            $lines = InvoiceLines::getFromInvoice($invoice->id);
            $taxes = Taxes::all();

            foreach ($lines as $line) {
                $ecritureComptaRetour = $this->updateLine($invoice, $line, $taxes);
                $ecritureComptable = $this->fuisionTableTaxe($ecritureComptable, $ecritureComptaRetour);
            }


            $total_ht = 0 ;
            $total_tva = 0 ;
            $total_ttc = 0 ;

            $total_ht_before_discount = 0 ;
            $total_ttc_before_discount = 0 ;



            // sauvegarde les lignes comptables
            InvoiceTaxes::where("id_invoice", $invoice->id)->delete();



            $ecritureTVA = array();

            foreach ($ecritureComptable as $ecriture) {
                $value_taxe = $ecriture["value_taxe"] . "" ;
                if (!isset($ecritureTVA[$value_taxe])) {
                    $ecritureTVA[$value_taxe] = $ecriture["total_ht"] * 1 ;
                } else {
                    $ecritureTVA[$value_taxe] += $ecriture["total_ht"] * 1 ;
                }
            }





            foreach ($ecritureTVA as $value_taxe => $total_amount_ht) {
                $value_taxe = str_replace(",", ".", $value_taxe);
                $value_taxe *= 1 ;

                $total_amount_ht *= 1 ;
                $amount_taxe = round($total_amount_ht * $value_taxe / 100, 2) ;


                $objQuoteTaxes = new InvoiceTaxes();
                $objQuoteTaxes->id_invoice = $invoice->id;
                $objQuoteTaxes->base_tax = $total_amount_ht;
                $objQuoteTaxes->id_taxe = 0;
                $objQuoteTaxes->value_rate_tax = $value_taxe;
                $objQuoteTaxes->amount_tax = $amount_taxe;
                $objQuoteTaxes->accounting_number = "";
                $objQuoteTaxes->accounting_number_taxe = "";
                $objQuoteTaxes->total_ttc = $total_amount_ht + $amount_taxe;

                if ($amount_taxe != 0) {
                    $objQuoteTaxes->save();
                }


                $total_ht += $objQuoteTaxes->base_tax * 1 ;
                $total_tva += $objQuoteTaxes->amount_tax * 1 ;
                $total_ttc += $objQuoteTaxes->base_tax * 1 + $objQuoteTaxes->amount_tax * 1 ;

                $total_ht_before_discount += $ecriture["total_ht_before_discount"] ;
                $total_ttc_before_discount += $ecriture["total_ttc_before_discount"] ;
            }



            // calcul le montant total du document
            self::where('id', $invoice->id)->update(
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

    private function updateLine($invoice, $line, $taxes, $discount_prohibited = 0) {
        $ecritureComptable = [] ;


        // si c'est une ligne composée
        if (isset($line->sublines) && count($line->sublines)) {
            foreach ($line->sublines as $subline) {
                $ecritureComptable = $this->fuisionTableTaxe($ecritureComptable, $this->updateLine($invoice, $subline, $taxes, $discount_prohibited || $line->discount_prohibited));
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
        if ($invoice->global_discount > 0 && $line->id_parent == 0) {
            $ecritureComptable = $this->appliqueRemise($ecritureComptable, $invoice->global_discount, $discount_prohibited || $line->discount_prohibited);
        }



        // calcul le prix total de la ligne
        $total_ht = 0 ;
        $total_ttc = 0 ;
        foreach ($ecritureComptable as &$ecriture) {
            $total_ht += $ecriture["total_ht"] * 1 ;
            $total_ttc += $ecriture["total_ttc"] * 1 ;
        }


        // Sauvergarder le prix unitaire
        $line = InvoiceLines::find($line->id) ;
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