<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/crm_origins/get/{id}", 'App\\com_zeapps_crm\\Controllers\\CrmOrigins@get');
Routeur::get("/com_zeapps_crm/crm_origins/getAll/", 'App\\com_zeapps_crm\\Controllers\\CrmOrigins@getAll');
Routeur::post("/com_zeapps_crm/crm_origins/save", 'App\\com_zeapps_crm\\Controllers\\CrmOrigins@save');
Routeur::post("/com_zeapps_crm/crm_origins/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\CrmOrigins@delete');


