<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/stock/get/{id_stock}/{id_warehouse}", 'App\\com_zeapps_crm\\Controllers\\Stock@get');
Routeur::get("/com_zeapps_crm/stock/get_movements/{id_stock}/{id_warehouse}/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\Stock@get_movements');
Routeur::post("/com_zeapps_crm/stock/getAll/{limit}/{offset}/{context}", 'App\\com_zeapps_crm\\Controllers\\Stock@getAll');
Routeur::post("/com_zeapps_crm/stock/save/{id_warehouse}", 'App\\com_zeapps_crm\\Controllers\\Stock@save');
Routeur::post("/com_zeapps_crm/stock/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Stock@delete');
Routeur::post("/com_zeapps_crm/stock/add_mvt/", 'App\\com_zeapps_crm\\Controllers\\Stock@add_mvt');
Routeur::post("/com_zeapps_crm/stock/add_transfert/", 'App\\com_zeapps_crm\\Controllers\\Stock@add_transfert');
Routeur::post("/com_zeapps_crm/stock/ignore_mvt/{id}/{value}/{id_stock}/{id_warehouse}", 'App\\com_zeapps_crm\\Controllers\\Stock@ignore_mvt');

