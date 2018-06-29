<?php

namespace App\com_zeapps_crm\Observer;

use Zeapps\Core\iObserver ;

class CrmObserver implements iObserver
{
    public static function action($transmitterClassName = '', $actionName = '', $arrayParam = array(), $callBack = null) {
        if ($transmitterClassName == 'com_zeapps_contact' && $actionName == 'save') {
            echo "ok CRM observer<br>" ;

            // rappel de la callback
            $callBack('super utile pour envoyer des onglets dans les vues') ;
        }
    }
}