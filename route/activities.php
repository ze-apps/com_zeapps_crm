<?php
use Zeapps\Core\Routeur ;

Routeur::get("/com_zeapps_crm/activities/lists", 'App\\com_zeapps_crm\\Controllers\\Activities@lists');
Routeur::post("/com_zeapps_crm/activities/getAll/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\Activities@getAll');