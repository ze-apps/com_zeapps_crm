<?php

namespace App\com_zeapps_crm\Controllers;

use Zeapps\Core\Controller;
use Zeapps\Core\Request;
use Zeapps\Core\Session;

use App\com_zeapps_crm\Models\PriceList;
use App\com_zeapps_crm\Models\PriceListRate;
use App\com_zeapps_crm\Models\Product\Products;
use App\com_zeapps_crm\Models\Product\ProductPriceList;
use App\com_zeapps_crm\Models\Product\ProductCategories;

class Cron extends Controller
{
    public function updatePriceList()
    {
        // check if we need to update Product price
        $isNeedUpdate = false ;


        $priceList = PriceList::where("is_updated", 1)->where("type_pricelist", 1)->get();
        if ($priceList && count($priceList)) {
            $isNeedUpdate = true ;
        }


        if (!$isNeedUpdate) {
            $priceListRate = PriceListRate::where("is_updated", 1)->get();
            if ($priceListRate && count($priceListRate)) {
                $isNeedUpdate = true;
            }
        }




        if ($isNeedUpdate) {
            // charge les grilles de prix de type %
            $priceLists = PriceList::where("type_pricelist", 1)->get();

            // charge les catégories
            $productCategories = ProductCategories::get();



            // charge les remises par catégorie
            $priceListRates = PriceListRate::get();



            // calcul la valeur pour chaque catégorie
            foreach ($priceLists as $priceList) {
                foreach ($productCategories as &$productCategorie) {
                    $priceListRate = $this->recursiveCheckCatagory($priceList->id, $productCategories, $productCategorie, $priceListRates) ;

                    if (!isset($productCategorie->priceLists)) {
                        $productCategorie->priceLists = array() ;
                    }

                    $valuePriceLists = $productCategorie->priceLists ;



                    if (!isset($valuePriceLists[$priceList->id])) {
                        $valuePriceLists[$priceList->id] = array() ;
                    }

                    if ($priceListRate != null) {
                        $valuePriceLists[$priceList->id]["percentage"] = $priceListRate->percentage ;
                        $valuePriceLists[$priceList->id]["accounting_number"] = $priceListRate->accounting_number ;
                        $valuePriceLists[$priceList->id]["id_taxe"] = $priceListRate->id_taxe ;
                        $valuePriceLists[$priceList->id]["value_taxe"] = $priceListRate->value_taxe ;
                    } else {
                        $valuePriceLists[$priceList->id]["percentage"] = $priceList->percentage ;
                        $valuePriceLists[$priceList->id]["accounting_number"] = "" ;
                        $valuePriceLists[$priceList->id]["id_taxe"] = -1 ;
                        $valuePriceLists[$priceList->id]["value_taxe"] = "" ;
                    }

                    $productCategorie->priceLists = $valuePriceLists ;
                }
            }





            // charge les produits
            $products = Products::get();






            // Ecrit la remise, compte compta, TVA (vérifie les catégories parent et si rien prendre le taux par defaut)
            foreach ($products as $product) {
                foreach ($priceLists as $priceList) {
                    // recherche si le produit a un grille de tarif
                    $objProductPriceList = ProductPriceList::where("id_product", $product->id)->where("id_price_list", $priceList->id)->first();

                    if (!$objProductPriceList) {
                        $objProductPriceList = new ProductPriceList() ;
                        $objProductPriceList->id_product = $product->id ;
                        $objProductPriceList->id_price_list = $priceList->id ;
                    }


                    // recherche la catégorie du produit
                    $rate = null ;
                    if ($product->id_cat) {
                        foreach ($productCategories as $productCategorie) {
                            if ($productCategorie->id == $product->id_cat) {
                                if (isset($productCategorie->priceLists) && isset($productCategorie->priceLists[$priceList->id])) {
                                    $rate = $productCategorie->priceLists[$priceList->id] ;
                                }
                                break;
                            }
                        }
                    }


                    // applique la remise
                    $objProductPriceList->price_ht = $product->price_ht;
                    $objProductPriceList->id_taxe = ($rate && isset($rate["id_taxe"]) && $rate["id_taxe"] != -1) ? $rate["id_taxe"]:$product->id_taxe;
                    $objProductPriceList->value_taxe = ($rate && isset($rate["id_taxe"]) && $rate["id_taxe"] != -1) ? $rate["value_taxe"]:$product->value_taxe;
                    $objProductPriceList->accounting_number = ($rate && isset($rate["accounting_number"]) && $rate["accounting_number"] != "") ? $rate["accounting_number"] : $product->accounting_number;
                    $objProductPriceList->percentage_discount = ($rate && isset($rate["percentage"])) ? $rate["percentage"] : 0;
                    $objProductPriceList->price_ttc = round($objProductPriceList->price_ht * (1 + $objProductPriceList->value_taxe/100), 2);



                    $objProductPriceList->save();
                }
            }





            // TODO : activer les lignes ci-dessous quand le dev et terminé
            // TODO : activer les lignes ci-dessous quand le dev et terminé
            // TODO : activer les lignes ci-dessous quand le dev et terminé
            // TODO : activer les lignes ci-dessous quand le dev et terminé
            // TODO : activer les lignes ci-dessous quand le dev et terminé

            // update to disable update
            //PriceList::where("id", ">", 0)->update(["is_updated"=>0]);
            //PriceListRate::where("id", ">", 0)->update(["is_updated"=>0]);
        }
    }

    private function recursiveCheckCatagory($idPriceList, $categories, $categoryCheck, $priceListRates) {
        $priceListRateReturn = null ;

        foreach ($priceListRates as $priceListRate) {
            if ($priceListRate->id_category == $categoryCheck->id && $priceListRate->id_pricelist == $idPriceList) {
                $priceListRateReturn = $priceListRate;
                break;
            }
        }

        // recherche le parent
        if ($priceListRateReturn == null && $categoryCheck->id_parent) {
            foreach ($categories as $categorie) {
                if ($categorie->id == $categoryCheck->id_parent) {
                    $priceListRateReturn = $this->recursiveCheckCatagory($idPriceList, $categories, $categorie, $priceListRates) ;
                    break;
                }
            }
        }

        return $priceListRateReturn ;
    }


}