<?php
use Zeapps\Core\Routeur ;


Routeur::get("/com_zeapps_crm/price-list/lists", 'App\\com_zeapps_crm\\Controllers\\PriceList@lists');
Routeur::get("/com_zeapps_crm/price-list/form_modal", 'App\\com_zeapps_crm\\Controllers\\PriceList@form_modal');
Routeur::get("/com_zeapps_crm/price-list/taux", 'App\\com_zeapps_crm\\Controllers\\PriceList@taux');



Routeur::get("/com_zeapps_crm/price-list/get/{id}", 'App\\com_zeapps_crm\\Controllers\\PriceList@get');
Routeur::get("/com_zeapps_crm/price-list/getAll", 'App\\com_zeapps_crm\\Controllers\\PriceList@getAll');
Routeur::get("/com_zeapps_crm/price-list/getAllAdmin", 'App\\com_zeapps_crm\\Controllers\\PriceList@getAllAdmin');
Routeur::get("/com_zeapps_crm/price-list/getPriceListType", 'App\\com_zeapps_crm\\Controllers\\PriceList@getPriceListType');
Routeur::post("/com_zeapps_crm/price-list/save", 'App\\com_zeapps_crm\\Controllers\\PriceList@save');
Routeur::post("/com_zeapps_crm/price-list/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\PriceList@delete');
Routeur::get("/com_zeapps_crm/price-list/rate/{id_pricelist}", 'App\\com_zeapps_crm\\Controllers\\PriceList@rate');
Routeur::post("/com_zeapps_crm/price-list/rate-save", 'App\\com_zeapps_crm\\Controllers\\PriceList@rateSave');