<?php
use Zeapps\Core\Routeur ;


Routeur::get("/com_zeapps_crm/payment/lists_partial", 'App\\com_zeapps_crm\\Controllers\\Payment@lists_partial');
Routeur::get("/com_zeapps_crm/payment/form_modal", 'App\\com_zeapps_crm\\Controllers\\Payment@form_modal');
Routeur::get("/com_zeapps_crm/payment/view_modal", 'App\\com_zeapps_crm\\Controllers\\Payment@view_modal');

Routeur::get("/com_zeapps_crm/payment/get/{id}", 'App\\com_zeapps_crm\\Controllers\\Payment@get');
Routeur::post("/com_zeapps_crm/payment/getAll/{id}/{type}/{limit}/{offset}/{context}", 'App\\com_zeapps_crm\\Controllers\\Payment@getAll');
Routeur::post("/com_zeapps_crm/payment/modal/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\Payment@modal');
Routeur::post("/com_zeapps_crm/payment/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Payment@delete');
Routeur::post("/com_zeapps_crm/payment/save", 'App\\com_zeapps_crm\\Controllers\\Payment@save');

/*Routeur::get("/com_zeapps_crm/payment/lists", 'App\\com_zeapps_crm\\Controllers\\Payment@lists');

Routeur::get("/com_zeapps_crm/payment/form_line", 'App\\com_zeapps_crm\\Controllers\\Payment@form_line');

Routeur::post("/com_zeapps_crm/payment/saveLine", 'App\\com_zeapps_crm\\Controllers\\Payment@saveLine');
Routeur::post("/com_zeapps_crm/payment/deleteLine/{id}", 'App\\com_zeapps_crm\\Controllers\\Payment@deleteLine');
Routeur::post("/com_zeapps_crm/payment/saveLineDetail", 'App\\com_zeapps_crm\\Controllers\\Payment@saveLineDetail');
*/
