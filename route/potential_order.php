<?php
use Zeapps\Core\Routeur ;





Routeur::post("/com_zeapps_crm/potential_orders/all/{limit}/{offset}/{context}", 'App\\com_zeapps_crm\\Controllers\\PotentialOrders@all');


