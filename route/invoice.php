<?php
use Zeapps\Core\Routeur ;





Routeur::post("/com_zeapps_crm/invoices/testFormat", 'App\\com_zeapps_crm\\Controllers\\Invoices@testFormat');
Routeur::get("/com_zeapps_crm/invoices/get/{id}", 'App\\com_zeapps_crm\\Controllers\\Invoices@get');
Routeur::post("/com_zeapps_crm/invoices/getAll/{id_project}/{type}/{limit}/{offset}/{context}", 'App\\com_zeapps_crm\\Controllers\\Invoices@getAll');
Routeur::post("/com_zeapps_crm/invoices/modal/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\Invoices@modal');
Routeur::post("/com_zeapps_crm/invoices/save", 'App\\com_zeapps_crm\\Controllers\\Invoices@save');
Routeur::post("/com_zeapps_crm/invoices/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Invoices@delete');
Routeur::post("/com_zeapps_crm/invoices/transform/{id}", 'App\\com_zeapps_crm\\Controllers\\Invoices@transform');
Routeur::post("/com_zeapps_crm/invoices/finalize/{id}", 'App\\com_zeapps_crm\\Controllers\\Invoices@finalize');

Routeur::post("/com_zeapps_crm/invoices/saveLine", 'App\\com_zeapps_crm\\Controllers\\Invoices@saveLine');
Routeur::post("/com_zeapps_crm/invoices/updateLinePosition/", 'App\\com_zeapps_crm\\Controllers\\Invoices@updateLinePosition');
Routeur::post("/com_zeapps_crm/invoices/deleteLine/{id}", 'App\\com_zeapps_crm\\Controllers\\Invoices@deleteLine');
Routeur::post("/com_zeapps_crm/invoices/saveLineDetail", 'App\\com_zeapps_crm\\Controllers\\Invoices@saveLineDetail');

Routeur::post("/com_zeapps_crm/invoices/activity", 'App\\com_zeapps_crm\\Controllers\\Invoices@activity');
Routeur::post("/com_zeapps_crm/invoices/del_activity/{id}", 'App\\com_zeapps_crm\\Controllers\\Invoices@del_activity');

Routeur::get("/com_zeapps_crm/invoices/uploadDocuments/", 'App\\com_zeapps_crm\\Controllers\\Invoices@uploadDocuments');
Routeur::post("/com_zeapps_crm/invoices/del_document/{id}", 'App\\com_zeapps_crm\\Controllers\\Invoices@del_document');

Routeur::get("/com_zeapps_crm/invoices/getPDF/", 'App\\com_zeapps_crm\\Controllers\\Invoices@getPDF');
Routeur::post("/com_zeapps_crm/invoices/makePDF/{id}", 'App\\com_zeapps_crm\\Controllers\\Invoices@makePDF');


