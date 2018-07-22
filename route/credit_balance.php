<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/credit_balances/get/{id}", 'App\\com_zeapps_crm\\Controllers\\CreditBalances@get');
Routeur::post("/com_zeapps_crm/credit_balances/all/{src_id}/{src}/{limit}/{offset}", 'App\\com_zeapps_crm\\Controllers\\CreditBalances@all');
Routeur::post("/com_zeapps_crm/credit_balances/save", 'App\\com_zeapps_crm\\Controllers\\CreditBalances@save');
Routeur::post("/com_zeapps_crm/credit_balances/save_multiples", 'App\\com_zeapps_crm\\Controllers\\CreditBalances@save_multiples');
Routeur::post("/com_zeapps_crm/credit_balances/delete/{id}", 'App\\com_zeapps_crm\\Controllers\\CreditBalances@delete');
