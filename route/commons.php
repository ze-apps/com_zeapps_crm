<?php
use Zeapps\Core\Routeur ;


Routeur::get("/com_zeapps_crm/crm_commons/transform_modal", 'App\\com_zeapps_crm\\Controllers\\Commons@transform_modal');
Routeur::get("/com_zeapps_crm/crm_commons/transformed_modal", 'App\\com_zeapps_crm\\Controllers\\Commons@transformed_modal');
Routeur::get("/com_zeapps_crm/crm_commons/form_comment", 'App\\com_zeapps_crm\\Controllers\\Commons@form_comment');
Routeur::get("/com_zeapps_crm/crm_commons/form_document", 'App\\com_zeapps_crm\\Controllers\\Commons@form_document');
Routeur::get("/com_zeapps_crm/crm_commons/form_activity", 'App\\com_zeapps_crm\\Controllers\\Commons@form_activity');

