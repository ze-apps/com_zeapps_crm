<?php

use Illuminate\Database\Capsule\Manager as Capsule;

use App\com_zeapps_crm\Models\Taxes;
use App\com_zeapps_crm\Models\Activities;
use App\com_zeapps_crm\Models\CrmOrigins;
use App\com_zeapps_crm\Models\Warehouses;

use App\com_zeapps_crm\Models\ProductCategories;
use App\com_zeapps_crm\Models\ProductProducts;
use App\com_zeapps_crm\Models\ProductLines;
use App\com_zeapps_crm\Models\ProductStocks;

use App\com_zeapps_crm\Models\Quotes;
use App\com_zeapps_crm\Models\QuoteLines;
use App\com_zeapps_crm\Models\QuoteActivities;

use App\com_zeapps_crm\Models\Orders;
use App\com_zeapps_crm\Models\OrderLines;
use App\com_zeapps_crm\Models\OrderActivities;

use App\com_zeapps_crm\Models\Invoices;
use App\com_zeapps_crm\Models\InvoiceLines;
use App\com_zeapps_crm\Models\InvoiceActivities;

use App\com_zeapps_crm\Models\Deliveries;
use App\com_zeapps_crm\Models\DeliveryLines;
use App\com_zeapps_crm\Models\DeliveryActivities;


class ComZeappsCrmInitSeeds
{
    public function run()
    {
        // import de compagnies
        Capsule::table('com_zeapps_crm_taxes')->truncate();
        $compagnies = json_decode(file_get_contents(dirname(__FILE__) . "/taxes.json"));
        foreach ($compagnies as $compagny_json) {
            $taxe = new Taxes();

            foreach ($compagny_json as $key => $value) {
                $taxe->$key = $value;
            }

            $taxe->save();
        }


        // import des activités
        Capsule::table('com_zeapps_crm_activities')->truncate();
        $activities = json_decode(file_get_contents(dirname(__FILE__) . "/activities.json"));
        foreach ($activities as $activity_json) {
            $activity = new Activities();

            foreach ($activity_json as $key => $value) {
                $activity->$key = $value;
            }

            $activity->save();
        }


        // import de origins
        Capsule::table('com_zeapps_crm_crm_origins')->truncate();
        $origins = json_decode(file_get_contents(dirname(__FILE__) . "/crm_origins.json"));
        foreach ($origins as $origin_json) {
            $crmOrigin = new CrmOrigins();

            foreach ($origin_json as $key => $value) {
                $crmOrigin->$key = $value;
            }

            $crmOrigin->save();
        }


        // import de warehouses
        Capsule::table('com_zeapps_crm_warehouses')->truncate();
        $warehouses = json_decode(file_get_contents(dirname(__FILE__) . "/warehouses.json"));
        foreach ($warehouses as $warehouse_json) {
            $warehouse = new Warehouses();

            foreach ($warehouse_json as $key => $value) {
                $warehouse->$key = $value;
            }

            $warehouse->save();
        }


        // import de ProductCategories
        Capsule::table('com_zeapps_crm_product_categories')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/ProductCategories.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new ProductCategories();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }


        // import de ProductProducts
        Capsule::table('com_zeapps_crm_product_products')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/ProductProducts.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new ProductProducts();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }


        // import de ProductLines
        Capsule::table('com_zeapps_crm_product_lines')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/ProductLines.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new ProductLines();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }


        // import de ProductStocks
        Capsule::table('com_zeapps_crm_product_stocks')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/ProductStocks.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new ProductStocks();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }


        // import de Quotes
        Capsule::table('com_zeapps_crm_quotes')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/Quotes.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new Quotes();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }
            $obj_data->numerotation = "" ;

            $obj_data->save();
        }


        // import de QuoteLines
        Capsule::table('com_zeapps_crm_quote_lines')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/QuoteLines.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new QuoteLines();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }


        // import de QuoteActivities
        Capsule::table('com_zeapps_crm_quote_activities')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/QuoteActivities.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new QuoteActivities();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }



        // import de Orders
        Capsule::table('com_zeapps_crm_orders')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/Orders.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new Orders();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }
            $obj_data->numerotation = "" ;

            $obj_data->save();
        }



        // import de OrderLines
        Capsule::table('com_zeapps_crm_order_lines')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/OrderLines.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new OrderLines();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }






        // import de OrderActivities
        Capsule::table('com_zeapps_crm_order_activities')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/OrderActivities.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new OrderActivities();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }





        // import de Invoices
        Capsule::table('com_zeapps_crm_invoices')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/Invoices.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new Invoices();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }
            $obj_data->numerotation = "" ;

            $obj_data->save();
        }



        // import de InvoiceLines
        Capsule::table('com_zeapps_crm_invoice_lines')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/InvoiceLines.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new InvoiceLines();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }







        // import de InvoiceActivities
        Capsule::table('com_zeapps_crm_invoice_activities')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/InvoiceActivities.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new InvoiceActivities();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }






        // import de Deliveries
        Capsule::table('com_zeapps_crm_deliveries')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/Deliveries.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new Deliveries();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }
            $obj_data->numerotation = "" ;

            $obj_data->save();
        }




        // import de DeliveryLines
        Capsule::table('com_zeapps_crm_delivery_lines')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/DeliveryLines.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new DeliveryLines();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }







        // import de DeliveryActivities
        Capsule::table('com_zeapps_crm_delivery_activities')->truncate();
        $json_content = json_decode(file_get_contents(dirname(__FILE__) . "/DeliveryActivities.json"));
        foreach ($json_content as $data_json) {
            $obj_data = new DeliveryActivities();

            foreach ($data_json as $key => $value) {
                $obj_data->$key = $value;
            }

            $obj_data->save();
        }


    }
}
