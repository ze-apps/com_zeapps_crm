<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/product/get/{id}", 'App\\com_zeapps_crm\\Controllers\\Product@get');
Routeur::get("/com_zeapps_crm/product/get_code/{id}", 'App\\com_zeapps_crm\\Controllers\\Product@get_code');
Routeur::get("/com_zeapps_crm/product/getAll", 'App\\com_zeapps_crm\\Controllers\\Product@getAll');
Routeur::post("/com_zeapps_crm/product/modal/{id}/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\Product@modal');
Routeur::post("/com_zeapps_crm/product/save", 'App\\com_zeapps_crm\\Controllers\\Product@save');
Routeur::post("/com_zeapps_crm/product/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Product@delete');
Routeur::get("/com_zeapps_crm/product/updateRatio", 'App\\com_zeapps_crm\\Controllers\\Product@updateRatio');