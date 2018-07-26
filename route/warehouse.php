<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/warehouse/get/{id}", 'App\\com_zeapps_crm\\Controllers\\Warehouse@get');
Routeur::get("/com_zeapps_crm/warehouse/getAll/", 'App\\com_zeapps_crm\\Controllers\\Warehouse@getAll');
Routeur::post("/com_zeapps_crm/warehouse/save/", 'App\\com_zeapps_crm\\Controllers\\Warehouse@save');
Routeur::post("/com_zeapps_crm/warehouse/save_all/", 'App\\com_zeapps_crm\\Controllers\\Warehouse@save_all');
Routeur::post("/com_zeapps_crm/warehouse/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Warehouse@delete');
