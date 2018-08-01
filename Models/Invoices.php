<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Zeapps\Models\Config;
use App\com_zeapps_crm\Models\InvoiceLines;
use App\com_zeapps_crm\Models\InvoiceLineDetails;
use App\com_zeapps_contact\Models\Modalities;
use App\com_zeapps_crm\Models\CreditBalanceDetails;
use App\com_zeapps_crm\Models\AccountingEntries;
use App\com_zeapps_crm\Models\Taxes;

use Zeapps\Core\ModelHelper;

use Illuminate\Database\Capsule\Manager as Capsule;

class Invoices extends Model {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_invoices';
    protected $table ;


    protected $fieldModelInfo ;




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
        $this->fieldModelInfo->decimal('due', 8, 2)->default(0);
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




        parent::__construct($attributes);
    }


    public static function createFrom($src){
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


        $invoice = new Invoices() ;
        foreach (self::getSchema() as $key) {
            if (isset($src->$key)) {
                $invoice->$key = $src->$key;
            }
        }
        $invoice->date_creation = date('Y-m-d');
        $invoice->finalized = 0;
        $invoice->save() ;
        $id = $invoice->id;


        $new_id_lines = [];

        if(isset($src->lines)){
            foreach($src->lines as $line){
                $old_id = $line->id;

                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);


                $invoiceLine = new InvoiceLines() ;
                foreach (InvoiceLines::getSchema() as $key) {
                    if (isset($line->$key)) {
                        $invoiceLine->$key = $line->$key;
                    }
                }
                $invoiceLine->id_invoice = $id;
                $invoiceLine->save() ;


                $new_id_lines[$old_id] = $invoiceLine->id;
            }
        }

        if(isset($src->line_details)){
            foreach($src->line_details as $line) {
                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);


                $invoiceLineDetail = new InvoiceLineDetails() ;
                foreach (InvoiceLineDetails::getSchema() as $key) {
                    if (isset($line->$key)) {
                        $invoiceLineDetail->$key = $line->$key;
                    }
                }

                $invoiceLineDetail->id_invoice = $id;
                $invoiceLineDetail->id_line = $new_id_lines[$line->id_line];

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


        $finalized_orignal = $this->getOriginal("finalized") ;



        /******** clean data **********/
        $this->fieldModelInfo->cleanData($this) ;






        /**** set a document number ****/
        if ($this->finalized == 1 && (!isset($this->numerotation) || !$this->numerotation || $this->numerotation == "")) {
            $format = Config::where('id', 'crm_invoice_format')->first()->value;
            $num = self::get_numerotation();
            $this->numerotation = self::parseFormat($format, $num);

            /**** to delete unwanted field ****/
            $this->fieldModelInfo->removeFieldUnwanted($this) ;
            /**** end to delete unwanted field ****/
            parent::save($options);
        }



        if ($this->finalized == 1 && $finalized_orignal != 1) {
            $this->finalize();
        }








        /**** to delete unwanted field ****/
        $this->fieldModelInfo->removeFieldUnwanted($this) ;
        /**** end to delete unwanted field ****/

        return parent::save($options);
    }

    private function finalize() {
        $pdf = $this->makePDF($this->id, false);

        $this->final_pdf = $pdf ;
        $this->save() ;

        $lines = InvoiceLines::where("id_invoice", $this->id)->orderBy("sort")->get();
        $line_details = InvoiceLineDetails::where("id_invoice", $this->id)->get();

        if(($modality = Modalities::get($this->id_modality)) && ((int) $modality->situation !== 0)){
            $creditBalanceDetails = new CreditBalanceDetails() ;
            $creditBalanceDetails->id_invoice = $this->id ;
            $creditBalanceDetails->paid = $this->total_ttc ;
            $creditBalanceDetails->id_modality = $this->id_modality ;
            $creditBalanceDetails->label_modality = $this->label_modality ;
            $creditBalanceDetails->date_payment = date('Y-m-d')." 00:00:00" ;
            $creditBalanceDetails->save() ;
        }


        $label_entry = $this->name_company ?: ($this->name_contact ?: "");

        $entries = [];
        $tvas = [];
        foreach ($lines as $line) {
            if ((int) $line->has_detail === 0) {
                if (!isset($entries[$line->accounting_number])) {
                    $entries[$line->accounting_number] = 0;
                }

                $entries[$line->accounting_number] += floatval($line->total_ht);

                if ($line->id_taxe !== '0') {
                    if (!isset($tvas[$line->id_taxe])) {
                        $tvas[$line->id_taxe] = array(
                            'ht' => 0,
                            'value_taxe' => floatval($line->value_taxe)
                        );
                    }

                    $tvas[$line->id_taxe]['ht'] += floatval($line->total_ht);
                    $tvas[$line->id_taxe]['value'] = round(floatval($tvas[$line->id_taxe]['ht']) * ($tvas[$line->id_taxe]['value_taxe'] / 100), 2);
                }
            }
        }
        foreach ($line_details as $line) {
            if (!isset($entries[$line->accounting_number])) {
                $entries[$line->accounting_number] = 0;
            }

            $entries[$line->accounting_number] += floatval($line->total_ht);

            if ($line->id_taxe !== '0') {
                if (!isset($tvas[$line->id_taxe])) {
                    $tvas[$line->id_taxe] = array(
                        'ht' => 0,
                        'value_taxe' => floatval($line->value_taxe)
                    );
                }

                $tvas[$line->id_taxe]['ht'] += floatval($line->total_ht);
                $tvas[$line->id_taxe]['value'] = round(floatval($tvas[$line->id_taxe]['ht']) * ($tvas[$line->id_taxe]['value_taxe'] / 100), 2);
            }
        }

        foreach ($tvas as $id_taxe => $tva) {
            $taxe = Taxes::where("id", $id_taxe)->first();

            if (!isset($entries[$taxe->accounting_number])) {
                $entries[$taxe->accounting_number] = 0;
            }

            $entries[$taxe->accounting_number] += floatval($tva['value']);
        }

        foreach ($entries as $accounting_number => $sum) {

            $accountingEntries = new AccountingEntries();
            $accountingEntries->id_invoice = $this->id ;
            $accountingEntries->accounting_number = $accounting_number ;
            $accountingEntries->number_document = $this->numerotation ;
            $accountingEntries->label = $label_entry ;
            if ($sum >= 0) {
                $accountingEntries->credit = $sum ;
                $accountingEntries->debit = 0 ;
            } else {
                $accountingEntries->credit = 0 ;
                $accountingEntries->debit = $sum * -1 ;
            }

            $accountingEntries->code_journal = 'VE' ;
            $accountingEntries->date_writing = $this->date_creation ;
            $accountingEntries->date_limit = $this->date_limit ;
            $accountingEntries->save() ;
        }

        $accountingEntries = new AccountingEntries();
        $accountingEntries->id_invoice = $this->id ;
        $accountingEntries->accounting_number = $this->accounting_number ;
        $accountingEntries->number_document = $this->numerotation ;
        $accountingEntries->label = $label_entry ;
        if ($this->total_ttc >= 0) {
            $accountingEntries->credit = 0 ;
            $accountingEntries->debit = $this->total_ttc ;
        } else {
            $accountingEntries->credit = $this->total_ttc * -1 ;
            $accountingEntries->debit = 0 ;
        }

        $accountingEntries->code_journal = 'VE' ;
        $accountingEntries->date_writing = $this->date_creation ;
        $accountingEntries->date_limit = $this->date_limit ;
        $accountingEntries->save() ;
    }


    public function makePDF($id, $echo = true){
        /*$this->load->model("Zeapps_invoices", "invoices");
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");
        $this->load->model("Zeapps_invoice_line_details", "invoice_line_details");

        $data = [];

        $data['invoice'] = $this->invoices->get($id);
        $data['lines'] = $this->invoice_lines->order_by('sort')->all(array('id_invoice'=>$id));
        $line_details = $this->invoice_line_details->all(array('id_order'=>$id));

        $data['showDiscount'] = false;
        $data['tvas'] = [];
        foreach($data['lines'] as $line){
            if(floatval($line->discount) > 0)
                $data['showDiscount'] = true;

            if($line->id_taxe !== '0'){
                if(!isset($data['tvas'][$line->id_taxe])){
                    $data['tvas'][$line->id_taxe] = array(
                        'ht' => 0,
                        'value_taxe' => floatval($line->value_taxe)
                    );
                }

                $data['tvas'][$line->id_taxe]['ht'] += floatval($line->total_ht);
                $data['tvas'][$line->id_taxe]['value'] = round(floatval($data['tvas'][$line->id_taxe]['ht']) * ($data['tvas'][$line->id_taxe]['value_taxe'] / 100), 2);
            }
        }
        foreach($line_details as $line){
            if($line->id_taxe !== '0'){
                if(!isset($data['tvas'][$line->id_taxe])){
                    $data['tvas'][$line->id_taxe] = array(
                        'ht' => 0,
                        'value_taxe' => floatval($line->value_taxe)
                    );
                }

                $data['tvas'][$line->id_taxe]['ht'] += floatval($line->total_ht);
                $data['tvas'][$line->id_taxe]['value'] = round(floatval($data['tvas'][$line->id_taxe]['ht']) * ($data['tvas'][$line->id_taxe]['value_taxe'] / 100), 2);
            }
        }

        //load the view and saved it into $html variable
        $html = $this->load->view('invoices/PDF', $data, true);

        $nomPDF = $data['invoice']->name_company.'_'.$data['invoice']->numerotation.'_'.$data['invoice']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');

        recursive_mkdir(FCPATH . 'tmp/com_zeapps_crm/invoices/');

        //this the the PDF filename that user will get to download
        $pdfFilePath = FCPATH . 'tmp/com_zeapps_crm/invoices/'.$nomPDF.'.pdf';

        //set the PDF header
        $this->M_pdf->pdf->SetHeader('Facture €<br>n° : '.$data['invoice']->numerotation.'|C. Compta : '.$data['invoice']->accounting_number.'|{DATE d/m/Y}');

        //set the PDF footer
        $this->M_pdf->pdf->SetFooter('{PAGENO}/{nb}');

        //generate the PDF from the given html
        $this->M_pdf->pdf->WriteHTML($html);

        //download it.
        $this->M_pdf->pdf->Output($pdfFilePath, "F");

        if($echo) {
            echo json_encode($nomPDF);
        }*/


        $nomPDF = "test.pdf" ;

        return $nomPDF;
    }
}