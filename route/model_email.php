<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/model_email/liste", 'App\\com_zeapps_crm\\Controllers\\ModelEmail@liste');
Routeur::get("/com_zeapps_crm/model_email/form", 'App\\com_zeapps_crm\\Controllers\\ModelEmail@form');


Routeur::get("/com_zeapps_crm/model_email/get/{id}", 'App\\com_zeapps_crm\\Controllers\\ModelEmail@get');
Routeur::get("/com_zeapps_crm/model_email/duplicate/{id}", 'App\\com_zeapps_crm\\Controllers\\ModelEmail@duplicate');
Routeur::get("/com_zeapps_crm/model_email/getAll", 'App\\com_zeapps_crm\\Controllers\\ModelEmail@get_all');
Routeur::post("/com_zeapps_crm/model_email/save", 'App\\com_zeapps_crm\\Controllers\\ModelEmail@save');
Routeur::post("/com_zeapps_crm/model_email/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\ModelEmail@delete');
Routeur::post("/com_zeapps_crm/model_email/upload-file", 'App\\com_zeapps_crm\\Controllers\\ModelEmail@uploadFile');


