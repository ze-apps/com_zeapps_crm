<?php

namespace App\com_zeapps_crm\Observer;

use Zeapps\Core\iObserver ;
use Zeapps\Models\CronModel;

use App\com_zeapps_crm\Models\Quote\Quotes;
use App\com_zeapps_crm\Models\Order\Orders;
use App\com_zeapps_crm\Models\Invoice\Invoices;
use App\com_zeapps_crm\Models\Delivery\Deliveries;

use App\com_zeapps_crm\Models\Quote\QuoteActivities;
use App\com_zeapps_crm\Models\Order\OrderActivities;
use App\com_zeapps_crm\Models\Invoice\InvoiceActivities;
use App\com_zeapps_crm\Models\Delivery\DeliveryActivities;

use App\com_zeapps_contact\Models\Address;

class CrmObserver implements iObserver
{
    public static function action($transmitterClassName = '', $actionName = '', $arrayParam = array(), $callBack = null) {
        if ($transmitterClassName == 'com_zeapps_contact' && $actionName == 'save') {
            echo "ok CRM observer<br>" ;

            // rappel de la callback
            $callBack('super utile pour envoyer des onglets dans les vues') ;
        } elseif ($transmitterClassName == 'com_zeapps_crm_activites' && $actionName == 'getInfoSource') {
            if ($arrayParam && gettype($arrayParam) == "object" && get_class($arrayParam) == "App\com_zeapps_crm\Models\Activity\ActivityConnection") {
                if ($arrayParam->table == "com_zeapps_crm_quote_activities") {
                    $quoteActivity = QuoteActivities::where("id", $arrayParam->id_table)->first();
                    if ($quoteActivity) {
                        // recupère le document
                        $quote = Quotes::where("id", $quoteActivity->id_quote)->first();
                        if ($quote) {
                            $arrayParam->url_to_document = "/ng/com_zeapps_crm/quote/" . $quote->id ;

                            $address = Address::getTextAddress($quote->id_company, $quote->id_company_address_billing, $quote->id_contact, $quote->id_contact_address_billing);
                            $arrayParam->info_source = __t("Quote") . " #" . $quote->numerotation . "\n" . $address;
                        }
                    }

                } else if ($arrayParam->table == "com_zeapps_crm_order_activities") {
                    $orderActivities= OrderActivities::where("id", $arrayParam->id_table)->first();
                    if ($orderActivities) {
                        // recupère le document
                        $order = Orders::where("id", $orderActivities->id_order)->first();
                        if ($order) {
                            $arrayParam->url_to_document = "/ng/com_zeapps_crm/order/" . $order->id ;

                            $address = Address::getTextAddress($order->id_company, $order->id_company_address_billing, $order->id_contact, $order->id_contact_address_billing);
                            $arrayParam->info_source = __t("Order") . " #" . $order->numerotation . "\n" . $address;
                        }
                    }
                    

                } else if ($arrayParam->table == "com_zeapps_crm_invoice_activities") {
                    $invoiceActivity = InvoiceActivities::where("id", $arrayParam->id_table)->first();
                    if ($invoiceActivity) {
                        // recupère le document
                        $invoice = Invoices::where("id", $invoiceActivity->id_invoice)->first();
                        if ($invoice) {
                            $arrayParam->url_to_document = "/ng/com_zeapps_crm/invoice/" . $invoice->id ;

                            $address = Address::getTextAddress($invoice->id_company, $invoice->id_company_address_billing, $invoice->id_contact, $invoice->id_contact_address_billing);
                            $arrayParam->info_source = __t("Invoice") . " #" . $invoice->numerotation . "\n" . $address;
                        }
                    }

                } else if ($arrayParam->table == "com_zeapps_crm_delivery_activities") {
                    $deliveryActivity = DeliveryActivities::where("id", $arrayParam->id_table)->first();
                    if ($deliveryActivity) {
                        // recupère le document
                        $delivery = Deliveries::where("id", $deliveryActivity->id_delivery)->first();
                        if ($delivery) {
                            $arrayParam->url_to_document = "/ng/com_zeapps_crm/delivery/" . $delivery->id ;

                            $address = Address::getTextAddress($delivery->id_company, $delivery->id_company_address_billing, $delivery->id_contact, $delivery->id_contact_address_billing);
                            $arrayParam->info_source = __t("Delivery") . " #" . $delivery->numerotation . "\n" . $address;
                        }
                    }
                }
            }
        }
    }




