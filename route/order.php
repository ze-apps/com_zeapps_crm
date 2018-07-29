<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/orders/lists", 'App\\com_zeapps_crm\\Controllers\\Orders@lists');
Routeur::get("/com_zeapps_crm/orders/lists_partial", 'App\\com_zeapps_crm\\Controllers\\Orders@lists_partial');
Routeur::get("/com_zeapps_crm/orders/form_modal", 'App\\com_zeapps_crm\\Controllers\\Orders@form_modal');
Routeur::get("/com_zeapps_crm/orders/view", 'App\\com_zeapps_crm\\Controllers\\Orders@view');
Routeur::get("/com_zeapps_crm/orders/form_line", 'App\\com_zeapps_crm\\Controllers\\Orders@form_line');


Routeur::post("/com_zeapps_crm/orders/testFormat", 'App\\com_zeapps_crm\\Controllers\\Orders@testFormat');
Routeur::get("/com_zeapps_crm/orders/get/{id}", 'App\\com_zeapps_crm\\Controllers\\Orders@get');
Routeur::post("/com_zeapps_crm/orders/getAll/{id}/{type}/{limit}/{offset}/{context}", 'App\\com_zeapps_crm\\Controllers\\Orders@getAll');
Routeur::post("/com_zeapps_crm/orders/modal/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\Orders@modal');
Routeur::post("/com_zeapps_crm/orders/save", 'App\\com_zeapps_crm\\Controllers\\Orders@save');
Routeur::post("/com_zeapps_crm/orders/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\Orders@delete');
Routeur::post("/com_zeapps_crm/orders/transform/{id}", 'App\\com_zeapps_crm\\Controllers\\Orders@transform');

Routeur::post("/com_zeapps_crm/orders/saveLine", 'App\\com_zeapps_crm\\Controllers\\Orders@saveLine');
Routeur::post("/com_zeapps_crm/orders/updateLinePosition/", 'App\\com_zeapps_crm\\Controllers\\Orders@updateLinePosition');
Routeur::post("/com_zeapps_crm/orders/deleteLine/{id}", 'App\\com_zeapps_crm\\Controllers\\Orders@deleteLine');
Routeur::post("/com_zeapps_crm/orders/saveLineDetail", 'App\\com_zeapps_crm\\Controllers\\Orders@saveLineDetail');

Routeur::post("/com_zeapps_crm/orders/activity", 'App\\com_zeapps_crm\\Controllers\\Orders@activity');
Routeur::post("/com_zeapps_crm/orders/del_activity/{id}", 'App\\com_zeapps_crm\\Controllers\\Orders@del_activity');


// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
// TODO : implémenter le methode ci-dessous
Routeur::get("/com_zeapps_crm/orders/uploadDocuments/", 'App\\com_zeapps_crm\\Controllers\\Orders@uploadDocuments');
Routeur::post("/com_zeapps_crm/orders/del_document/{id}", 'App\\com_zeapps_crm\\Controllers\\Orders@del_document');

Routeur::get("/com_zeapps_crm/orders/getPDF/", 'App\\com_zeapps_crm\\Controllers\\Orders@getPDF');
Routeur::post("/com_zeapps_crm/orders/makePDF/{id}", 'App\\com_zeapps_crm\\Controllers\\Orders@makePDF');
