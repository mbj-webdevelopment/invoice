<?php

/**
 * @since      1.0.0
 * @package    Invoice
 * @subpackage Invoice/includes
 * @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class PayPal_Invoice_Manager {

    function App_CreateInvoice($post) {
        global $post;
        $get_array_invoice_client_detail_in_postmeta = "";
        $get_json_invoice_item_detail_in_postmeta_table = get_post_meta($post->ID, 'invoice_item_details');
        if (isset($get_json_invoice_item_detail_in_postmeta_table) && !empty($get_json_invoice_item_detail_in_postmeta_table)) {
            if (isset($get_json_invoice_item_detail_in_postmeta_table[0]['client_post_id'])) {
                $get_array_invoice_client_detail_in_postmeta = get_post_meta($get_json_invoice_item_detail_in_postmeta_table[0]['client_post_id'], 'invoice_client_details');
                if (is_array($get_array_invoice_client_detail_in_postmeta)) {
                    if ('on' == $get_array_invoice_client_detail_in_postmeta[0]['invoice_ship_to_different_address']) {
                        $invoice_shipping_Info = $this->Get_ShippingInfo($get_array_invoice_client_detail_in_postmeta);
                        $invoice_shippingInfo_Address = $this->Get_ShippingInfoAddress($get_array_invoice_client_detail_in_postmeta);
                    }
                    $create_invoice_paypalconfig = $this->get_paypal_config_value();
                    $invoice_paypal_adaptive_class_obj = new PayPal_Adaptive($create_invoice_paypalconfig);
                    $create_invoice_fields = $this->Get_CreateInvoiceFields($post, $get_json_invoice_item_detail_in_postmeta_table, $get_array_invoice_client_detail_in_postmeta);
                    $invoice_billing_Info = $this->Get_BillingInfo($get_array_invoice_client_detail_in_postmeta);
                    $invoice_billingInfo_Address = $this->Get_BillingInfoAddress($get_array_invoice_client_detail_in_postmeta);
                    $invoice_items_array = $this->Get_Invoice_Item_Array($get_json_invoice_item_detail_in_postmeta_table);
                    $invoice_paypal_request_data = array(
                        'CreateInvoiceFields' => $create_invoice_fields,
                        'BillingInfo' => $invoice_billing_Info,
                        'BillingInfoAddress' => $invoice_billingInfo_Address,
                        'ShippingInfo' => $invoice_shipping_Info,
                        'ShippingInfoAddress' => $invoice_shippingInfo_Address,
                        'InvoiceItems' => $invoice_items_array
                    );
                    return $invoice_paypal_adaptive_class_obj->CreateInvoice($invoice_paypal_request_data);
                }
            }
        }
    }

    function App_CreateAndSendInvoice($post) {
        global $post;
        $get_array_invoice_client_detail_in_postmeta = "";
        $get_json_invoice_item_detail_in_postmeta_table = get_post_meta($post->ID, 'invoice_item_details');
        if (isset($get_json_invoice_item_detail_in_postmeta_table) && !empty($get_json_invoice_item_detail_in_postmeta_table)) {
            if (isset($get_json_invoice_item_detail_in_postmeta_table[0]['client_post_id'])) {
                $get_array_invoice_client_detail_in_postmeta = get_post_meta($get_json_invoice_item_detail_in_postmeta_table[0]['client_post_id'], 'invoice_client_details');
                if (is_array($get_array_invoice_client_detail_in_postmeta)) {
                    if ('on' == $get_array_invoice_client_detail_in_postmeta[0]['invoice_ship_to_different_address']) {
                        $invoice_shipping_Info = $this->Get_ShippingInfo($get_array_invoice_client_detail_in_postmeta);
                        $invoice_shippingInfo_Address = $this->Get_ShippingInfoAddress($get_array_invoice_client_detail_in_postmeta);
                    }
                    $create_invoice_paypalconfig = $this->get_paypal_config_value();
                    $invoice_paypal_adaptive_class_obj = new PayPal_Adaptive($create_invoice_paypalconfig);
                    $create_invoice_fields = $this->Get_CreateInvoiceFields($post, $get_json_invoice_item_detail_in_postmeta_table, $get_array_invoice_client_detail_in_postmeta);
                    $invoice_billing_Info = $this->Get_BillingInfo($get_array_invoice_client_detail_in_postmeta);
                    $invoice_billingInfo_Address = $this->Get_BillingInfoAddress($get_array_invoice_client_detail_in_postmeta);
                    $invoice_items_array = $this->Get_Invoice_Item_Array($get_json_invoice_item_detail_in_postmeta_table);
                    $invoice_paypal_request_data = array(
                        'CreateInvoiceFields' => $create_invoice_fields,
                        'BillingInfo' => $invoice_billing_Info,
                        'BillingInfoAddress' => $invoice_billingInfo_Address,
                        'ShippingInfo' => $invoice_shipping_Info,
                        'ShippingInfoAddress' => $invoice_shippingInfo_Address,
                        'InvoiceItems' => $invoice_items_array
                    );
                    return $invoice_paypal_adaptive_class_obj->CreateAndSendInvoice($invoice_paypal_request_data);
                }
            }
        }
    }

    function App_CancelInvoice($post) {
        $invoice_id = get_post_meta($post->ID, 'InvoiceID');
        $invoice_massege_return = 'Invoice ID is Empty';
        if (isset($invoice_id) && !empty($invoice_id)) {
            $create_invoice_paypalconfig = $this->get_paypal_config_value();
            $invoice_paypal_adaptive_class_obj = new PayPal_Adaptive($create_invoice_paypalconfig);
            $CancelInvoiceFields = array(
                'InvoiceID' => $invoice_id[0],
                'Subject' => 'Invoice has been canceled.',
                'NoteForPayer' => 'Note for Payer.',
                'SendCopyToMerchant' => 'true'
            );
            $PayPalRequestData = array('CancelInvoiceFields' => $CancelInvoiceFields);
            $invoice_massege_return = $invoice_paypal_adaptive_class_obj->CancelInvoice($PayPalRequestData);
        }
        return $invoice_massege_return;
    }

    function App_DeleteInvoice($post) {
        $invoice_id = get_post_meta($post->ID, 'InvoiceID');
        $invoice_massege_return = 'Invoice ID is Empty';
        if (isset($invoice_id) && !empty($invoice_id)) {
            $create_invoice_paypalconfig = $this->get_paypal_config_value();
            $invoice_paypal_adaptive_class_obj = new PayPal_Adaptive($create_invoice_paypalconfig);
            $invoice_massege_return = $invoice_paypal_adaptive_class_obj->DeleteInvoice($invoice_id[0]);
        }
        return $invoice_massege_return;
    }

    function App_SendInvoice($post) {
        $invoice_id = get_post_meta($post->ID, 'InvoiceID');
        $invoice_massege_return = 'Invoice ID is Empty';
        if (isset($invoice_id) && !empty($invoice_id)) {
            $create_invoice_paypalconfig = $this->get_paypal_config_value();
            $invoice_paypal_adaptive_class_obj = new PayPal_Adaptive($create_invoice_paypalconfig);
            $InvoiceID = $invoice_id[0];
            $invoice_massege_return = $invoice_paypal_adaptive_class_obj->SendInvoice($InvoiceID);
        }
        return $invoice_massege_return;
    }

    function App_UpdateInvoice($post) {
        global $post;
        $get_array_invoice_client_detail_in_postmeta = "";
        $get_json_invoice_item_detail_in_postmeta_table = get_post_meta($post->ID, 'invoice_item_details');
        if (isset($get_json_invoice_item_detail_in_postmeta_table) && !empty($get_json_invoice_item_detail_in_postmeta_table)) {
            if (isset($get_json_invoice_item_detail_in_postmeta_table[0]['client_post_id'])) {
                $get_array_invoice_client_detail_in_postmeta = get_post_meta($get_json_invoice_item_detail_in_postmeta_table[0]['client_post_id'], 'invoice_client_details');
                if (is_array($get_array_invoice_client_detail_in_postmeta)) {
                    if ('on' == $get_array_invoice_client_detail_in_postmeta[0]['invoice_ship_to_different_address']) {
                        $invoice_shipping_Info = $this->Get_ShippingInfo($get_array_invoice_client_detail_in_postmeta);
                        $invoice_shippingInfo_Address = $this->Get_ShippingInfoAddress($get_array_invoice_client_detail_in_postmeta);
                    }
                    $create_invoice_paypalconfig = $this->get_paypal_config_value();
                    $invoice_paypal_adaptive_class_obj = new PayPal_Adaptive($create_invoice_paypalconfig);
                    $UpdateInvoiceFields = $this->Get_CreateInvoiceFields($post, $get_json_invoice_item_detail_in_postmeta_table, $get_array_invoice_client_detail_in_postmeta);
                    $invoice_billing_Info = $this->Get_BillingInfo($get_array_invoice_client_detail_in_postmeta);
                    $invoice_billingInfo_Address = $this->Get_BillingInfoAddress($get_array_invoice_client_detail_in_postmeta);
                    $invoice_items_array = $this->Get_Invoice_Item_Array($get_json_invoice_item_detail_in_postmeta_table);
                    $invoice_paypal_request_data = array(
                        'UpdateInvoiceFields' => $UpdateInvoiceFields,
                        'BillingInfo' => $invoice_billing_Info,
                        'BillingInfoAddress' => $invoice_billingInfo_Address,
                        'ShippingInfo' => $invoice_shipping_Info,
                        'ShippingInfoAddress' => $invoice_shippingInfo_Address,
                        'InvoiceItems' => $invoice_items_array
                    );
                    return $invoice_paypal_adaptive_class_obj->UpdateInvoice($invoice_paypal_request_data);
                }
            }
        }
    }

    function App_GetInvoiceDetails() {
        $invoice_id = get_post_meta($post->ID, 'InvoiceID');
        $invoice_invoice_detail = '';
        if (isset($invoice_id) && !empty($invoice_id)) {
            $create_invoice_paypalconfig = $this->get_paypal_config_value();
            $invoice_paypal_adaptive_class_obj = new PayPal_Adaptive($create_invoice_paypalconfig);

            $invoice_invoice_detail = $invoice_paypal_adaptive_class_obj->GetInvoiceDetails($InvoiceID);
        }
        return $invoice_invoice_detail;
    }

    function App_MarkInvoiceAsPaid() {
        $invoice_id = get_post_meta($post->ID, 'InvoiceID');
        $invoice_massege_return = 'Invoice ID is Empty';
        if (isset($invoice_id) && !empty($invoice_id)) {
            $create_invoice_paypalconfig = $this->get_paypal_config_value();
            $invoice_paypal_adaptive_class_obj = new PayPal_Adaptive($create_invoice_paypalconfig);
            $MarkInvoiceAsPaidFields = array(
                'InvoiceID' => 'INV2-F3MK-CBYE-XLGL-6EF8',
                'Method' => 'Cash',
                'Note' => 'User paid cash.',
                'Date' => '2012-03-31'
            );
            $PayPalRequestData = array('MarkInvoiceAsPaidFields' => $MarkInvoiceAsPaidFields);
            $invoice_massege_return = $invoice_paypal_adaptive_class_obj->MarkInvoiceAsPaid($PayPalRequestData);
        }
        return $invoice_massege_return;
    }

    function App_SearchInvoices() {
        $PayPal = new PayPal_Adaptive($PayPalConfig);
        $SearchInvoicesFields = array(
            'MerchantEmail' => 'sandbo_1215254764_biz@angelleye.com', // Required.  Email address of invoice creator.
            'Page' => '1',
            'PageSize' => '1'
        );
        $Parameters = array(
            'Email' => '',
            'RecipientName' => '',
            'BusinessName' => '',
            'InvoiceNumber' => '',
            'Status' => '',
            'LowerAmount' => '',
            'UpperAmount' => '',
            'CurrencyCode' => '',
            'Memo' => '',
            'Origin' => ''
        );
        $PayPalRequestData = array(
            'SearchInvoicesFields' => $SearchInvoicesFields,
            'Parameters' => $Parameters
        );
        $PayPalResult = $PayPal->SearchInvoices($PayPalRequestData);
    }

    function get_paypal_config_value() {
        return $PayPalConfig = array(
            'Sandbox' => ( 'yes' == get_option('invoice_sandbox_enable') ) ? true : false,
            'DeveloperAccountEmail' => get_option('invoice_developer_account_email_address'),
            'ApplicationID' => get_option('invoice_application_id'),
            'DeviceID' => '',
            'IPAddress' => $_SERVER['REMOTE_ADDR'],
            'APIUsername' => get_option('invoice_api_username'),
            'APIPassword' => get_option('invoice_api_password'),
            'APISignature' => get_option('invoice_api_signature'),
            'APISubject' => '',
            'PrintHeaders' => '',
            'LogResults' => '',
            'LogPath' => '',
        );
    }

    function Get_CreateInvoiceFields($post, $get_json_invoice_item_detail_in_postmeta_table, $get_array_invoice_client_detail_in_postmeta) {
        $get_invoice_id = get_post_meta($post->ID, 'InvoiceID');
        return $CreateInvoiceFields = array(
            'InvoiceID' => $get_invoice_id[0],
            'MerchantEmail' => get_option('invoice_merchant_email'),
            'PayerEmail' => $get_array_invoice_client_detail_in_postmeta[0]['payer_email'],
            'Number' => get_option('invoice_custom_prefix') . substr(microtime(), -5),
            'CurrencyCode' => get_option('invoice_currncy_code'),
            'InvoiceDate' => '',
            'DueDate' => '',
            'PaymentTerms' => '',
            'DiscountPercent' => '',
            'DiscountAmount' => str_replace("$", "", $get_json_invoice_item_detail_in_postmeta_table[0]['invoice_descount']),
            'Terms' => '',
            'Note' => '',
            'MerchantMemo' => '',
            'ShippingAmount' => '',
            'ShippingTaxName' => '',
            'ShippingTaxRate' => '',
            'LogoURL' => 'https://www.sandbox.paypal.com/en_US/i/logo/paypal_logo.gif'
        );
    }

    function Get_BillingInfo($get_array_invoice_client_detail_in_postmeta) {
        return $BillingInfo = array(
            'FirstName' => $get_array_invoice_client_detail_in_postmeta[0]['billing_first_name'],
            'LastName' => $get_array_invoice_client_detail_in_postmeta[0]['billing_last_name'],
            'BusinessName' => $get_array_invoice_client_detail_in_postmeta[0]['billing_businessname'],
            'Phone' => $get_array_invoice_client_detail_in_postmeta[0]['billing_phone'],
            'Fax' => $get_array_invoice_client_detail_in_postmeta[0]['billing_fax'],
            'Website' => $get_array_invoice_client_detail_in_postmeta[0]['billing_website'],
            'Custom' => $get_array_invoice_client_detail_in_postmeta[0]['billing_custom']
        );
    }

    function Get_BillingInfoAddress($get_array_invoice_client_detail_in_postmeta) {
        return $BillingInfoAddress = array(
            'Line1' => $get_array_invoice_client_detail_in_postmeta[0]['billing_address_1'],
            'Line2' => $get_array_invoice_client_detail_in_postmeta[0]['billing_address_2'],
            'City' => $get_array_invoice_client_detail_in_postmeta[0]['billing_city'],
            'State' => $get_array_invoice_client_detail_in_postmeta[0]['billing_state'],
            'PostalCode' => $get_array_invoice_client_detail_in_postmeta[0]['billing_postcode'],
            'CountryCode' => $get_array_invoice_client_detail_in_postmeta[0]['billing_country']
        );
    }

    function Get_ShippingInfo($get_array_invoice_client_detail_in_postmeta) {
        return $shippingInfo = array(
            'FirstName' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_first_name']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_first_name'] : '',
            'LastName' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_last_name']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_last_name'] : '',
            'BusinessName' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_businessname']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_businessname'] : '',
            'Phone' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_phone']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_phone'] : '',
            'Fax' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_fax']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_fax'] : '',
            'Website' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_website']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_website'] : '',
            'Custom' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_custom']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_custom'] : ''
        );
    }

    function Get_ShippingInfoAddress($get_array_invoice_client_detail_in_postmeta) {
        return $shippingInfoAddress = array(
            'Line1' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_address_1']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_address_1'] : '',
            'Line2' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_address_2']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_address_2'] : '',
            'City' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_city']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_city'] : '',
            'State' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_state']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_state'] : '',
            'PostalCode' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_postcode']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_postcode'] : '',
            'CountryCode' => ($get_array_invoice_client_detail_in_postmeta[0]['shipping_country']) ? $get_array_invoice_client_detail_in_postmeta[0]['shipping_country'] : '',
        );
    }

    function Get_Invoice_Item_Array($get_obj_invoice_item_detail_in_postmeta_table) {
        $InvoiceItems = array();
        if (is_array($get_obj_invoice_item_detail_in_postmeta_table)) {
            $i = 1;
            while (isset($get_obj_invoice_item_detail_in_postmeta_table[0]['item_name' . $i])) {
                $invoice_tax_rate = '';
                $invoice_tax_name = '';
                if (isset($get_obj_invoice_item_detail_in_postmeta_table[0]['tax_rate' . $i]) && !empty($get_obj_invoice_item_detail_in_postmeta_table[0]['tax_rate' . $i])) {
                    $tax_data = explode('-', $get_obj_invoice_item_detail_in_postmeta_table[0]['tax_rate' . $i]);
                    $invoice_tax_rate = $tax_data[0];
                    $invoice_tax_name = $tax_data[1];
                }
                $InvoiceItem = array(
                    'Name' => $get_obj_invoice_item_detail_in_postmeta_table[0]['item_name' . $i],
                    'Description' => $get_obj_invoice_item_detail_in_postmeta_table[0]['item_description' . $i],
                    'Date' => '', // Date on which the product or service was provided.
                    'Quantity' => $get_obj_invoice_item_detail_in_postmeta_table[0]['item_qty' . $i],
                    'UnitPrice' => $get_obj_invoice_item_detail_in_postmeta_table[0]['item_cost' . $i],
                    'TaxName' => strtoupper($invoice_tax_name),
                    'TaxRate' => $invoice_tax_rate
                );
                $i++;
                array_push($InvoiceItems, $InvoiceItem);
            }
            return $InvoiceItems;
        }
    }

}
