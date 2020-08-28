<?php

/********** insert in left menu ************/
//$tabMenu = array () ;
//$tabMenu["id"] = "com_ze_apps_taxes" ;
//$tabMenu["space"] = "com_ze_apps_config" ;
//$tabMenu["label"] = "Taxes" ;
//$tabMenu["fa-icon"] = "percentage" ;
//$tabMenu["url"] = "/ng/com_zeapps/taxes" ;
//$tabMenu["access"] = "com_zeapps_crm_read" ;
//$tabMenu["order"] = 41 ;
//$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_quotes" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = __t("Quote") ;
$tabMenu["fa-icon"] = "file" ;
$tabMenu["url"] = "/ng/com_zeapps/quote/config" ;
$tabMenu["access"] = "com_zeapps_crm_configuration" ;
$tabMenu["order"] = 45 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_orders" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = __t("Orders") ;
$tabMenu["fa-icon"] = "file" ;
$tabMenu["url"] = "/ng/com_zeapps/order/config" ;
$tabMenu["access"] = "com_zeapps_crm_configuration" ;
$tabMenu["order"] = 46 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_invoices" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = __t("Invoices") ;
$tabMenu["fa-icon"] = "credit-card" ;
$tabMenu["url"] = "/ng/com_zeapps/invoice/config" ;
$tabMenu["access"] = "com_zeapps_crm_configuration" ;
$tabMenu["order"] = 47 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_deliveries" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = __t("Deliveries") ;
$tabMenu["fa-icon"] = "truck" ;
$tabMenu["url"] = "/ng/com_zeapps/delivery/config" ;
$tabMenu["access"] = "com_zeapps_crm_configuration" ;
$tabMenu["order"] = 48 ;
$menuLeft[] = $tabMenu ;



$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_products" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = __t("Products") ;
$tabMenu["fa-icon"] = "tags" ;
$tabMenu["url"] = "/ng/com_zeapps/produits" ;
$tabMenu["access"] = "com_zeapps_crm_configuration" ;
$tabMenu["order"] = 49 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_config_model_email" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = __t("Email template") ;
$tabMenu["fa-icon"] = "tags" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/model_email" ;
$tabMenu["access"] = "com_zeapps_crm_configuration" ;
$tabMenu["order"] = 50 ;
$menuLeft[] = $tabMenu ;






$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_quote" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Quote") ;
$tabMenu["fa-icon"] = "file" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/quote" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 3 ;
$menuLeft[] = $tabMenu ;


$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_order" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Orders") ;
$tabMenu["fa-icon"] = "file" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/order" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 4 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_invoice" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Invoices") ;
$tabMenu["fa-icon"] = "credit-card" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/invoice" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 5 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_delivery" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Deliveries") ;
$tabMenu["fa-icon"] = "truck" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/delivery" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 6 ;
$menuLeft[] = $tabMenu ;



//$tabMenu = array () ;
//$tabMenu["id"] = "com_zeapps_crm_contract" ;
//$tabMenu["space"] = "com_ze_apps_sales" ;
//$tabMenu["label"] = "Contrats" ;
//$tabMenu["fa-icon"] = "handshake-o" ;
//$tabMenu["url"] = "/ng/com_zeapps_crm/contract" ;
//$tabMenu["access"] = "com_zeapps_crm_read" ;
//$tabMenu["order"] = 7 ;
//$menuLeft[] = $tabMenu ;

//$tabMenu = array () ;
//$tabMenu["id"] = "com_zeapps_crm_opportunity" ;
//$tabMenu["space"] = "com_ze_apps_sales" ;
//$tabMenu["label"] = "Opportunités" ;
//$tabMenu["fa-icon"] = "search" ;
//$tabMenu["url"] = "/ng/com_zeapps_crm/opportunity" ;
//$tabMenu["access"] = "com_zeapps_crm_read" ;
//$tabMenu["order"] = 8 ;
//$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_product" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Products") ;
$tabMenu["fa-icon"] = "tags" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/product" ;
$tabMenu["access"] = "com_zeapps_crm_produit_admin" ;
$tabMenu["order"] = 9 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_product_price_list" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Price list") ;
$tabMenu["fa-icon"] = "tags" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/product_price_list" ;
$tabMenu["access"] = "com_zeapps_crm_produit_admin" ;
$tabMenu["order"] = 10 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_stock" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Stocks") ;
$tabMenu["fa-icon"] = "cubes" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/stock" ;
$tabMenu["access"] = "com_zeapps_crm_produit_stock" ;
$tabMenu["order"] = 11 ;
$menuLeft[] = $tabMenu ;

/*$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_potential_orders" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Commandes probables" ;
$tabMenu["fa-icon"] = "question-circle" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/potential_orders" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 50 ;
$menuLeft[] = $tabMenu ;*/


