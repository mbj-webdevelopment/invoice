<?php

/**
 * @since      1.0.0
 * @package    Invoice
 * @subpackage Invoice/includes
 * @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Paypal_Invoice_Credentials_For_Wordpress_Setting {

    public static function init() {
        add_action('payment_invoice_credentials_tab_setting_save_field', array(__CLASS__, 'payment_invoice_credentials_tab_setting_save_field'));
        add_action('payment_invoice_credentials_tab_setting', array(__CLASS__, 'payment_invoice_credentials_tab_setting'));
        add_action('payment_invoice_tax_name_rate_tab_setting_save_field', array(__CLASS__, 'payment_invoice_tax_name_rate_tab_setting_save_field'));
        add_action('payment_invoice_tax_name_rate_tab_setting', array(__CLASS__, 'payment_invoice_tax_name_rate_tab_setting'));
        add_action('wp_ajax_payment_invoice_tax_name_rate_remove', array(__CLASS__, 'payment_invoice_tax_name_rate_remove'));
        add_action('payment_invoice_client_create_for_wordpress_setting_save_field', array(__CLASS__, 'payment_invoice_client_create_for_wordpress_setting_save_field'));
        add_action('payment_invoice_client_create_for_wordpress_setting', array(__CLASS__, 'payment_invoice_client_create_for_wordpress_setting'));
    }

    public static function paypal_invoice_for_wordpress_setting_fields() {
        $fields[] = array('title' => __('PayPal API Credentials', 'paypal-invoicing'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array(
            'title' => __('Application ID', 'paypal-invoicing'),
            'desc' => __('Enter your ApplicationID'),
            'id' => 'invoice_application_id',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Developer Account Email Address', 'paypal-invoicing'),
            'desc' => __('Enter your Developer Account Email Address'),
            'id' => 'invoice_developer_account_email_address',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Merchant Email', 'paypal-invoicing'),
            'desc' => __('Enter your Merchant Email'),
            'id' => 'invoice_merchant_email',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('API User Name', 'paypal-invoicing'),
            'desc' => __('Enter your API User Name'),
            'id' => 'invoice_api_username',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('API Password', 'paypal-invoicing'),
            'desc' => __('Enter your API Password'),
            'id' => 'invoice_api_password',
            'type' => 'password',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('API Signature', 'paypal-invoicing'),
            'desc' => __('Enter your API Signature'),
            'id' => 'invoice_api_signature',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Invoice Prefix', 'paypal-invoicing'),
            'desc' => __('Enter your Invoice Prefix'),
            'id' => 'invoice_custom_prefix',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Sandbox', 'paypal-invoicing'),
            'desc' => __('Enable Sandbox'),
            'id' => 'invoice_sandbox_enable',
            'type' => 'checkbox',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Currncy Code', 'paypal-invoicing'),
            'desc' => __('Select Currncy Code'),
            'id' => 'invoice_currncy_code',
            'type' => 'select',
            'css' => 'min-width:300px;',
            'options' => self::Get_Currncy_Code_And_Symbol()
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function Get_Currncy_Code_And_Symbol() {
        $return_code_with_symbol = array();
        $currncy_code = array(
            'AED' => __('United Arab Emirates Dirham', 'paypal-invoicing'),
            'AUD' => __('Australian Dollars', 'paypal-invoicing'),
            'BDT' => __('Bangladeshi Taka', 'paypal-invoicing'),
            'BRL' => __('Brazilian Real', 'paypal-invoicing'),
            'BGN' => __('Bulgarian Lev', 'paypal-invoicing'),
            'CAD' => __('Canadian Dollars', 'paypal-invoicing'),
            'CLP' => __('Chilean Peso', 'paypal-invoicing'),
            'CNY' => __('Chinese Yuan', 'paypal-invoicing'),
            'COP' => __('Colombian Peso', 'paypal-invoicing'),
            'CZK' => __('Czech Koruna', 'paypal-invoicing'),
            'DKK' => __('Danish Krone', 'paypal-invoicing'),
            'DOP' => __('Dominican Peso', 'paypal-invoicing'),
            'EUR' => __('Euros', 'paypal-invoicing'),
            'HKD' => __('Hong Kong Dollar', 'paypal-invoicing'),
            'HRK' => __('Croatia kuna', 'paypal-invoicing'),
            'HUF' => __('Hungarian Forint', 'paypal-invoicing'),
            'ISK' => __('Icelandic krona', 'paypal-invoicing'),
            'IDR' => __('Indonesia Rupiah', 'paypal-invoicing'),
            'INR' => __('Indian Rupee', 'paypal-invoicing'),
            'NPR' => __('Nepali Rupee', 'paypal-invoicing'),
            'ILS' => __('Israeli Shekel', 'paypal-invoicing'),
            'JPY' => __('Japanese Yen', 'paypal-invoicing'),
            'KIP' => __('Lao Kip', 'paypal-invoicing'),
            'KRW' => __('South Korean Won', 'paypal-invoicing'),
            'MYR' => __('Malaysian Ringgits', 'paypal-invoicing'),
            'MXN' => __('Mexican Peso', 'paypal-invoicing'),
            'NGN' => __('Nigerian Naira', 'paypal-invoicing'),
            'NOK' => __('Norwegian Krone', 'paypal-invoicing'),
            'NZD' => __('New Zealand Dollar', 'paypal-invoicing'),
            'PYG' => __('Paraguayan Guaraní', 'paypal-invoicing'),
            'PHP' => __('Philippine Pesos', 'paypal-invoicing'),
            'PLN' => __('Polish Zloty', 'paypal-invoicing'),
            'GBP' => __('Pounds Sterling', 'paypal-invoicing'),
            'RON' => __('Romanian Leu', 'paypal-invoicing'),
            'RUB' => __('Russian Ruble', 'paypal-invoicing'),
            'SGD' => __('Singapore Dollar', 'paypal-invoicing'),
            'ZAR' => __('South African rand', 'paypal-invoicing'),
            'SEK' => __('Swedish Krona', 'paypal-invoicing'),
            'CHF' => __('Swiss Franc', 'paypal-invoicing'),
            'TWD' => __('Taiwan New Dollars', 'paypal-invoicing'),
            'THB' => __('Thai Baht', 'paypal-invoicing'),
            'TRY' => __('Turkish Lira', 'paypal-invoicing'),
            'UAH' => __('Ukrainian Hryvnia', 'paypal-invoicing'),
            'USD' => __('US Dollars', 'paypal-invoicing'),
            'VND' => __('Vietnamese Dong', 'paypal-invoicing'),
            'EGP' => __('Egyptian Pound', 'paypal-invoicing'),
        );
        foreach ($currncy_code as $key => $value) {
            $symbol = self::Get_Currncy_Symbol($key);
            $currncy_with_symbol = $value . ' ( ' . $symbol . ' )';
            $return_code_with_symbol[$key] = $currncy_with_symbol;
        }
        return $return_code_with_symbol;
    }

    public static function Get_Currncy_Symbol($currency) {
        $currency_symbol = '';
        switch ($currency) {
            case 'AED' :
                $currency_symbol = 'د.إ';
                break;
            case 'AUD' :
            case 'CAD' :
            case 'CLP' :
            case 'COP' :
            case 'HKD' :
            case 'MXN' :
            case 'NZD' :
            case 'SGD' :
            case 'USD' :
                $currency_symbol = '&#36;';
                break;
            case 'BDT':
                $currency_symbol = '&#2547;&nbsp;';
                break;
            case 'BGN' :
                $currency_symbol = '&#1083;&#1074;.';
                break;
            case 'BRL' :
                $currency_symbol = '&#82;&#36;';
                break;
            case 'CHF' :
                $currency_symbol = '&#67;&#72;&#70;';
                break;
            case 'CNY' :
            case 'JPY' :
            case 'RMB' :
                $currency_symbol = '&yen;';
                break;
            case 'CZK' :
                $currency_symbol = '&#75;&#269;';
                break;
            case 'DKK' :
                $currency_symbol = 'kr.';
                break;
            case 'DOP' :
                $currency_symbol = 'RD&#36;';
                break;
            case 'EGP' :
                $currency_symbol = 'EGP';
                break;
            case 'EUR' :
                $currency_symbol = '&euro;';
                break;
            case 'GBP' :
                $currency_symbol = '&pound;';
                break;
            case 'HRK' :
                $currency_symbol = 'Kn';
                break;
            case 'HUF' :
                $currency_symbol = '&#70;&#116;';
                break;
            case 'IDR' :
                $currency_symbol = 'Rp';
                break;
            case 'ILS' :
                $currency_symbol = '&#8362;';
                break;
            case 'INR' :
                $currency_symbol = 'Rs.';
                break;
            case 'ISK' :
                $currency_symbol = 'Kr.';
                break;
            case 'KIP' :
                $currency_symbol = '&#8365;';
                break;
            case 'KRW' :
                $currency_symbol = '&#8361;';
                break;
            case 'MYR' :
                $currency_symbol = '&#82;&#77;';
                break;
            case 'NGN' :
                $currency_symbol = '&#8358;';
                break;
            case 'NOK' :
                $currency_symbol = '&#107;&#114;';
                break;
            case 'NPR' :
                $currency_symbol = 'Rs.';
                break;
            case 'PHP' :
                $currency_symbol = '&#8369;';
                break;
            case 'PLN' :
                $currency_symbol = '&#122;&#322;';
                break;
            case 'PYG' :
                $currency_symbol = '&#8370;';
                break;
            case 'RON' :
                $currency_symbol = 'lei';
                break;
            case 'RUB' :
                $currency_symbol = '&#1088;&#1091;&#1073;.';
                break;
            case 'SEK' :
                $currency_symbol = '&#107;&#114;';
                break;
            case 'THB' :
                $currency_symbol = '&#3647;';
                break;
            case 'TRY' :
                $currency_symbol = '&#8378;';
                break;
            case 'TWD' :
                $currency_symbol = '&#78;&#84;&#36;';
                break;
            case 'UAH' :
                $currency_symbol = '&#8372;';
                break;
            case 'VND' :
                $currency_symbol = '&#8363;';
                break;
            case 'ZAR' :
                $currency_symbol = '&#82;';
                break;
            default :
                $currency_symbol = '';
                break;
        }
        return $currency_symbol;
    }

    public static function payment_invoice_credentials_tab_setting() {
        $invoice_cradentials_setting_fields = self::paypal_invoice_for_wordpress_setting_fields();
        $Html_output = new Paypal_Invoice_Credentials_For_Wordpress_Html_output();
        ?>
        <form id="mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
        <?php $Html_output->init($invoice_cradentials_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="mailChimp_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function payment_invoice_credentials_tab_setting_save_field() {
        $invoice_cradentials_setting_fields = self::paypal_invoice_for_wordpress_setting_fields();
        $Html_output = new Paypal_Invoice_Credentials_For_Wordpress_Html_output();
        $Html_output->save_fields($invoice_cradentials_setting_fields);
    }

    public static function payment_invoice_tax_name_rate_tab_setting() {
        ?>
        <div class="wrap">
            <form method="post" id="paypal_invoice_tax_form" action="" enctype="multipart/form-data">
                <h3>Invoice Tax Settings</h3>
                <input type="checkbox" name="paypal_invoice_tax_enable" <?php echo (get_option('paypal_invoice_tax_enable')) ? 'checked' : ''; ?>> Enable Tax
                <div class="paypal_invoice_tax_add_new">
                    <button class="add_field_button button">Add New Tax</button><br><br>
                </div>
                <table class='widefat' id="paypal_invoice_tax_section">
                    <thead>
                        <tr>
                            <th>Tax Name</th>
                            <th>Tax Rate</th>
                            <th>Active / Inactive</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Tax Name</th>
                            <th>Tax Rate</th>
                            <th class="w115">Active / Inactive</th>
                            <th class="w80">Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
        <?php self::payment_invoice_display_tax_name_rate_helper(); ?>
                    </tbody>
                </table>
                <p class="submit">
                    <input name="save" class="button-primary" type="submit" value="<?php _e('Save changes', 'Option'); ?>" />
                </p>
            </form>
        </div>
        <?php
    }

    public static function payment_invoice_tax_name_rate_tab_setting_save_field() {
        $postdata = (isset($_POST) && !empty($_POST)) ? $_POST : false;
        $final_ipn_forwarding = array();
        if ($postdata) {
            update_option('paypal_invoice_tax_enable', $postdata['paypal_invoice_tax_enable'], true);
            if (array_key_exists('paypal_invoice_tax_enable', $postdata)) {
                unset($postdata['paypal_invoice_tax_enable']);
            }
            unset($postdata['save']);
            foreach ($postdata as $postdata_key => $postdata_value) {
                if (isset($postdata_value[0]) && !empty($postdata_value[0])) {
                    $fieldarray = explode('_', $postdata_key);
                    $final_ipn_forwarding[$fieldarray[3]][$fieldarray[0] . '_' . $fieldarray[1] . '_' . $fieldarray[2]] = $postdata_value[0];
                }
            }
            update_option('paypal_invoice_tax_setting', $final_ipn_forwarding, true);
        }
    }

    public static function payment_invoice_tax_name_rate_remove() {
        check_ajax_referer('paypal_invoice_tax', 'security');
        $postdata = !empty($_POST['value']) ? $_POST['value'] : false;
        if ($postdata[0]['name'] == 'paypal_invoice_tax_enable') {
            update_option('paypal_invoice_tax_enable', $postdata[0]['value'], true);
            unset($postdata[0]);
        }
        if (isset($postdata) && !empty($postdata)) {
            foreach ($postdata as $postdata_key => $postdata_value) {
                $fieldarray = explode('_', $postdata_value['name']);
                if (isset($postdata_value['value']) && !empty($postdata_value['value'])) {
                    $final_ipn_forwarding[$fieldarray[3]][$fieldarray[0] . '_' . $fieldarray[1] . '_' . $fieldarray[2]] = $postdata_value['value'];
                }
            }
            update_option('paypal_invoice_tax_setting', $final_ipn_forwarding, true);
        } else {
            update_option('paypal_invoice_tax_setting', '', true);
        }
    }

    public static function payment_invoice_display_tax_name_rate_helper() {
        $paypal_invoice_tax_setting_serialize = maybe_unserialize(get_option('paypal_invoice_tax_setting'));
        if (empty($paypal_invoice_tax_setting_serialize)) {
            $paypal_invoice_tax_setting_serialize = array('0' => '');
        }
        foreach ($paypal_invoice_tax_setting_serialize as $serialize_key => $serialize_value) {
            echo sprintf("<tr id='%d'><td colspan='1'><input type='text' name='paypalinvoice_tax_name_%d_[]' value='%s' class='medium-text' placeholder='Tax Name'></td><td colspan=''><input type='text' name='paypalinvoice_tax_rate_%d_[]' value='%s' class='medium-text' placeholder='Tax Rate %s'></td><td class='center'><input name='taxactive_inactive_checkbox_%d_[]' type='checkbox' id='switch_checkbox' %s></td><td><a class='delete' title='Remove PayPal Invoice Tax'>Delete</a></td></tr>", $serialize_key, $serialize_key, (isset($serialize_value['paypalinvoice_tax_name'])) ? $serialize_value['paypalinvoice_tax_name'] : '', $serialize_key, (isset($serialize_value['paypalinvoice_tax_rate'])) ? $serialize_value['paypalinvoice_tax_rate'] : '', '(%)', $serialize_key, (isset($serialize_value['taxactive_inactive_checkbox']) && $serialize_value['taxactive_inactive_checkbox'] == 'on') ? 'checked' : '');
        }
    }

    public static function payment_invoice_client_create_for_wordpress_setting() {
        $client_setting_fields = self::paypal_client_invoice_for_wordpress_setting_fields();
        $Html_output = new Paypal_Invoice_Credentials_For_Wordpress_Html_output();
        $Html_output->init($client_setting_fields);
    }

    public static function paypal_client_invoice_for_wordpress_setting_fields() {
        $fields[] = array('title' => __('', 'paypal-invoicing'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array(
            'title' => __('FirstName', 'paypal-invoicing'),
            'desc' => __('Enter your FirstName'),
            'id' => 'billing_first_name',
            'type' => 'text',
            'css' => 'min-width:300px;',
            'default' => 'a'
        );
        $fields[] = array(
            'title' => __('LastName', 'paypal-invoicing'),
            'desc' => __('Enter your LastName'),
            'id' => 'billing_last_name',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('BusinessName', 'paypal-invoicing'),
            'desc' => __('Enter your BusinessName'),
            'id' => 'billing_businessname',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Address 1', 'paypal-invoicing'),
            'desc' => __('Enter your Address 1'),
            'id' => 'billing_address_1',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Address 2', 'paypal-invoicing'),
            'desc' => __('Enter your Address 2'),
            'id' => 'billing_address_2',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Currncy Code', 'paypal-invoicing'),
            'desc' => __('Select Currncy Code'),
            'id' => 'currncy_code',
            'type' => 'select',
            'css' => 'min-width:300px;',
            'options' => self::Get_Currncy_Code_And_Symbol()
        );
        $fields[] = array(
            'title' => __('State', 'paypal-invoicing'),
            'desc' => __('Enter your State'),
            'id' => 'billing_state',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('City', 'paypal-invoicing'),
            'desc' => __('Enter your City'),
            'id' => 'billing_city',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Postcode', 'paypal-invoicing'),
            'desc' => __('Enter your Postcode'),
            'id' => 'billing_postcode',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Telephone', 'paypal-invoicing'),
            'desc' => __('Enter your Telephone'),
            'id' => 'billing_phone',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Fax', 'paypal-invoicing'),
            'desc' => __('Enter your Fax'),
            'id' => 'billing_fax',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Website', 'paypal-invoicing'),
            'desc' => __('Enter your Website'),
            'id' => 'billing_website',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Custom Information', 'paypal-invoicing'),
            'desc' => __('Enter your Custom Information'),
            'id' => 'billing_custom',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Payer Email', 'paypal-invoicing'),
            'desc' => __('Enter Payer Email'),
            'id' => 'payer_email',
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function payment_invoice_client_create_for_wordpress_setting_save_field() {
        $client_setting_fields = self::paypal_client_invoice_for_wordpress_setting_fields();
        $Html_output = new Paypal_Invoice_Credentials_For_Wordpress_Html_output();
        $Html_output->save_fields($client_setting_fields);
    }

}

Paypal_Invoice_Credentials_For_Wordpress_Setting::init();
