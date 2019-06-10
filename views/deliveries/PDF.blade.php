<html>
<header>
    <style>

        @page {
            size: 210mm 297mm;
            margin-top: 8cm;
            margin-bottom: 2.5cm;
            header: html_MyHeader1;
            footer: html_MyFooter1;
        }


        body {
            font-family: Verdana;
            font-size: 12px;
        }

        table {
            width: 100%;
            margin: 10px 0;
            background-color: #ffffff;
            border-collapse: collapse;
        }
        td{
            vertical-align: top;
        }
        .taxes{
            float: left;
            width: 80%;
        }
        .total{
            float: right;
            width: 80%;
        }
        .lines th,
        .lines td,
        .taxes th,
        .taxes td,
        .total th,
        .total td,
        .border{
            border: solid 1px #000000;
            padding: 5px 8px;
            vertical-align: middle;
        }
        #logo{
            padding: 10px 0;
        }
        #billing_address,
        #delivery_address{
            padding: 0 0 10px 0;
        }
        .text-left {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .text-right{
            text-align: right;
        }
        .object{
            padding: 10px 0;
        }

        .number_document {
            font-size: 1.5em;
            font-weight: bold;
        }
        .mention_pied_page {
            font-size: 7pt;
        }
    </style>
</header>
<body>




<htmlpageheader name="MyHeader1">
    <table>
        <tr>
            <td id="logo" width="50%">
                <img src="/user/logo.jpg" style="max-width: 190px; height: auto">
            </td>
            <td class="text-right" width="50%">
                <span class="number_document">Bon de livraison n° {{ $delivery->numerotation }}</span><br>
                Date : {{ date("d/m/Y", strtotime($delivery->date_creation)) }}
            </td>
        </tr>
        <tr>
            <td id="delivery_address">
                <b>Adresse de facturation</b><br>
                <?php
                if ($delivery->name_company != "") {
                    echo $delivery->name_company . '<br>';
                }
                if ($delivery->name_contact != "") {
                    echo $delivery->name_contact . '<br>';
                }
                echo $delivery->billing_address_1;
                echo $delivery->billing_address_2 ? '<br>' . $delivery->billing_address_2 : '';
                echo $delivery->billing_address_3 ? '<br>' . $delivery->billing_address_3 : '';
                echo '<br>';
                echo $delivery->billing_zipcode . ' ' . $delivery->billing_city;
                if ($delivery->billing_state != "") {
                    echo '<br>' . $delivery->billing_state;
                }
                if ($delivery->billing_country_name != "") {
                    echo '<br>' . $delivery->billing_country_name;
                }
                ?>
            </td>

            <td id="billing_address">
                <b>Adresse de livraison</b><br>
                <?php
                if ($delivery->delivery_name_company != "") {
                    echo $delivery->delivery_name_company . '<br>';
                }
                if ($delivery->delivery_name_contact != "") {
                    echo $delivery->delivery_name_contact . '<br>';
                }
                echo $delivery->delivery_address_1;
                echo $delivery->delivery_address_2 ? '<br>' . $delivery->delivery_address_2 : '';
                echo $delivery->delivery_address_3 ? '<br>' . $delivery->delivery_address_3 : '';
                echo '<br>';
                echo $delivery->delivery_zipcode . ' ' . $delivery->delivery_city;
                if ($delivery->delivery_state != "") {
                    echo '<br>' . $delivery->delivery_state;
                }
                if ($delivery->delivery_country_name != "") {
                    echo '<br>' . $delivery->delivery_country_name;
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="object">
                <strong>Objet : <?php echo $delivery->libelle; ?></strong>
            </td>
        </tr>
    </table>
</htmlpageheader>

<htmlpagefooter name="MyFooter1">

    <div class="mention_pied_page">
        En cas de retard de paiement, il sera appliqué des pénalités à un taux égal à 12% sans que celui-ci ne puisse être inférieur à une fois et demi le taux d'intérêt légal en vigueur en France. Pas d'escompte en cas de paiement anticipé. Une indemnité forfaitaire de 40 € pour frais de recouvrement sera appliquée en cas de retard de paiement conformément aux articles L441-3 et L441-6 du Code de commerce.
    </div>

    <div class="text-right" style="border-top: 1px solid #000000;">{PAGENO}/{nbpg}</div>
</htmlpagefooter>



<table>
    <tr>
        <td colspan="2">
            <table class="lines">
                <thead>
                <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">Désignation</th>
                    <th>Qte</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $subtotal_ht = 0;
                $subtotal_ttc = 0;
                if($lines) {
                    foreach ($lines as $line) {
                        if ($line->type == 'subTotal') {
                            $subtotal_ht = 0;
                            $subtotal_ttc = 0;
                        } elseif ($line->type == 'comment') {
                            ?>
                            <tr>
                                <td class="text-left" colspan="3">
                                    <?php echo nl2br($line->designation_desc); ?>
                                </td>
                            </tr>
                            <?php
                        } else {
                            $subtotal_ht += floatval($line->total_ht);
                            $subtotal_ttc += floatval($line->total_ttc);
                            ?>
                            <tr>
                                <td class="text-left"><?php echo $line->ref; ?></td>
                                <td class="text-left">
                                    <strong><?php echo $line->designation_title; ?></strong><br/>
                                    <?php echo nl2br($line->designation_desc); ?>
                                </td>
                                <td class="text-center"><?php echo floatval($line->qty) === round(floatval($line->qty)) ? intval($line->qty) : number_format(floatval($line->qty), 3, ',', ' '); ?></td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
                </tbody>
            </table>
        </td>
    </tr>
</table>
</body>
</html>