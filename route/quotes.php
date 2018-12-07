<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/quotes/lists", 'App\\com_zeapps_crm\\Controllers\\Quotes@lists');
Routeur::get("/com_zeapps_crm/quotes/lists_partial", 'App\\com_zeapps_crm\\Controllers\\Quotes@lists_partial');
Routeur::get("/com_zeapps_crm/quotes/form_modal", 'App\\com_zeapps_crm\\Controllers\\Quotes@form_modal');
Routeur::get("/com_zeapps_crm/quotes/view", 'App\\com_zeapps_crm\\Controllers\\Quotes@view');
Routeur::get("/com_zeapps_crm/quotes/form_line", 'App\\com_zeapps_crm\\Controllers\\Quotes@form_line');


Routeur::post("/com_zeapps_crm/quotes/testFormat", 'App\\com_zeapps_crm\\Controllers\\Quotes@testFormat');
Routeur::get("/com_zeapps_crm/quotes/get/{id}", 'App\\com_zeapps_crm\\Controllers\\Quotes@get');
Routeur::post("/com_zeapps_crm/quotes/getAll/{id}/{type}/{limit}/{offset}/{context}", 'App\\com_zeapps_crm\\Controllers\\Quotes@getAll');
Routeur::post("/com_zeapps_crm/quotes/modal/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\Quotes@modal');
Routeur::post("/com_zeapps_crm/quotes/save", 'App\\com_zeapps_crm\\Controllers\\Quotes@save');
Routeur::post("/com_zeapps_crm/quotes/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Quotes@delete');
Routeur::post("/com_zeapps_crm/quotes/transform/{id}", 'App\\com_zeapps_crm\\Controllers\\Quotes@transform');

Routeur::post("/com_zeapps_crm/quotes/saveLine", 'App\\com_zeapps_crm\\Controllers\\Quotes@saveLine');
Routeur::post("/com_zeapps_crm/quotes/updateLinePosition/", 'App\\com_zeapps_crm\\Controllers\\Quotes@updateLinePosition');
Routeur::post("/com_zeapps_crm/quotes/deleteLine/{id}", 'App\\com_zeapps_crm\\Controllers\\Quotes@deleteLine');
Routeur::post("/com_zeapps_crm/quotes/saveLineDetail", 'App\\com_zeapps_crm\\Controllers\\Quotes@saveLineDetail');

Routeur::post("/com_zeapps_crm/quotes/activity", 'App\\com_zeapps_crm\\Controllers\\Quotes@activity');
Routeur::post("/com_zeapps_crm/quotes/del_activity/{id}", 'App\\com_zeapps_crm\\Controllers\\Quotes@del_activity');


Routeur::post("/com_zeapps_crm/quotes/makePDF/{id}", 'App\\com_zeapps_crm\\Controllers\\Quotes@makePDF');

// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
Routeur::get("/com_zeapps_crm/quotes/uploadDocuments/", 'App\\com_zeapps_crm\\Controllers\\Quotes@uploadDocuments');
Routeur::post("/com_zeapps_crm/quotes/del_document/{id}", 'App\\com_zeapps_crm\\Controllers\\Quotes@del_document');

Routeur::get("/com_zeapps_crm/quotes/getPDF/", 'App\\com_zeapps_crm\\Controllers\\Quotes@getPDF');

