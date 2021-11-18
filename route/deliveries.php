<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/deliveries/lists", 'App\\com_zeapps_crm\\Controllers\\Deliveries@lists');
Routeur::get("/com_zeapps_crm/deliveries/lists_partial", 'App\\com_zeapps_crm\\Controllers\\Deliveries@lists_partial');
Routeur::get("/com_zeapps_crm/deliveries/form_modal", 'App\\com_zeapps_crm\\Controllers\\Deliveries@form_modal');
Routeur::get("/com_zeapps_crm/deliveries/view", 'App\\com_zeapps_crm\\Controllers\\Deliveries@view');
Routeur::get("/com_zeapps_crm/deliveries/form_line", 'App\\com_zeapps_crm\\Controllers\\Deliveries@form_line');


Routeur::post("/com_zeapps_crm/deliveries/testFormat", 'App\\com_zeapps_crm\\Controllers\\Deliveries@testFormat');
Routeur::get("/com_zeapps_crm/deliveries/get/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@get');
Routeur::post("/com_zeapps_crm/deliveries/getAll/{id}/{type}/{limit}/{offset}/{context}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@getAll');
Routeur::post("/com_zeapps_crm/deliveries/modal/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@modal');
Routeur::post("/com_zeapps_crm/deliveries/save", 'App\\com_zeapps_crm\\Controllers\\Deliveries@save');
Routeur::post("/com_zeapps_crm/deliveries/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@delete');
Routeur::post("/com_zeapps_crm/deliveries/transform/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@transform');

Routeur::post("/com_zeapps_crm/deliveries/finalize/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@finalize');


Routeur::post("/com_zeapps_crm/deliveries/saveLine", 'App\\com_zeapps_crm\\Controllers\\Deliveries@saveLine');
Routeur::post("/com_zeapps_crm/deliveries/updateLinePosition/", 'App\\com_zeapps_crm\\Controllers\\Deliveries@updateLinePosition');
Routeur::post("/com_zeapps_crm/deliveries/deleteLine/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@deleteLine');
Routeur::post("/com_zeapps_crm/deliveries/saveLineDetail", 'App\\com_zeapps_crm\\Controllers\\Deliveries@saveLineDetail');

Routeur::post("/com_zeapps_crm/deliveries/activity", 'App\\com_zeapps_crm\\Controllers\\Deliveries@activity');
Routeur::post("/com_zeapps_crm/deliveries/del_activity/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@del_activity');


Routeur::post("/com_zeapps_crm/deliveries/makePDF/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@makePDF');


Routeur::post("/com_zeapps_crm/deliveries/uploadDocuments/{idDelivery}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@uploadDocuments');
Routeur::get("/com_zeapps_crm/deliveries/del_document/{id}", 'App\\com_zeapps_crm\\Controllers\\Deliveries@del_document');

Routeur::get("/com_zeapps_crm/deliveries/getPDF/", 'App\\com_zeapps_crm\\Controllers\\Deliveries@getPDF');
