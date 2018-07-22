<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/taxes/get/{id}", 'App\\com_zeapps_crm\\Controllers\\Taxes@get');
Routeur::get("/com_zeapps_crm/taxes/getAll/", 'App\\com_zeapps_crm\\Controllers\\Taxes@getAll');
Routeur::post("/com_zeapps_crm/taxes/save", 'App\\com_zeapps_crm\\Controllers\\Taxes@save');
Routeur::post("/com_zeapps_crm/taxes/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Taxes@delete');


