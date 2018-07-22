<?php
use Zeapps\Core\Routeur ;


Routeur::post("/com_zeapps_crm/deliveries/testFormat", 'App\\com_zeapps_crm\\Controllers\\Deliveries@testFormat');
Routeur::get("/com_zeapps_crm/deliveries/get/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@get');
Routeur::post("/com_zeapps_crm/deliveries/getAll/{id_project}/{type}/{limit}/{offset}/{context}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@getAll');
Routeur::post("/com_zeapps_crm/deliveries/modal/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@modal');
Routeur::post("/com_zeapps_crm/deliveries/save", 'App\\com_zeapps_crm\\Controllers\\Deliveries@save');
Routeur::post("/com_zeapps_crm/deliveries/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@delete');
Routeur::post("/com_zeapps_crm/deliveries/transform/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@transform');

Routeur::post("/com_zeapps_crm/deliveries/saveLine", 'App\\com_zeapps_crm\\Controllers\\Deliveries@saveLine');
Routeur::post("/com_zeapps_crm/deliveries/updateLinePosition/", 'App\\com_zeapps_crm\\Controllers\\Deliveries@updateLinePosition');
Routeur::post("/com_zeapps_crm/deliveries/deleteLine/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@deleteLine');
Routeur::post("/com_zeapps_crm/deliveries/saveLineDetail", 'App\\com_zeapps_crm\\Controllers\\Deliveries@saveLineDetail');

Routeur::post("/com_zeapps_crm/deliveries/activity", 'App\\com_zeapps_crm\\Controllers\\Deliveries@activity');
Routeur::post("/com_zeapps_crm/deliveries/del_activity/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@del_activity');

Routeur::get("/com_zeapps_crm/deliveries/uploadDocuments/", 'App\\com_zeapps_crm\\Controllers\\Deliveries@uploadDocuments');
Routeur::post("/com_zeapps_crm/deliveries/del_document/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@del_document');

Routeur::get("/com_zeapps_crm/deliveries/getPDF/", 'App\\com_zeapps_crm\\Controllers\\Deliveries@getPDF');
Routeur::post("/com_zeapps_crm/deliveries/makePDF/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@makePDF');


