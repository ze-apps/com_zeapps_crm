<?php
use Zeapps\Core\Routeur ;



Routeur::get("/com_zeapps_crm/stock/view", 'App\\com_zeapps_crm\\Controllers\\Stock@view');
Routeur::get("/com_zeapps_crm/stock/details", 'App\\com_zeapps_crm\\Controllers\\Stock@details');
Routeur::get("/com_zeapps_crm/stock/chart", 'App\\com_zeapps_crm\\Controllers\\Stock@chart');
Routeur::get("/com_zeapps_crm/stock/history", 'App\\com_zeapps_crm\\Controllers\\Stock@history');
//Routeur::get("/com_zeapps_crm/stock/form_modal", 'App\\com_zeapps_crm\\Controllers\\Stock@form_modal');
Routeur::get("/com_zeapps_crm/stock/form_transfert", 'App\\com_zeapps_crm\\Controllers\\Stock@form_transfert');
Routeur::get("/com_zeapps_crm/stock/form_mvt", 'App\\com_zeapps_crm\\Controllers\\Stock@form_mvt');
//Routeur::get("/com_zeapps_crm/stock/modal", 'App\\com_zeapps_crm\\Controllers\\Stock@modal');




Routeur::get("/com_zeapps_crm/stock/get/{id_product}/{id_warehouse}", 'App\\com_zeapps_crm\\Controllers\\Stock@get');
Routeur::get("/com_zeapps_crm/stock/get_movements/{id_product}/{id_warehouse}/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\Stock@get_movements');
Routeur::post("/com_zeapps_crm/stock/getAll/{id}/{limit}/{offset}/{context}", 'App\\com_zeapps_crm\\Controllers\\Stock@getAll');
Routeur::post("/com_zeapps_crm/stock/export/{id}/{limit}/{offset}/{context}", 'App\\com_zeapps_crm\\Controllers\\Stock@export');
Routeur::post("/com_zeapps_crm/stock/save/{id_warehouse}", 'App\\com_zeapps_crm\\Controllers\\Stock@save');
Routeur::post("/com_zeapps_crm/stock/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Stock@delete');
Routeur::post("/com_zeapps_crm/stock/add_mvt/", 'App\\com_zeapps_crm\\Controllers\\Stock@add_mvt');
Routeur::post("/com_zeapps_crm/stock/add_transfert/", 'App\\com_zeapps_crm\\Controllers\\Stock@add_transfert');
Routeur::post("/com_zeapps_crm/stock/ignore_mvt/{id}/{value}/{id_product}/{id_warehouse}", 'App\\com_zeapps_crm\\Controllers\\Stock@ignore_mvt');
Routeur::get("/com_zeapps_crm/stock/get_export/{link}", 'App\\com_zeapps_crm\\Controllers\\Stock@get_export');