    public static function getHook() {
        $retour = array();

        // déclaration de l'onglet devis pour les entreprises
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_EntrepriseHook" ;
        $hook->template = "/com_zeapps_crm/quotes/lists_partial" ;
        $hook->label = __t("Quote") ;
        $hook->shown = 1 ;
        $hook->sort = 1 ;
        $retour[] = $hook ;


        // déclaration de l'onglet commandes pour les entreprises
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_EntrepriseHook" ;
        $hook->template = "/com_zeapps_crm/orders/lists_partial" ;
        $hook->label = __t("Orders") ;
        $hook->shown = 1 ;
        $hook->sort = 2 ;
        $retour[] = $hook ;



        // déclaration de l'onglet factures pour les entreprises
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_EntrepriseHook" ;
        $hook->template = "/com_zeapps_crm/invoices/lists_partial" ;
        $hook->label = __t("Invoices") ;
        $hook->shown = 1 ;
        $hook->sort = 3 ;
        $retour[] = $hook ;


        // déclaration de l'onglet livraison pour les entreprises
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_EntrepriseHook" ;
        $hook->template = "/com_zeapps_crm/deliveries/lists_partial" ;
        $hook->label = __t("Deliveries") ;
        $hook->shown = 1 ;
        $hook->sort = 4 ;
        $retour[] = $hook ;


        // déclaration de l'onglet Encaissement pour les entreprises
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_EntrepriseHook" ;
        $hook->template = "/com_zeapps_crm/payment/lists_partial" ;
        $hook->label = __t("Cashing");
        $hook->shown = 1 ;
        $hook->sort = 5 ;
        $retour[] = $hook ;













        // déclaration de l'onglet devis pour les contacts
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_ContactHook" ;
        $hook->template = "/com_zeapps_crm/quotes/lists_partial" ;
        $hook->label = __t("Quote") ;
        $hook->shown = 1 ;
        $hook->sort = 1 ;
        $retour[] = $hook ;


        // déclaration de l'onglet commandes pour les contacts
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_ContactHook" ;
        $hook->template = "/com_zeapps_crm/orders/lists_partial" ;
        $hook->label = __t("Orders") ;
        $hook->shown = 1 ;
        $hook->sort = 2 ;
        $retour[] = $hook ;



        // déclaration de l'onglet factures pour les contacts
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_ContactHook" ;
        $hook->template = "/com_zeapps_crm/invoices/lists_partial" ;
        $hook->label = __t("Invoices") ;
        $hook->shown = 1 ;
        $hook->sort = 3 ;
        $retour[] = $hook ;


        // déclaration de l'onglet livraison pour les contacts
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_ContactHook" ;
        $hook->template = "/com_zeapps_crm/deliveries/lists_partial" ;
        $hook->label = __t("Deliveries") ;
        $hook->shown = 1 ;
        $hook->sort = 4 ;
        $retour[] = $hook ;



        // déclaration de l'onglet Encaissement pour les contacts
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_ContactHook" ;
        $hook->template = "/com_zeapps_crm/payment/lists_partial" ;
        $hook->label = __t("Cashing");
        $hook->shown = 1 ;
        $hook->sort = 5 ;
        $retour[] = $hook ;








        return $retour ;
    }


    public static function getCron() {
        $retour = array();

        // to update prices following a change in the price grid
        $cron = new CronModel() ;
        $cron->command = "App\\com_zeapps_crm\\Controllers\\Cron@updatePriceList" ;
        $retour[] = $cron ;


        // to update prices following a change in the price grid
        $cron = new CronModel() ;
        $cron->command = "App\\com_zeapps_crm\\Controllers\\Cron@updatePriceProduct" ;
        $retour[] = $cron ;

        return $retour ;
    }
}