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
                <span class="number_document">
                    @if (!$invoice->numerotation || $invoice->numerotation == "")
                        <div style="background-color: #dd0000; color:#ffffff; padding: 0.3mm">
                            Projet de facture :<br>Facture non clôturée
                        </div>
                    @else
                        <div>Facture n° {{ $invoice->numerotation }}</div>
                    @endif
                    </span>
                Date : {{ date("d/m/Y", strtotime($invoice->date_creation)) }}
            </td>
        </tr>
        <tr>
            <td id="delivery_address">
                <b>Adresse de livraison</b><br>
                <?php
                echo $invoice->name_company . '<br>';
                echo $invoice->last_name . '<br>';
                echo $invoice->delivery_address_1;
                echo $invoice->delivery_address_2 ? '<br>' : '';
                echo $invoice->delivery_address_2;
                echo $invoice->delivery_address_3 ? '<br>' : '';
                echo $invoice->delivery_address_3;
                echo '<br>';
                echo $invoice->delivery_zipcode . ' ' . $invoice->delivery_city;
                ?>
            </td>
            <td id="billing_address">
                <b>Adresse de facturation</b><br>
                <?php
                echo $invoice->name_company . '<br>';
                echo $invoice->last_name . '<br>';
                echo $invoice->billing_address_1;
                echo $invoice->billing_address_2 ? '<br>' : '';
                echo $invoice->billing_address_2;
                echo $invoice->billing_address_3 ? '<br>' : '';
                echo $invoice->billing_address_3;
                echo '<br>';
                echo $invoice->billing_zipcode . ' ' . $invoice->billing_city;
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="object">
                <strong>Objet : <?php echo $invoice->libelle; ?></strong>
            </td>
        </tr>
        <tr>
            <?php if($invoice->modalities !== ""){ ?>
            <td class="border">
                <strong>Mode de reglement</strong><br>
                <?php echo $invoice->modalities; ?>
            </td>
            <?php } ?>
            <?php if($invoice->date_limit !== "0000-00-00 00:00:00"){ ?>
            <td class="border">
                <strong>Date de limite de paiement</strong><br>
                <?php  echo date('d/m/Y', strtotime($invoice->date_limit)); ?>
            </td>
            <?php } ?>
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
                    <th>P.U. HT</th>
                    <th>Taxe</th>
                    <?php if($showDiscount){ ?>
                        <th>Remise</th>
                    <?php } ?>
                    <th>T. HT</th>
                    <th>T. TTC</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $subtotal_ht = 0;
                $subtotal_ttc = 0;
                if($lines) {
                    foreach ($lines as $line) {
                        if ($line->type == 'subTotal') {
                            ?>
                            <tr>
                                <td colspan="<?php echo $showDiscount ? 6 : 5; ?>" class="text-right">
                                    <b>Sous-Total</b>
                                </td>
                                <td class="text-right"><b><?php echo number_format(floatval($subtotal_ht), 2, ',', ' '); ?></b></td>
                                <td class="text-right"><b><?php echo number_format(floatval($subtotal_ttc), 2, ',', ' '); ?></b></td>
                            </tr>
                            <?php
                            $subtotal_ht = 0;
                            $subtotal_ttc = 0;
                        } elseif ($line->type == 'comment') {
                            ?>
                            <tr>
                                <td class="text-left" colspan="<?php echo $showDiscount ? 8 : 7; ?>">
                                    <?php echo $line->designation_desc; ?>
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
                                    <?php echo $line->designation_desc; ?>
                                </td>
                                <td class="text-center"><?php echo floatval($line->qty) === round(floatval($line->qty)) ? intval($line->qty) : number_format(floatval($line->qty), 3, ',', ' '); ?></td>
                                <td class="text-right"><?php echo number_format(floatval($line->price_unit), 2, ',', ' '); ?></td>
                                <td class="text-right"><?php echo number_format(floatval($line->value_taxe), 2, ',', ' ') . '%'; ?></td>
                                <?php if($showDiscount){ ?>
                                    <td class="text-right"><?php echo number_format(floatval($line->discount), 2, ',', ' ') . '%'; ?></td>
                                <?php } ?>
                                <td class="text-right"><?php echo number_format(floatval($line->total_ht), 2, ',', ' '); ?></td>
                                <td class="text-right"><?php echo number_format(floatval($line->total_ttc), 2, ',', ' '); ?></td>
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
    <tr>
        <td class="text-left">
            <table class="taxes">
                <thead>
                <tr>
                    <th>Base TVA</th>
                    <th>Taux TVA</th>
                    <th>MT TVA</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($tvas as $tva) {
                    ?>
                    <tr>
                        <td><?php echo number_format(floatval($tva['ht']), 2, ',', ' '); ?></td>
                        <td class="text-right"><?php echo number_format(floatval($tva['value_taxe']), 2, ',', ' '); ?>%</td>
                        <td class="text-right"><?php echo number_format(floatval($tva['value']), 2, ',', ' '); ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </td>
        <td class="text-right">
                <?php if(floatval($invoice->total_discount_ht) > 0){ ?>
                <table class="total">
                    <tr>
                        <td class="text-left">
                            Total HT av remise
                        </td>
                        <td class="text-right">
                            <?php echo number_format(floatval($invoice->total_prediscount_ht), 2, ',', ' '); ?>
                        </td>
                    </tr>
                    <?php if(floatval($invoice->global_discount) > 0){ ?>
                    <tr>
                        <td class="text-left">
                            Remise globable
                        </td>
                        <td class="text-right">
                            <?php echo number_format(floatval($invoice->global_discount), 2, ',', ' ') . '%'; ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="text-left">
                            Total remises HT
                        </td>
                        <td class="text-right">
                            <?php echo number_format(floatval($invoice->total_discount_ht), 2, ',', ' ') ? : '0,00'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                            Total remises TTC
                        </td>
                        <td class="text-right">
                            <?php echo number_format(floatval($invoice->total_discount_ttc), 2, ',', ' ') ? : '0,00'; ?>
                        </td>
                    </tr>
                </table>
                <?php }?>
            <table class="total">
                <tr>
                    <td class="text-left">
                        <strong>Total HT</strong>
                    </td>
                    <td class="text-right">
                        <?php echo number_format(floatval($invoice->total_ht), 2, ',', ' '); ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-left">
                        Total TVA
                    </td>
                    <td class="text-right">
                        <?php echo number_format(floatval($invoice->total_tva), 2, ',', ' '); ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-left">
                        <strong>Total TTC</strong>
                    </td>
                    <td class="text-right">
                        <?php echo number_format(floatval($invoice->total_ttc), 2, ',', ' '); ?>
                    </td>
                </tr>
            </table>
            Prix en euros
        </td>
    </tr>
</table>
</body>
</html>