//$tabMenu = array () ;
//$tabMenu["id"] = "com_zeapps_crm_purchase" ;
//$tabMenu["space"] = "com_ze_apps_purchase" ;
//$tabMenu["label"] = "Achats" ;
//$tabMenu["url"] = "/ng/com_zeapps_crm/purchase" ;
//$tabMenu["access"] = "com_zeapps_crm_read" ;
//$tabMenu["order"] = 1 ;
//$menuLeft[] = $tabMenu ;

//$tabMenu = array () ;
//$tabMenu["id"] = "com_zeapps_crm_delivery_receipt" ;
//$tabMenu["space"] = "com_ze_apps_purchase" ;
//$tabMenu["label"] = "Bon de réception" ;
//$tabMenu["url"] = "/ng/com_zeapps_crm/delivery_receipt" ;
//$tabMenu["access"] = "com_zeapps_crm_read" ;
//$tabMenu["order"] = 2 ;
//$menuLeft[] = $tabMenu ;






/********** insert in top menu ************/
$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_quote" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Quote") ;
$tabMenu["url"] = "/ng/com_zeapps_crm/quote" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 3 ;
$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_order" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Orders") ;
$tabMenu["url"] = "/ng/com_zeapps_crm/order" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 4 ;
$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_invoice" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Invoices") ;
$tabMenu["url"] = "/ng/com_zeapps_crm/invoice" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 5 ;
$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_deliveries" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Deliveries") ;
$tabMenu["url"] = "/ng/com_zeapps_crm/delivery" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 6 ;
$menuHeader[] = $tabMenu ;


/*$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_credit_balance" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Règlement à venir" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/credit_balances" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 6 ;
$menuHeader[] = $tabMenu ;*/

//$tabMenu = array () ;
//$tabMenu["id"] = "com_zeapps_crm_contract" ;
//$tabMenu["space"] = "com_ze_apps_sales" ;
//$tabMenu["label"] = "Contrats" ;
//$tabMenu["url"] = "/ng/com_zeapps_crm/contract" ;
//$tabMenu["access"] = "com_zeapps_crm_read" ;
//$tabMenu["order"] = 7 ;
//$menuHeader[] = $tabMenu ;

//$tabMenu = array () ;
//$tabMenu["id"] = "com_zeapps_crm_opportunity" ;
//$tabMenu["space"] = "com_ze_apps_sales" ;
//$tabMenu["label"] = "Opportunités" ;
//$tabMenu["url"] = "/ng/com_zeapps_crm/opportunity" ;
//$tabMenu["access"] = "com_zeapps_crm_read" ;
//$tabMenu["order"] = 8 ;
//$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_product" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Products") ;
$tabMenu["url"] = "/ng/com_zeapps_crm/product" ;
$tabMenu["access"] = "com_zeapps_crm_produit_admin" ;
$tabMenu["order"] = 9 ;
$menuHeader[] = $tabMenu ;

//$tabMenu = array () ;
//$tabMenu["id"] = "com_zeapps_crm_product_price" ;
//$tabMenu["space"] = "com_ze_apps_sales" ;
//$tabMenu["label"] = "Grille tarifaire" ;
//$tabMenu["url"] = "/ng/com_zeapps_crm/product_price" ;
//$tabMenu["access"] = "com_zeapps_crm_produit_admin" ;
//$tabMenu["order"] = 10 ;
//$menuHeader[] = $tabMenu ;


$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_stock" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = __t("Stocks") ;
$tabMenu["url"] = "/ng/com_zeapps_crm/stock" ;
$tabMenu["access"] = "com_zeapps_crm_produit_stock" ;
$tabMenu["order"] = 11 ;
$menuHeader[] = $tabMenu ;



//$tabMenu = array () ;
//$tabMenu["id"] = "com_zeapps_crm_invoice" ;
//$tabMenu["space"] = "com_ze_apps_purchase" ;
//$tabMenu["label"] = "Achats" ;
//$tabMenu["url"] = "/ng/com_zeapps_crm/purchase" ;
//$tabMenu["access"] = "com_zeapps_crm_read" ;
//$tabMenu["order"] = 12 ;
//$menuHeader[] = $tabMenu ;

//$tabMenu = array () ;
//$tabMenu["id"] = "com_zeapps_crm_delivery_receipt" ;
//$tabMenu["space"] = "com_ze_apps_purchase" ;
//$tabMenu["label"] = "Bon de réception" ;
//$tabMenu["url"] = "/ng/com_zeapps_crm/delivery_receipt" ;
//$tabMenu["access"] = "com_zeapps_crm_read" ;
//$tabMenu["order"] = 13 ;
//$menuHeader[] = $tabMenu ;
