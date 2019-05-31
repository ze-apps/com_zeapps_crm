<?php

namespace App\com_zeapps_crm\Observer;

use Zeapps\Core\iObserver ;
use Zeapps\Models\CronModel;

class CrmObserver implements iObserver
{
    public static function action($transmitterClassName = '', $actionName = '', $arrayParam = array(), $callBack = null) {
        if ($transmitterClassName == 'com_zeapps_contact' && $actionName == 'save') {
            echo "ok CRM observer<br>" ;

            // rappel de la callback
            $callBack('super utile pour envoyer des onglets dans les vues') ;
        }
    }




    public static function getHook() {
        $retour = array();

        // déclaration de l'onglet devis pour les entreprises
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_EntrepriseHook" ;
        $hook->template = "/com_zeapps_crm/quotes/lists_partial" ;
        $hook->label = "Devis" ;
        $hook->shown = 1 ;
        $hook->sort = 1 ;
        $retour[] = $hook ;


        // déclaration de l'onglet commandes pour les entreprises
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_EntrepriseHook" ;
        $hook->template = "/com_zeapps_crm/orders/lists_partial" ;
        $hook->label = "Commande" ;
        $hook->shown = 1 ;
        $hook->sort = 2 ;
        $retour[] = $hook ;



        // déclaration de l'onglet factures pour les entreprises
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_EntrepriseHook" ;
        $hook->template = "/com_zeapps_crm/invoices/lists_partial" ;
        $hook->label = "Facture" ;
        $hook->shown = 1 ;
        $hook->sort = 3 ;
        $retour[] = $hook ;


        // déclaration de l'onglet livraison pour les entreprises
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_EntrepriseHook" ;
        $hook->template = "/com_zeapps_crm/deliveries/lists_partial" ;
        $hook->label = "Livraison" ;
        $hook->shown = 1 ;
        $hook->sort = 4 ;
        $retour[] = $hook ;


        // déclaration de l'onglet Encaissement pour les entreprises
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_EntrepriseHook" ;
        $hook->template = "/com_zeapps_crm/payment/lists_partial" ;
        $hook->label = "Encaissement" ;
        $hook->shown = 1 ;
        $hook->sort = 5 ;
        $retour[] = $hook ;













        // déclaration de l'onglet devis pour les contacts
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_ContactHook" ;
        $hook->template = "/com_zeapps_crm/quotes/lists_partial" ;
        $hook->label = "Devis" ;
        $hook->shown = 1 ;
        $hook->sort = 1 ;
        $retour[] = $hook ;


        // déclaration de l'onglet commandes pour les contacts
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_ContactHook" ;
        $hook->template = "/com_zeapps_crm/orders/lists_partial" ;
        $hook->label = "Commande" ;
        $hook->shown = 1 ;
        $hook->sort = 2 ;
        $retour[] = $hook ;



        // déclaration de l'onglet factures pour les contacts
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_ContactHook" ;
        $hook->template = "/com_zeapps_crm/invoices/lists_partial" ;
        $hook->label = "Facture" ;
        $hook->shown = 1 ;
        $hook->sort = 3 ;
        $retour[] = $hook ;


        // déclaration de l'onglet livraison pour les contacts
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_ContactHook" ;
        $hook->template = "/com_zeapps_crm/deliveries/lists_partial" ;
        $hook->label = "Livraison" ;
        $hook->shown = 1 ;
        $hook->sort = 4 ;
        $retour[] = $hook ;



        // déclaration de l'onglet Encaissement pour les contacts
        $hook = new \stdClass();
        $hook->hook = "comZeappsContact_ContactHook" ;
        $hook->template = "/com_zeapps_crm/payment/lists_partial" ;
        $hook->label = "Encaissement" ;
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