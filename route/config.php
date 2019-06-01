<?php
use Zeapps\Core\Routeur ;





Routeur::get("/com_zeapps_crm/config/get/crm_invoice_numerotation", 'App\\com_zeapps_crm\\Controllers\\Config@crm_invoice_numerotation');
Routeur::get("/com_zeapps_crm/config/get/crm_invoice_format", 'App\\com_zeapps_crm\\Controllers\\Config@crm_invoice_format');
Routeur::get("/com_zeapps_crm/config/get/crm_quote_numerotation", 'App\\com_zeapps_crm\\Controllers\\Config@crm_quote_numerotation');
Routeur::get("/com_zeapps_crm/config/get/crm_quote_format", 'App\\com_zeapps_crm\\Controllers\\Config@crm_quote_format');
Routeur::get("/com_zeapps_crm/config/get/crm_order_numerotation", 'App\\com_zeapps_crm\\Controllers\\Config@crm_order_numerotation');
Routeur::get("/com_zeapps_crm/config/get/crm_order_format", 'App\\com_zeapps_crm\\Controllers\\Config@crm_order_format');
Routeur::get("/com_zeapps_crm/config/get/crm_delivery_numerotation", 'App\\com_zeapps_crm\\Controllers\\Config@crm_delivery_numerotation');
Routeur::get("/com_zeapps_crm/config/get/crm_delivery_format", 'App\\com_zeapps_crm\\Controllers\\Config@crm_delivery_format');
Routeur::get("/com_zeapps_crm/config/get/crm_product_attributes", 'App\\com_zeapps_crm\\Controllers\\Config@crm_product_attributes');


Routeur::get("/com_zeapps_crm/quotes/config", 'App\\com_zeapps_crm\\Controllers\\Config@quotes');
Routeur::get("/com_zeapps_crm/orders/config", 'App\\com_zeapps_crm\\Controllers\\Config@orders');
Routeur::get("/com_zeapps_crm/invoices/config", 'App\\com_zeapps_crm\\Controllers\\Config@invoice');
Routeur::get("/com_zeapps_crm/deliveries/config", 'App\\com_zeapps_crm\\Controllers\\Config@delivery');

