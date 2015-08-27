<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Paypal_Invoice
 * @subpackage Paypal_Invoice/admin
 * @author     paypal-invoice <paypal-invoice@gmail.com>
 */
class Invoice_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/invoice-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'jquery-ui-datepicker', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/invoice-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . 'one', plugin_dir_url(__FILE__) . 'js/example.js', array(), '1.0.0', true);
        wp_enqueue_script('jquery-ui-datepicker');
        if (wp_script_is($this->plugin_name)) {
            wp_localize_script($this->plugin_name, 'paypal_invoice_tax_params', apply_filters('paypal_invoice_tax_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'paypal_invoice_tax' => wp_create_nonce("paypal_invoice_tax"),
            )));
        }
    }

    public function paypal_invoice_post_init() {
        $labels = array(
            'name' => _x('Invoices', 'post type general name', 'paypal-invoicing'),
            'singular_name' => _x('Invoice', 'post type singular name', 'paypal-invoicing'),
            'menu_name' => _x('Invoices', 'admin menu', 'paypal-invoicing'),
            'name_admin_bar' => _x('Invoice', 'add new on admin bar', 'paypal-invoicing'),
            'add_new' => _x('Add New', 'Invoice', 'paypal-invoicing'),
            'add_new_item' => __('Add New Invoice', 'paypal-invoicing'),
            'new_item' => __('New Invoice', 'paypal-invoicing'),
            'edit_item' => __('Edit Invoice', 'paypal-invoicing'),
            'view_item' => __('View Invoice', 'paypal-invoicing'),
            'all_items' => __('All Invoices', 'paypal-invoicing'),
            'search_items' => __('Search Invoices', 'paypal-invoicing'),
            'parent_item_colon' => __('Parent Invoices:', 'paypal-invoicing'),
            'not_found' => __('No Invoices found.', 'paypal-invoicing'),
            'not_found_in_trash' => __('No Invoices found in Trash.', 'paypal-invoicing')
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'Invoice', 'with_front' => true),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title')
        );
        register_post_type('invoice', $args);
    }

    public function paypal_invoice_client_create_init() {
        $labels = array(
            'name' => _x('Clients', 'post type general name', 'paypal-invoicing'),
            'singular_name' => _x('Client', 'post type singular name', 'paypal-invoicing'),
            'menu_name' => _x('Clients', 'admin menu', 'paypal-invoicing'),
            'name_admin_bar' => _x('Client', 'add new on admin bar', 'paypal-invoicing'),
            'add_new' => _x('Add New', 'Client', 'paypal-invoicing'),
            'add_new_item' => __('Add New Client', 'paypal-invoicing'),
            'new_item' => __('New Client', 'paypal-invoicing'),
            'edit_item' => __('Edit Client', 'paypal-invoicing'),
            'view_item' => __('View Client', 'paypal-invoicing'),
            'all_items' => __('All Client', 'paypal-invoicing'),
            'search_items' => __('Search Client', 'paypal-invoicing'),
            'parent_item_colon' => __('Parent Client:', 'paypal-invoicing'),
            'not_found' => __('No Clients found.', 'paypal-invoicing'),
            'not_found_in_trash' => __('No Clients found in Trash.', 'paypal-invoicing')
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'Client', 'with_front' => true),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title')
        );
        register_post_type('clients', $args);
    }

    public function add_client_meta_boxes_detail() {
        add_meta_box('paypal-buttons-meta-id', ('Client Details'), array(__CLASS__, 'paypal_wp_client_detail_manager_metabox_item'), 'clients', 'normal', 'high');
    }

    public function paypal_wp_client_detail_manager_metabox_item() {
        $client_details_result_array = array();
        $invoice_client_details_postmeta_json = get_post_meta(get_the_ID(), 'invoice_client_details');
        if (isset($invoice_client_details_postmeta_json[0])) {
            $client_details_result_array = $invoice_client_details_postmeta_json[0];
        }
        ?>
        <div class='wrap' id="paypal_invoice_client">
            <table class="widefat" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="4">Billing information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>First name</th>
                        <td>
                            <input type="text" id="billing_first_name" class="billing_first_name input" name="billing_first_name" value="<?php echo (isset($client_details_result_array['billing_first_name'])) ? $client_details_result_array['billing_first_name'] : '' ?>">
                        </td>
                        <th>Last name</th>
                        <td>
                            <input type="text" id="billing_last_name" class="billing_last_name" name="billing_last_name" value="<?php echo (isset($client_details_result_array['billing_last_name'])) ? $client_details_result_array['billing_last_name'] : '' ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>Business name</th>
                        <td>
                            <input type="text" id="billing_businessname" class="billing_businessname" name="billing_businessname" value="<?php echo (isset($client_details_result_array['billing_businessname'])) ? $client_details_result_array['billing_businessname'] : '' ?>">
                        </td>
                        <th>Recipient's email address</th>
                        <td><input type="text" id="payer_email" class="payer_email" name="payer_email" value="<?php echo (isset($client_details_result_array['payer_email'])) ? $client_details_result_array['payer_email'] : '' ?>"></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><input type="text" id="billing_phone" class="billing_phone" name="billing_phone" value="<?php echo (isset($client_details_result_array['billing_phone'])) ? $client_details_result_array['billing_phone'] : '' ?>"></td>
                        <th>Country</th>
                        <td><select id="billing_country" class="billing_country" name="billing_country" style="width: 190px;">
                                <?php
                                $country_array = self::get_county_list();
                                foreach ($country_array as $key => $value) {
                                    $selected = '';
                                    if ($client_details_result_array['billing_country'] == $key) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
        <?php }
        ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Address Line1</th>
                        <td><input type="text" id="billing_address_1" class="billing_address_1" name="billing_address_1" value="<?php echo (isset($client_details_result_array['billing_address_1'])) ? $client_details_result_array['billing_address_1'] : '' ?>"></td>
                        <th>Address Line2</th>
                        <td><input type="text" id="billing_address_2" class="billing_address_2" name="billing_address_2" value="<?php echo (isset($client_details_result_array['billing_address_2'])) ? $client_details_result_array['billing_address_2'] : '' ?>"></td>
                    </tr>
                    <tr>
                        <th>Town / City</th>
                        <td><input type="text" id="billing_city" class="billing_city" name="billing_city" value="<?php echo (isset($client_details_result_array['billing_city'])) ? $client_details_result_array['billing_city'] : '' ?>"></td>
                        <th>State / County</th>
                        <td><input type="text" id="billing_state" class="billing_state" name="billing_state" value="<?php echo (isset($client_details_result_array['billing_state'])) ? $client_details_result_array['billing_state'] : '' ?>"></td>
                    </tr>
                    <tr>
                        <th>Postcode / Zip</th>
                        <td><input type="text" id="billing_postcode" class="billing_postcode" name="billing_postcode" value="<?php echo (isset($client_details_result_array['billing_postcode'])) ? $client_details_result_array['billing_postcode'] : '' ?>"></td>
                        <th>Fax</th>
                        <td><input type="text" id="billing_fax" class="billing_fax" name="billing_fax" value="<?php echo (isset($client_details_result_array['billing_fax'])) ? $client_details_result_array['billing_fax'] : '' ?>"></td>

                    </tr>
                    <tr>
                        <th>Website</th>
                        <td><input type="text" id="billing_website" class="billing_website" name="billing_website" value="<?php echo (isset($client_details_result_array['billing_website'])) ? $client_details_result_array['billing_website'] : '' ?>"></td>
                        <td>Other Information</td>
                        <td><input type="text" id="billing_custom" class="billing_custom" name="billing_custom" value="<?php echo (isset($client_details_result_array['billing_custom'])) ? $client_details_result_array['billing_custom'] : '' ?>"></td>
                    </tr>
                </tbody>
            </table><br>
            <table class="widefat" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="4">Ship to a different address?<input style="margin: 3px 0 0 9px;" type="checkbox" id="same_address" class="same_address" name="invoice_ship_to_different_address" <?php echo (isset($client_details_result_array['invoice_ship_to_different_address'])) ? 'checked' : '' ?>></th>
                    </tr>
                </thead>
                <tbody <?php echo (isset($client_details_result_array['invoice_ship_to_different_address'])) ? '' : 'style="display: none"' ?>  class="same_address_tbody">
                    <tr>
                        <th>First Name</th>
                        <td><input type="text" id="shipping_first_name" class="shipping_first_name" name="shipping_first_name" value="<?php echo (isset($client_details_result_array['shipping_first_name'])) ? $client_details_result_array['shipping_first_name'] : '' ?>"></td>
                        <th>Last Name</th>
                        <td><input type="text" id="shipping_last_name" class="shipping_last_name" name="shipping_last_name" value="<?php echo (isset($client_details_result_array['shipping_last_name'])) ? $client_details_result_array['shipping_last_name'] : '' ?>"></td>
                    </tr>
                    <tr>
                        <th>Company Name</th>
                        <td><input type="text" id="shipping_businessname" class="shipping_businessname" name="shipping_businessname" value="<?php echo (isset($client_details_result_array['shipping_businessname'])) ? $client_details_result_array['shipping_businessname'] : '' ?>"></td>
                        <th>Phone</th>
                        <td><input type="text" id="shipping_phone" class="shipping_phone" name="shipping_phone" value="<?php echo (isset($client_details_result_array['shipping_phone'])) ? $client_details_result_array['shipping_phone'] : '' ?>"></td>
                    </tr>
                    <tr>
                        <th>Website</th>
                        <td><input type="text" id="shipping_website" class="shipping_website" name="shipping_website" value="<?php echo (isset($client_details_result_array['shipping_website'])) ? $client_details_result_array['shipping_website'] : '' ?>"></td>
                        <th>Country</th>
                        <td><select id="shipping_country" name="shipping_country" style="width: 190px;">
                                <?php
                                $country_array = self::get_county_list();
                                foreach ($country_array as $key => $value) {
                                    $selected = '';
                                    if ($client_details_result_array['shipping_country'] == $key) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
        <?php }
        ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Address Line1</th>
                        <td><input type="text" id="shipping_address_1" class="shipping_address_1" name="shipping_address_1" value="<?php echo (isset($client_details_result_array['shipping_address_1'])) ? $client_details_result_array['shipping_address_1'] : '' ?>"></td>
                        <th>Address Line2</th>
                        <td><input type="text" id="shipping_address_2" class="shipping_address_2" name="shipping_address_2" value="<?php echo (isset($client_details_result_array['shipping_address_2'])) ? $client_details_result_array['shipping_address_2'] : '' ?>"></td>
                    </tr>
                    <tr>
                        <th>Town / City</th>
                        <td><input type="text" id="shipping_city" class="shipping_city" name="shipping_city" value="<?php echo (isset($client_details_result_array['shipping_city'])) ? $client_details_result_array['shipping_city'] : '' ?>"></td>
                        <th>State / County</th>
                        <td><input type="text" id="shipping_state" class="shipping_state" name="shipping_state" value="<?php echo (isset($client_details_result_array['shipping_state'])) ? $client_details_result_array['shipping_state'] : '' ?>"></td>
                    </tr>
                    <tr>
                        <th>Postcode</th>
                        <td><input type="text" id="shipping_postcode" class="shipping_postcode" name="shipping_postcode" value="<?php echo (isset($client_details_result_array['shipping_postcode'])) ? $client_details_result_array['shipping_postcode'] : '' ?>"></td>
                        <th>Fax</th>
                        <td><input type="text" id="shipping_fax" class="shipping_fax" name="shipping_fax" value="<?php echo (isset($client_details_result_array['shipping_fax'])) ? $client_details_result_array['shipping_fax'] : '' ?>"></td>
                    </tr>
                    <tr>
                        <th>Other Information</th>
                        <td>
                            <input type="text" id="shipping_custom" class="shipping_custom" name="shipping_custom" value="<?php echo (isset($client_details_result_array['shipping_custom'])) ? $client_details_result_array['billing_custom'] : '' ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function add_meta_boxes_item_detail() {
        add_meta_box('paypal-buttons-meta-id', ('Invoice Detail'), array(__CLASS__, 'paypal_wp_item_detail_manager_metabox_item'), 'invoice', 'normal', 'high');
    }

    public static function get_county_list() {
        return $_countries = array(
            "GB" => "United Kingdom",
            "US" => "United States",
            "AF" => "Afghanistan",
            "AL" => "Albania",
            "DZ" => "Algeria",
            "AS" => "American Samoa",
            "AD" => "Andorra",
            "AO" => "Angola",
            "AI" => "Anguilla",
            "AQ" => "Antarctica",
            "AG" => "Antigua And Barbuda",
            "AR" => "Argentina",
            "AM" => "Armenia",
            "AW" => "Aruba",
            "AU" => "Australia",
            "AT" => "Austria",
            "AZ" => "Azerbaijan",
            "BS" => "Bahamas",
            "BH" => "Bahrain",
            "BD" => "Bangladesh",
            "BB" => "Barbados",
            "BY" => "Belarus",
            "BE" => "Belgium",
            "BZ" => "Belize",
            "BJ" => "Benin",
            "BM" => "Bermuda",
            "BT" => "Bhutan",
            "BO" => "Bolivia",
            "BA" => "Bosnia And Herzegowina",
            "BW" => "Botswana",
            "BV" => "Bouvet Island",
            "BR" => "Brazil",
            "IO" => "British Indian Ocean Territory",
            "BN" => "Brunei Darussalam",
            "BG" => "Bulgaria",
            "BF" => "Burkina Faso",
            "BI" => "Burundi",
            "KH" => "Cambodia",
            "CM" => "Cameroon",
            "CA" => "Canada",
            "CV" => "Cape Verde",
            "KY" => "Cayman Islands",
            "CF" => "Central African Republic",
            "TD" => "Chad",
            "CL" => "Chile",
            "CN" => "China",
            "CX" => "Christmas Island",
            "CC" => "Cocos (Keeling) Islands",
            "CO" => "Colombia",
            "KM" => "Comoros",
            "CG" => "Congo",
            "CD" => "Congo, The Democratic Republic Of The",
            "CK" => "Cook Islands",
            "CR" => "Costa Rica",
            "CI" => "Cote D'Ivoire",
            "HR" => "Croatia (Local Name: Hrvatska)",
            "CU" => "Cuba",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "DK" => "Denmark",
            "DJ" => "Djibouti",
            "DM" => "Dominica",
            "DO" => "Dominican Republic",
            "TP" => "East Timor",
            "EC" => "Ecuador",
            "EG" => "Egypt",
            "SV" => "El Salvador",
            "GQ" => "Equatorial Guinea",
            "ER" => "Eritrea",
            "EE" => "Estonia",
            "ET" => "Ethiopia",
            "FK" => "Falkland Islands (Malvinas)",
            "FO" => "Faroe Islands",
            "FJ" => "Fiji",
            "FI" => "Finland",
            "FR" => "France",
            "FX" => "France, Metropolitan",
            "GF" => "French Guiana",
            "PF" => "French Polynesia",
            "TF" => "French Southern Territories",
            "GA" => "Gabon",
            "GM" => "Gambia",
            "GE" => "Georgia",
            "DE" => "Germany",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GR" => "Greece",
            "GL" => "Greenland",
            "GD" => "Grenada",
            "GP" => "Guadeloupe",
            "GU" => "Guam",
            "GT" => "Guatemala",
            "GN" => "Guinea",
            "GW" => "Guinea-Bissau",
            "GY" => "Guyana",
            "HT" => "Haiti",
            "HM" => "Heard And Mc Donald Islands",
            "VA" => "Holy See (Vatican City State)",
            "HN" => "Honduras",
            "HK" => "Hong Kong",
            "HU" => "Hungary",
            "IS" => "Iceland",
            "IN" => "India",
            "ID" => "Indonesia",
            "IR" => "Iran (Islamic Republic Of)",
            "IQ" => "Iraq",
            "IE" => "Ireland",
            "IL" => "Israel",
            "IT" => "Italy",
            "JM" => "Jamaica",
            "JP" => "Japan",
            "JO" => "Jordan",
            "KZ" => "Kazakhstan",
            "KE" => "Kenya",
            "KI" => "Kiribati",
            "KP" => "Korea, Democratic People's Republic Of",
            "KR" => "Korea, Republic Of",
            "KW" => "Kuwait",
            "KG" => "Kyrgyzstan",
            "LA" => "Lao People's Democratic Republic",
            "LV" => "Latvia",
            "LB" => "Lebanon",
            "LS" => "Lesotho",
            "LR" => "Liberia",
            "LY" => "Libyan Arab Jamahiriya",
            "LI" => "Liechtenstein",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "MO" => "Macau",
            "MK" => "Macedonia, Former Yugoslav Republic Of",
            "MG" => "Madagascar",
            "MW" => "Malawi",
            "MY" => "Malaysia",
            "MV" => "Maldives",
            "ML" => "Mali",
            "MT" => "Malta",
            "MH" => "Marshall Islands",
            "MQ" => "Martinique",
            "MR" => "Mauritania",
            "MU" => "Mauritius",
            "YT" => "Mayotte",
            "MX" => "Mexico",
            "FM" => "Micronesia, Federated States Of",
            "MD" => "Moldova, Republic Of",
            "MC" => "Monaco",
            "MN" => "Mongolia",
            "MS" => "Montserrat",
            "MA" => "Morocco",
            "MZ" => "Mozambique",
            "MM" => "Myanmar",
            "NA" => "Namibia",
            "NR" => "Nauru",
            "NP" => "Nepal",
            "NL" => "Netherlands",
            "AN" => "Netherlands Antilles",
            "NC" => "New Caledonia",
            "NZ" => "New Zealand",
            "NI" => "Nicaragua",
            "NE" => "Niger",
            "NG" => "Nigeria",
            "NU" => "Niue",
            "NF" => "Norfolk Island",
            "MP" => "Northern Mariana Islands",
            "NO" => "Norway",
            "OM" => "Oman",
            "PK" => "Pakistan",
            "PW" => "Palau",
            "PA" => "Panama",
            "PG" => "Papua New Guinea",
            "PY" => "Paraguay",
            "PE" => "Peru",
            "PH" => "Philippines",
            "PN" => "Pitcairn",
            "PL" => "Poland",
            "PT" => "Portugal",
            "PR" => "Puerto Rico",
            "QA" => "Qatar",
            "RE" => "Reunion",
            "RO" => "Romania",
            "RU" => "Russian Federation",
            "RW" => "Rwanda",
            "KN" => "Saint Kitts And Nevis",
            "LC" => "Saint Lucia",
            "VC" => "Saint Vincent And The Grenadines",
            "WS" => "Samoa",
            "SM" => "San Marino",
            "ST" => "Sao Tome And Principe",
            "SA" => "Saudi Arabia",
            "SN" => "Senegal",
            "SC" => "Seychelles",
            "SL" => "Sierra Leone",
            "SG" => "Singapore",
            "SK" => "Slovakia (Slovak Republic)",
            "SI" => "Slovenia",
            "SB" => "Solomon Islands",
            "SO" => "Somalia",
            "ZA" => "South Africa",
            "GS" => "South Georgia, South Sandwich Islands",
            "ES" => "Spain",
            "LK" => "Sri Lanka",
            "SH" => "St. Helena",
            "PM" => "St. Pierre And Miquelon",
            "SD" => "Sudan",
            "SR" => "Suriname",
            "SJ" => "Svalbard And Jan Mayen Islands",
            "SZ" => "Swaziland",
            "SE" => "Sweden",
            "CH" => "Switzerland",
            "SY" => "Syrian Arab Republic",
            "TW" => "Taiwan",
            "TJ" => "Tajikistan",
            "TZ" => "Tanzania, United Republic Of",
            "TH" => "Thailand",
            "TG" => "Togo",
            "TK" => "Tokelau",
            "TO" => "Tonga",
            "TT" => "Trinidad And Tobago",
            "TN" => "Tunisia",
            "TR" => "Turkey",
            "TM" => "Turkmenistan",
            "TC" => "Turks And Caicos Islands",
            "TV" => "Tuvalu",
            "UG" => "Uganda",
            "UA" => "Ukraine",
            "AE" => "United Arab Emirates",
            "UM" => "United States Minor Outlying Islands",
            "UY" => "Uruguay",
            "UZ" => "Uzbekistan",
            "VU" => "Vanuatu",
            "VE" => "Venezuela",
            "VN" => "Viet Nam",
            "VG" => "Virgin Islands (British)",
            "VI" => "Virgin Islands (U.S.)",
            "WF" => "Wallis And Futuna Islands",
            "EH" => "Western Sahara",
            "YE" => "Yemen",
            "YU" => "Yugoslavia",
            "ZM" => "Zambia",
            "ZW" => "Zimbabwe"
        );
    }

    public static function paypal_wp_item_detail_manager_metabox_item() {
        $get_client_list = self::paypal_invoice_client_list_array();
        $item_details_result_array = array();
        $invoice_item_details_postmeta = get_post_meta(get_the_ID(), 'invoice_item_details');
        $invoice_item_details = $invoice_item_details_postmeta[0];
        $paypal_invoice_tax_enable = get_option('paypal_invoice_tax_enable');
        $invoice_currency_saymbol = get_option('invoice_currncy_code') ? get_option('invoice_currncy_code') : 'USD';
        if (isset($invoice_currency_saymbol)) {
            $invoice_currency_saymbol = Paypal_Invoice_Credentials_For_Wordpress_Setting::Get_Currncy_Symbol(get_option('invoice_currncy_code'));
        }
        ?>
        <div class='wrap' id="paypal_select_client">
            <table class="widefat" cellspacing="0">
                <thead>
                </thead>
                <tbody>
                    <tr>
        <?php if ($get_client_list) { ?>
                            <th>Select Client</th>
                            <td colspan="2">
                                <select class="client_invoice_dropdown" name="client_post_id" id="client_invoice_dropdown">
                                    <option value="0">Select Client</option>
                                    <?php
                                    foreach ($get_client_list as $client_key => $client_value) {
                                        $selected = "";
                                        if ($client_value['ID'] == $invoice_item_details_postmeta[0]['client_post_id']) {
                                            $selected = "selected";
                                        }
                                        $invoice_client_details_postmeta_json = get_post_meta($client_value['ID'], 'invoice_client_details');
                                        $invoice_client_details_obj = $invoice_client_details_postmeta_json[0];
                                        echo '<option value="' . $client_value['ID'] . '" ' . $selected . '>' . $invoice_client_details_obj['billing_first_name'] . ' ' . $invoice_client_details_obj['billing_last_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <a id="client_view" title="View Client Profile" href="<?php echo admin_url(); ?>post.php?post=0&action=edit" target="_blank"  class="button button-primary button-large"><?php _e('View Client Profile', 'paypal-invoicing'); ?></a>
                            </td>
                        <?php } else {
                            ?>
                            <th>No client list available, <a href="<?php echo admin_url('post-new.php?post_type=clients'); ?>">Click here</a> for create client profile.</th>
            <?php
        }
        ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class='wrap' >
            <table class="widefat" cellspacing="0" id="paypal_invoice_item">
                <thead>
                    <tr>
                        <th><?php _e('Item name/ID', 'paypal-invoicing'); ?></th>
                        <th><?php _e('Quantity', 'paypal-invoicing'); ?></th>
                        <th><?php _e('Unit price', 'paypal-invoicing'); ?></th>
        <?php if ('on' == $paypal_invoice_tax_enable) { ?>
                            <th><?php _e('Tax', 'paypal-invoicing'); ?></th>
        <?php } ?>
                        <th><?php _e('Amount', 'paypal-invoicing'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>       
                    <?php
                    if (is_array($invoice_item_details)) {
                        $key = 1;
                        while (isset($invoice_item_details['item_name' . $key])) {
                            ?>
                            <tr class="item-row" id="<?php echo $key; ?>">
                                <td><input type="text" id="Item" placeholder="<?php _e('Item Name', 'paypal-invoicing'); ?>" name="item_name<?php echo $key; ?>" value="<?php echo (isset($invoice_item_details['item_name' . $key])) ? $invoice_item_details['item_name' . $key] : ''; ?>"></td>
                                <td>
                                    <input type="text" id="Quantity" name="item_cost<?php echo $key; ?>" class="cost textarea" value="<?php echo (isset($invoice_item_details['item_cost' . $key])) ? $invoice_item_details['item_cost' . $key] : ''; ?>">
                                </td>
                                <td>
                                    <input type="text" id="UnitCost" name="item_qty<?php echo $key; ?>" class="qty textarea" value="<?php echo (isset($invoice_item_details['item_qty' . $key])) ? $invoice_item_details['item_qty' . $key] : ''; ?>">
                                </td>
                                        <?php if ('on' == $paypal_invoice_tax_enable) { ?>
                                    <td>                    
                                        <select id="text_rate" name="tax_rate<?php echo $key; ?>" class="tax_rate">
                                            <?php
                                            $tax_data = explode('-', $invoice_item_details['tax_rate' . $key]);
                                            $invoice_tax_name = $tax_data[1];
                                            echo $get_option_value_invoice_tax = self::get_tax_list_dropdown($invoice_tax_name);
                                            ?>
                                        </select>
                                    </td>
                <?php } ?>
                                <td>
                                    <input type="text" id="Price" class ="price input_box" id="item_price" name="item_price<?php echo $key; ?>" value="<?php echo (isset($invoice_item_details['item_price' . $key])) ? $invoice_item_details['item_price' . $key] : ' 0.00'; ?>" readonly="readonly"></div>
                                    <input type="text" class ="item_tax" id="item_tax<?php echo $key; ?>" name="item_tax<?php echo $key; ?>" value="<?php echo (isset($invoice_item_details['item_tax' . $key])) ? $invoice_item_details['item_tax' . $key] : '0'; ?>" hidden >
                                </td>
                                <td>
                                    <?php if ('1' == $key) { ?>
                                        <a id="addrow" title="Add New Invoice Item" class="button button-small">+</a>
                <?php } else { ?>
                                        <a class="delete button button-small" title="Add New Invoice Item" id="delete">X</a>
                <?php } ?>
                                </td>
                            </tr>
                            <tr id="item_details<?php echo $key; ?>">
                                <td colspan="6">
                                    <textarea  cols="82" rows="1" id="Item_details" name="item_description<?php echo $key; ?>" placeholder="<?php _e('Description (optional)', 'paypal-invoicing'); ?> "><?php echo (isset($invoice_item_details['item_description' . $key])) ? $invoice_item_details['item_description' . $key] : ''; ?></textarea>
                                </td>
                            </tr>

                            <?php
                            $key++;
                        }
                    } else {
                        ?>
                        <tr class="item-row" id="1">
                            <td><input type="text" id="Item" placeholder="<?php _e('Item Name', 'paypal-invoicing'); ?>" name="item_name1" value=""></td>
                            <td>
                                <input type="text" id="Quantity" name="item_cost1" class="cost textarea" value="">
                            </td>
                            <td>
                                <input type="text" id="UnitCost" name="item_qty1" class="qty textarea" value="">
                            </td>
                                    <?php if ('on' == $paypal_invoice_tax_enable) { ?>
                                <td>                   
                                    <select id="text_rate" name="tax_rate1" class="tax_rate">
                                <?php echo $get_option_value_invoice_tax = self::get_tax_list_dropdown(); ?>
                                    </select>
                                </td>
            <?php } ?>
                            <td>
                                <input type="text" id="Price" class ="price input_box" id="item_price1" name="item_price1" value="0.00" readonly="readonly"></div>
                                <input type="text" class ="item_tax" id="item_tax1" name="item_tax1" value="" hidden >
                            </td>
                            <td><a id="addrow" title="Add New Invoice Item" class="button button-small">+</a></td>
                        </tr>
                        <tr >
                            <td colspan="6">
                                <textarea  cols="82" rows="1" id="Item_details" name="item_description1" placeholder="<?php _e('Description (optional)', 'paypal-invoicing'); ?> "></textarea>
                            </td>
                        </tr>
        <?php } ?>  
                </tbody>
            </table>
            <table class="widefat" cellspacing="0">
                <tbody>
                    <tr>
                        <td colspan="3"></td>
                        <td><?php _e('Subtotal', 'paypal-invoicing'); ?></td>
                        <td>
                            <input class="input_box" type="text" name="subtotal" id="subtotal" value="<?php echo (isset($invoice_item_details['subtotal'])) ? $invoice_item_details['subtotal'] : ' 0.00'; ?>" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><?php _e('Total Tax', 'paypal-invoicing'); ?></td>
                        <td>
                            <div id="total-tax" class="disable_box"><input class="input_box" type="text" name="total_tax" id="total_tax" value="<?php echo (isset($invoice_item_details['total_tax'])) ? $invoice_item_details['total_tax'] : ' 0.00' ?>" readonly></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><?php _e('Invoice Descount', 'paypal-invoicing'); ?></td>
                        <td>
                            <div id="invoice-descount"><input type="text" class="invoice_descount input_box" name="invoice_descount" id="invoice_descount" value="<?php echo (isset($invoice_item_details['invoice_descount'])) ? $invoice_currency_saymbol . ' ' . $invoice_item_details['invoice_descount'] : $invoice_currency_saymbol . ' 0.00' ?>"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><?php _e('Total With Tax', 'paypal-invoicing'); ?></td>
                        <td>
                            <div id="total-data" class="input-group input-group-invoice input-group-lg disable_box"> <input class="input_addon" type="text" id="total" name="balance_with_tax" value="<?php echo (isset($invoice_item_details['balance_with_tax'])) ? $invoice_item_details['balance_with_tax'] : ' 0.00'; ?>" readonly></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><?php _e('Total Without Tax', 'paypal-invoicing'); ?></td>
                        <td>
                            <div class="input-group input-group-invoice input-group-lg due disable_box"><span class="input-group-addon"></span> <input class="due_balance input_addon" type="text" id="due_balance" name="balance_without_tax" value="<?php echo (isset($invoice_item_details['balance_without_tax'])) ? $invoice_item_details['balance_without_tax'] : ' 0.00'; ?>" readonly></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><?php _e('Total Pay', 'paypal-invoicing'); ?></td>
                        <td colspan="2" class="total-value pay">
                            <div class="input-group input-group-invoice input-group-lg due disable_box"><input class="total_pay input_addon" type="text" id="total_pay" name="total_pay" value="<?php echo (isset($invoice_item_details['total_pay'])) ? $invoice_item_details['total_pay'] : ' 0.00'; ?>" readonly></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="<?php echo $colspan + 2; ?>" class="table-footer-color">
                            <?php
                            $Paypal_invoice_status = array();
                            $invoice_opration_id_is_set = ($invoice_item_details['create_invoice_opration']) ? $invoice_item_details['create_invoice_opration'] : '0';
                            if (isset($invoice_opration_id_is_set)) {
                                if ('0' == $invoice_opration_id_is_set) {
                                    $Paypal_invoice_status[0] = 'Save Only Database';
                                    $Paypal_invoice_status[1] = 'Create Invoice';
                                    $Paypal_invoice_status[2] = 'Create & Send Invoice';
                                }
                                if ('1' == $invoice_opration_id_is_set[0]) {
                                    $Paypal_invoice_status[6] = 'Send Invoice';
                                    $Paypal_invoice_status[7] = 'Update Invoice';
                                    $Paypal_invoice_status[8] = 'Delete Invoice';
                                }
                                if ('2' == $invoice_opration_id_is_set[0]) {
                                    $Paypal_invoice_status[7] = 'Update Invoice';
                                    $Paypal_invoice_status[3] = 'Cancel Invoice';
                                }
                                if ('3' == $invoice_opration_id_is_set[0]) {
                                    $Paypal_invoice_status[0] = 'Save Only Database';
                                    $Paypal_invoice_status[1] = 'Create Invoice';
                                    $Paypal_invoice_status[2] = 'Create & Send Invoice';
                                }
                                if ('4' == $invoice_opration_id_is_set[0]) {
                                    $Paypal_invoice_status[0] = 'Save Only Database';
                                    $Paypal_invoice_status[1] = 'Create Invoice';
                                    $Paypal_invoice_status[2] = 'Create & Send Invoice';
                                }
                                if ('5' == $invoice_opration_id_is_set[0]) {
                                    $Paypal_invoice_status[0] = 'Save Only Database';
                                    $Paypal_invoice_status[1] = 'Create Invoice';
                                    $Paypal_invoice_status[2] = 'Create & Send Invoice';
                                }
                                if ('6' == $invoice_opration_id_is_set[0]) {
                                    $Paypal_invoice_status[7] = 'Update Invoice';
                                    $Paypal_invoice_status[3] = 'Cancel Invoice';
                                }
                                if ('7' == $invoice_opration_id_is_set[0]) {
                                    $Paypal_invoice_status[7] = 'Update Invoice';
                                }
                                if ('8' == $invoice_opration_id_is_set[0]) {
                                    $Paypal_invoice_status[0] = 'Save Only Database';
                                    $Paypal_invoice_status[1] = 'Create Invoice';
                                    $Paypal_invoice_status[2] = 'Create & Send Invoice';
                                }
                            }
                            $invoice_opration_option_value = "";
                            foreach ($Paypal_invoice_status as $key => $value) {
                                $selected_invoice = "";
                                if ('0' == $invoice_opration_id_is_set || '3' == $invoice_opration_id_is_set[0] || '4' == $invoice_opration_id_is_set[0] || '5' == $invoice_opration_id_is_set[0]) {
                                    if ('2' == $key) {
                                        $selected_invoice = "selected";
                                    }
                                }
                                $invoice_opration_option_value .= '<option value="' . $key . '" ' . $selected_invoice . '>' . $value . '</option>';
                            }
                            ?>
                            <span class="invoice_methods"><?php _e('Invoice Methods', 'paypal-invoicing'); ?></span>
                            <select name="create_invoice_opration" class="select_dropdowp"><?php echo $invoice_opration_option_value; ?></select>
                        </td>
                    </tr>
                <input hidden type="text" id="total_count_value" name="total_count_value"  value="<?php echo (isset($invoice_item_details['total_count_value'])) ? $invoice_item_details['total_count_value'] : '1'; ?>">
                <input hidden type="text" id="paypal_invoice_tax_enable_disable" name="paypal_invoice_tax_enable_disable"  value="<?php echo (isset($paypal_invoice_tax_enable)) ? $paypal_invoice_tax_enable : 'off'; ?>">                
                <input hidden type="text" id="display_date" name="display_date"  value="<?php echo get_option('date_display'); ?>">
                <input hidden type="text" id="invoice_currency_saymbol" name="invoice_currency_saymbol"  value="<?php echo $invoice_currency_saymbol; ?>">
                </tbody>
            </table>
        </div>
        <?php
    }

    public function payment_invoice() {
        add_options_page('PayPal Invoice', 'PayPal Invoice', 'manage_options', 'paypal-invoice-settings-option', array($this, 'payment_invoice_setting_menu'));
    }

    public static function payment_invoice_setting_menu() {
        $setting_tabs = apply_filters('invoice_options_setting_tab', array('credentials_tab' => 'General Settings', 'tax_name_rate_tab' => 'Tax Setting'));
        $current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'credentials_tab';
        ?>
        <h2 class="nav-tab-wrapper">
        <?php
        foreach ($setting_tabs as $name => $label) {
            echo '<a href="' . admin_url('admin.php?page=paypal-invoice-settings-option&tab=' . $name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
        }
        ?>
        </h2>
        <?php
        foreach ($setting_tabs as $setting_tabkey => $setting_tabvalue) {
            switch ($setting_tabkey) {
                case $current_tab:
                    do_action('payment_invoice_' . $setting_tabkey . '_setting_save_field');
                    do_action('payment_invoice_' . $setting_tabkey . '_setting');
                    break;
            }
        }
    }

    public function register_my_setting() {
        register_setting('paypal_api_credentials_register', 'paypal_api_credentials');
    }

    public function save_post_item_details($postID, $post, $update) {
        $slug = 'invoice';
        if ($slug != $post->post_type) {
            return;
        }
        $get_post_request_data = $_REQUEST;
        $invoice_item_data = array();
        $invoice_coustom_field = array();
        $invoice_item_coustom_field_merge = array();
        $invoice_post_request_data_final = array();
        if (isset($get_post_request_data['client_post_id']) && $get_post_request_data['client_post_id'] != '0') {
            $count_no_of_invoice_item = (isset($get_post_request_data['total_count_value'])) ? $get_post_request_data['total_count_value'] : '0';
            $invoice_coustom_field = array(
                'client_post_id' => (isset($get_post_request_data['client_post_id'])) ? $get_post_request_data['client_post_id'] : '0',
                'total_count_value' => (isset($get_post_request_data['total_count_value'])) ? $get_post_request_data['total_count_value'] : '0',
                'subtotal' => preg_replace("/[^\s]+\s/", "", (isset($get_post_request_data['subtotal'])) ? $get_post_request_data['subtotal'] : '0.00'),
                'create_invoice_opration' => (isset($get_post_request_data['create_invoice_opration'])) ? $get_post_request_data['create_invoice_opration'] : '0',
                'invoice_descount' => preg_replace("/[^\s]+\s/", "", (isset($get_post_request_data['invoice_descount'])) ? $get_post_request_data['invoice_descount'] : '0.00' ),
                'balance_with_tax' => preg_replace("/[^\s]+\s/", "", (isset($get_post_request_data['balance_with_tax'])) ? $get_post_request_data['balance_with_tax'] : '0.00' ),
                'balance_without_tax' => preg_replace("/[^\s]+\s/", "", (isset($get_post_request_data['balance_without_tax'])) ? $get_post_request_data['balance_without_tax'] : '0.00' ),
                'total_pay' => preg_replace("/[^\s]+\s/", "", (isset($get_post_request_data['total_pay'])) ? $get_post_request_data['total_pay'] : '0.00'),
                'total_tax' => preg_replace("/[^\s]+\s/", "", (isset($get_post_request_data['total_tax'])) ? $get_post_request_data['total_tax'] : '0.00')
            );
            $invoice_item_coustom_field_merge = array_merge($invoice_item_coustom_field_merge, $invoice_coustom_field);
            for ($i = 1; $i <= $count_no_of_invoice_item; $i++) {
                if (strlen($get_post_request_data['item_name' . $i]) > 0) {
                    $invoice_item_data['item_name' . $i] = $get_post_request_data['item_name' . $i];
                    $invoice_item_data['item_description' . $i] = $get_post_request_data['item_description' . $i];
                    $invoice_item_data['datepicker' . $i] = ($get_post_request_data['datepicker' . $i]) ? $get_post_request_data['datepicker' . $i] : '';
                    $invoice_item_data['item_cost' . $i] = $get_post_request_data['item_cost' . $i];
                    $invoice_item_data['item_qty' . $i] = $get_post_request_data['item_qty' . $i];
                    $invoice_item_data['tax_name' . $i] = ''; //$invoice_tax_name;
                    $invoice_item_data['tax_rate' . $i] = ''; //$invoice_tax_rate;
                    $invoice_item_data['item_price' . $i] = preg_replace("/[^\s]+\s/", "", $get_post_request_data['item_price' . $i]);
                    $invoice_item_data['item_tax' . $i] = preg_replace("/[^\s]+\s/", "", $get_post_request_data['item_tax' . $i]);
                    $invoice_item_coustom_field_merge = array_merge($invoice_item_coustom_field_merge, $invoice_item_data);
                }
            }
            foreach ($get_post_request_data as $key => $value) {
                if (array_key_exists($key, $invoice_item_coustom_field_merge)) {
                    if ('create_invoice_opration' == $key) {
                        if ('7' == $value) {
                            $invoice_item_details_array_result = get_post_meta($postID, 'invoice_item_details');
                            $value = $invoice_item_details_array_result[0]['create_invoice_opration'];
                        }
                    }
                    $invoice_post_request_data_final[$key] = $value;
                }
            }
            update_post_meta($postID, 'invoice_item_details', $invoice_post_request_data_final);
            $paypal_invoice_manager_obj = new PayPal_Invoice_Manager();
            if (isset($get_post_request_data['create_invoice_opration']) && $get_post_request_data['create_invoice_opration'] != 0) {
                if (isset($get_post_request_data['client_post_id']) && $get_post_request_data['client_post_id'] != '0') {
                    if ('1' == $get_post_request_data['create_invoice_opration']) {
                        $paypal_responce = $paypal_invoice_manager_obj->App_CreateInvoice($post);
                        $this->Get_Responce_Update_Post_Meta($postID, $paypal_responce, $get_post_request_data['create_invoice_opration']);
                    }
                    if ('2' == $get_post_request_data['create_invoice_opration']) {
                        $paypal_responce = $paypal_invoice_manager_obj->App_CreateAndSendInvoice($post);
                        $this->Get_Responce_Update_Post_Meta($postID, $paypal_responce, $get_post_request_data['create_invoice_opration']);
                    }
                    if ('3' == $get_post_request_data['create_invoice_opration']) {
                        $paypal_responce = $paypal_invoice_manager_obj->App_CancelInvoice($post);
                        $this->Get_Responce_Update_Post_Meta($postID, $paypal_responce, $get_post_request_data['create_invoice_opration']);
                    }
                    if ('6' == $get_post_request_data['create_invoice_opration']) {
                        $paypal_responce = $paypal_invoice_manager_obj->App_SendInvoice($post);
                        $this->Get_Responce_Update_Post_Meta($postID, $paypal_responce, $get_post_request_data['create_invoice_opration']);
                    }
                    if ('7' == $get_post_request_data['create_invoice_opration']) {
                        $paypal_responce = $paypal_invoice_manager_obj->App_UpdateInvoice($post);
                        $this->Get_Responce_Update_Post_Meta($postID, $paypal_responce, $get_post_request_data['create_invoice_opration']);
                    }
                    if ('8' == $get_post_request_data['create_invoice_opration']) {
                        $paypal_responce = $paypal_invoice_manager_obj->App_DeleteInvoice($post);
                        $this->Get_Responce_Update_Post_Meta($postID, $paypal_responce, $get_post_request_data['create_invoice_opration']);
                    }
                } else {
                    set_transient('paypal_invoice_error', 'Please Select a Client', MINUTE_IN_SECONDS);
                }
            } elseif (isset($get_post_request_data['create_invoice_opration']) && $get_post_request_data['create_invoice_opration'] == 0) {
                set_transient('paypal_invoice_error', 'No Select Invoice Methods Only Create Your Invoice In Database', MINUTE_IN_SECONDS);
            }
        } else {
            if (isset($get_post_request_data['create_invoice_opration'])) {
                set_transient('paypal_invoice_error', 'Please Select a Client', MINUTE_IN_SECONDS);
                return;
            }
        }
    }

    public function Get_Responce_Update_Post_Meta($postID, $paypal_responce, $invoice_opration_id) {
        if ('7' == $invoice_opration_id) {
            $invoice_item_details_array_result = get_post_meta($postID, 'invoice_item_details');
            $invoice_opration_id = $invoice_item_details_array_result[0]['create_invoice_opration'];
        }
        if ('Success' == $paypal_responce['Ack']) {
            foreach ($paypal_responce as $key => $value) {
                if ('Ack' == $key && 'Success' == $value) {
                    if ('8' == $invoice_opration_id) {
                        update_post_meta($postID, 'InvoiceID', '');
                    } else {
                        update_post_meta($postID, 'opration_id', $invoice_opration_id);
                    }
                }
                if ('InvoiceID' == $key && strlen($value) > 0) {
                    update_post_meta($postID, 'InvoiceID', $value);
                }
            }
        } else {
            set_transient('paypal_invoice_error', $paypal_responce['Errors'][0]['Message'], MINUTE_IN_SECONDS);
        }
    }

    public function save_post_client_details($postID, $post, $update) {
        if (isset($post->post_type) && $post->post_type == 'clients') {
            $post_request_data_final_array = array();
            $billing_address_array = array();
            $shipping_address_array = array();
            $shipping_billing_address_array = array();
            $post_request_data = $_REQUEST;
            $billing_address_array = array('billing_first_name',
                'billing_last_name',
                'billing_businessname',
                'payer_email',
                'billing_phone',
                'billing_country',
                'billing_address_1',
                'billing_address_2',
                'billing_city',
                'billing_state',
                'billing_postcode',
                'billing_fax',
                'billing_website',
                'billing_custom');
            $shipping_billing_address_array = array_merge($shipping_billing_address_array, $billing_address_array);
            if (isset($post_request_data['invoice_ship_to_different_address']) && $post_request_data['invoice_ship_to_different_address']) {
                $shipping_address_array = array('shipping_first_name',
                    'shipping_last_name',
                    'shipping_businessname',
                    'shipping_phone',
                    'shipping_website',
                    'shipping_country',
                    'shipping_address_1',
                    'shipping_address_2',
                    'shipping_city',
                    'shipping_state',
                    'shipping_postcode',
                    'shipping_fax',
                    'shipping_custom',
                    'invoice_ship_to_different_address');
                $shipping_billing_address_array = array_merge($shipping_billing_address_array, $shipping_address_array);
            }
            foreach ($post_request_data as $post_request_data_key => $post_request_data_value) {
                if (in_array($post_request_data_key, $shipping_billing_address_array)) {
                    $post_request_data_final_array[$post_request_data_key] = $post_request_data_value;
                }
            }
            update_post_meta($postID, 'invoice_client_details', $post_request_data_final_array);
        }
    }

    public function show_admin_notice() {
        if (get_transient('paypal_invoice_error')) {
            echo ' <div class="error notice notice-error is-dismissible"><p>' . get_transient('paypal_invoice_error') . '</p></div>';
        }
        delete_transient('paypal_invoice_error');
    }

    public function set_custom_edit_invoice_columns($columns) {
        unset($columns['date']);
        $columns['invoice_payer_email'] = __('Payer Email', 'paypal-invoicing');
        $columns['invoice_total'] = __('Total Pay', 'paypal-invoicing');
        $columns['invoice_status'] = __('Status', 'paypal-invoicing');
        $columns['date'] = __('Date', 'paypal-invoicing');
        return $columns;
    }

    public function custom_invoice_columns($column, $post_id) {
        $invoice_item_details_postmeta = array();
        $invoice_item_details_postmeta = get_post_meta($post_id, 'invoice_item_details');
        if (is_array($invoice_item_details_postmeta)) {
            switch ($column) {
                case 'invoice_payer_email' :
                    if (isset($invoice_item_details_postmeta[0]['client_post_id']) && !empty($invoice_item_details_postmeta[0]['client_post_id'])) {
                        $client_post_id = $invoice_item_details_postmeta[0]['client_post_id'];
                        $invoice_custom_column_array_client_detail = get_post_meta($client_post_id, 'invoice_client_details');
                    }
                    if (empty($invoice_custom_column_array_client_detail[0]['payer_email']))
                        echo __('Unknown');
                    else
                        echo $invoice_custom_column_array_client_detail[0]['payer_email'];
                    break;
                case 'invoice_total' :
                    if (empty($invoice_item_details_postmeta[0]['total_pay']))
                        echo __('Unknown');
                    else
                        echo $invoice_item_details_postmeta[0]['total_pay'];
                    break;
                case 'invoice_status' :
                    if (!isset($invoice_item_details_postmeta[0]['create_invoice_opration']) && empty($invoice_item_details_postmeta[0]['create_invoice_opration'])) {
                        echo "No Status";
                        break;
                    } elseif ('0' == $invoice_item_details_postmeta[0]['create_invoice_opration']) {
                        echo "No Status";
                    } elseif ('1' == $invoice_item_details_postmeta[0]['create_invoice_opration']) {
                        echo "Draft";
                    } elseif ('2' == $invoice_item_details_postmeta[0]['create_invoice_opration']) {
                        echo "Sent";
                    } elseif ('3' == $invoice_item_details_postmeta[0]['create_invoice_opration']) {
                        echo "Canceled";
                    } elseif ('4' == $invoice_item_details_postmeta[0]['create_invoice_opration']) {
                        echo "Mark As Paid";
                    } elseif ('5' == $invoice_item_details_postmeta[0]['create_invoice_opration']) {
                        echo "Paid";
                    } elseif ('6' == $invoice_item_details_postmeta[0]['create_invoice_opration']) {
                        echo "Sent";
                    } elseif ('7' == $invoice_item_details_postmeta[0]['create_invoice_opration']) {
                        echo "Sent";
                    } elseif ('8' == $invoice_item_details_postmeta[0]['create_invoice_opration']) {
                        echo "Deleted";
                    }
                    break;
                default :
                    break;
            }
        }
    }

    public function bs_invoice_table_sorting($columns) {
        $columns['invoice_payer_email'] = 'invoice_payer_email';
        $columns['invoice_total'] = 'invoice_total';
        $columns['invoice_status'] = 'invoice_status';
        return $columns;
    }

    public function set_custom_edit_clients_columns($columns) {
        unset($columns['date']);
        $columns['client_full_name'] = __('Client Name', 'paypal-invoicing');
        $columns['client_business_name'] = __('Business Name', 'paypal-invoicing');
        $columns['client_country'] = __('Country / State', 'paypal-invoicing');
        $columns['client_phone'] = __('Phone', 'paypal-invoicing');
        $columns['date'] = __('Date', 'paypal-invoicing');
        return $columns;
    }

    public function custom_clients_columns($column, $post_id) {
        $invoice_client_details = get_post_meta($post_id, 'invoice_client_details');
        if (is_array($invoice_client_details)) {
            switch ($column) {
                case 'client_full_name' :
                    if ((isset($invoice_client_details[0]['billing_first_name']) && !empty($invoice_client_details[0]['billing_first_name'])) && (isset($invoice_client_details[0]['billing_last_name']) && !empty($invoice_client_details[0]['billing_last_name']))) {
                        echo $invoice_client_details[0]['billing_first_name'] . ' ' . $invoice_client_details[0]['billing_last_name'];
                    } elseif (isset($invoice_client_details[0]['billing_first_name']) && !empty($invoice_client_details[0]['billing_first_name'])) {
                        echo $invoice_client_details[0]['billing_first_name'] . ' ' . $invoice_client_details[0]['billing_last_name'];
                    } elseif (isset($invoice_client_details[0]['billing_last_name']) && !empty($invoice_client_details[0]['billing_last_name'])) {
                        echo $invoice_client_details[0]['billing_last_name'] . ' ' . $invoice_client_details[0]['billing_last_name'];
                    } else {
                        echo __('Unknown');
                    }
                    break;
                case 'client_business_name' :
                    if (isset($invoice_client_details[0]['billing_businessname']) && !empty($invoice_client_details[0]['billing_businessname'])) {
                        echo $invoice_client_details[0]['billing_businessname'];
                    } else {
                        echo __('Unknown');
                    }
                    break;
                case 'client_country' :

                    if ((isset($invoice_client_details[0]['billing_country']) && !empty($invoice_client_details[0]['billing_country'])) && (isset($invoice_client_details[0]['billing_state']) && !empty($invoice_client_details[0]['billing_state']))) {
                        echo $invoice_client_details[0]['billing_country'] . ' / ' . $invoice_client_details[0]['billing_state'];
                    } elseif (isset($invoice_client_details[0]['billing_country']) && !empty($invoice_client_details[0]['billing_country'])) {
                        echo $invoice_client_details[0]['billing_country'];
                    } elseif (isset($invoice_client_details[0]['billing_state']) && !empty($invoice_client_details[0]['billing_state'])) {
                        echo $invoice_client_details[0]['billing_state'];
                    } else {
                        echo __('Unknown');
                    }
                    break;
                case 'client_phone' :
                    if (isset($invoice_client_details[0]['billing_phone']) && !empty($invoice_client_details[0]['billing_phone'])) {
                        echo $invoice_client_details[0]['billing_phone'];
                    } else {
                        echo __('Unknown');
                    }
                    break;

                default :
                    break;
            }
        }
    }

    public function bs_clients_table_sorting($columns) {
        $columns['client_full_name'] = 'client_full_name';
        $columns['client_business_name'] = 'client_business_name';
        $columns['client_country'] = 'client_country';
        $columns['client_phone'] = 'client_phone';
        return $columns;
    }

    public function wp_insert_post_data_own($data, $postarr) {
        if (isset($data) && $data['post_type'] == 'invoice' && ($data['post_status'] == 'publish' || $data['post_status'] == 'draft' || $data['post_status'] == 'auto-draft')) {
            if (isset($data) && !empty($data)) {
                if (isset($data['post_title']) && empty($data['post_title'])) {
                    $my_error = new WP_Error('post_type_length_invalid', __('Post type names must be between 1 and 20 characters in length.'));
                    $my_error->add('post_type_length_invalid', 'Post type names must be between 1 and 20 characters in length.');
                } elseif (isset($_REQUEST['client_post_id']) && $_REQUEST['client_post_id'] == '0') {
                    $my_error = new WP_Error('require_client_selection', __('Could not save invoice without client selection', ''));
                    $my_error->add('require_client_selection', 'Could not save invoice without client selection.');
                }
            }
            if ($data['post_status'] == 'draft' || $data['post_status'] == 'auto-draft') {
                return $data;
            }
        } else {
            return $data;
        }
    }

    public static function paypal_invoice_client_list_array() {
        global $wpdb;
        $post_data = $wpdb->get_results($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_status = %s AND post_type = %s ", 'publish', 'clients'), ARRAY_A);
        if (empty($post_data)) {
            return false;
        } else {
            return $post_data;
        }
    }

    public static function get_tax_list_dropdown($selected_tax = null) {
        $paypal_invoice_tax_enable = get_option('paypal_invoice_tax_enable');
        if ('on' == $paypal_invoice_tax_enable) {
            $invoice_tax_array = get_option('paypal_invoice_tax_setting');
            foreach ($invoice_tax_array as $key => $value) {
                $invoice_selected = '';
                if ('on' == $value['taxactive_inactive_checkbox']) {
                    if ($value['paypalinvoice_tax_name'] == $selected_tax) {
                        $invoice_selected = 'selected';
                    }
                    $invoice_tax_option .= '<option value=' . $value['paypalinvoice_tax_rate'] . '-' . $value['paypalinvoice_tax_name'] . ' ' . $invoice_selected . ' >' . $value['paypalinvoice_tax_name'] . '</option>';
                }
            }
            if (empty($invoice_tax_option)) {
                $invoice_tax_option = '<option>Select Tax</option>';
            }
            return $invoice_tax_option;
        }
    }

}
