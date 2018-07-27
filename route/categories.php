<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/categories/form", 'App\\com_zeapps_crm\\Controllers\\Categories@form');




Routeur::get("/com_zeapps_crm/categories/get_tree", 'App\\com_zeapps_crm\\Controllers\\Categories@get_tree');
Routeur::get("/com_zeapps_crm/categories/get/{id}", 'App\\com_zeapps_crm\\Controllers\\Categories@get');
Routeur::post("/com_zeapps_crm/categories/save", 'App\\com_zeapps_crm\\Controllers\\Categories@save');
Routeur::post("/com_zeapps_crm/categories/update_order", 'App\\com_zeapps_crm\\Controllers\\Categories@update_order');
Routeur::post("/com_zeapps_crm/categories/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Categories@delete');
Routeur::post("/com_zeapps_crm/categories/delete_force/{id}/{force}", 'App\\com_zeapps_crm\\Controllers\\Categories@delete');


