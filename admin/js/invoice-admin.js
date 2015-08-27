(function($) {
    'use strict';
    $(function() {
        if ($('.date_display').length > 0) {
            $('.date_display').datepicker();
        }  
        var count_client = $('#client_invoice_dropdown option').length;
        if( count_client > 1) {
            $('.select_client_label').show();
            $('.client_invoice_dropdown').show();
            $('.label_client_no_found').hide();            
            var client_id = $( "#client_invoice_dropdown option:selected" ).val();
            var admin_url = $('#client_view').attr('href'); 
            admin_url = admin_url.toString().replace(/post=\d+/g,"post="+client_id);
            $('#client_view').attr('href',admin_url);
            $('#client_view').show();
            if('0' == $( "#client_invoice_dropdown option:selected" ).val()) {
                $('#client_view').hide();
            }
        } else {
            $('.select_client_label').hide();
            $('.client_invoice_dropdown').hide();
            $('#client_view').hide();
            $('.label_client_no_found').show();
        }
        $('#client_invoice_dropdown').on('change',function(){
           var client_id = $( "#client_invoice_dropdown option:selected" ).val();           
           var admin_url = $('#client_view').attr('href');    
           admin_url = admin_url.toString().replace(/post=\d+/g,"post="+client_id);           
            $('#client_view').attr('href',admin_url);
            $('#client_view').show();
           if('0' == client_id) {
               $('#client_view').hide();
           }         
        });
        
        $(".paypal_invoice_tax_add_new .add_field_button").click(function(e) { //on add input button click
            e.preventDefault();
            var tax_last_tr_id = $('#paypal_invoice_tax_section tr:last').attr('id');
            tax_last_tr_id = parseInt(tax_last_tr_id) + 1;            
            var rowhtml = "<tr id=" + tax_last_tr_id + "><td colspan='1'><input type='text' name='paypalinvoice_tax_name_" + tax_last_tr_id + "_[]' class='medium-text' placeholder='Tax Name'></td><td><input type='text' name='paypalinvoice_tax_rate_" + tax_last_tr_id + "_[]' class='medium-text' placeholder='Tax Rate (%)'></td><td class='center'><input name='taxactive_inactive_checkbox_" + tax_last_tr_id + "_[]' type='checkbox' id='switch_checkbox'></td><td><a class='delete' title='Remove PayPal Invoice Tax'>Delete</a></td></tr>";
            $("#paypal_invoice_tax_section tbody").append(rowhtml); //add input box

        });
        
        $("#paypal_invoice_tax_section td a.delete").live("click", function() {
            if (!confirm("Do you want to delete?")) {
                return false;
            } else {                
                if (typeof paypal_invoice_tax_params === 'undefined') {
                    return false;
                }
                $(this).closest("tr").remove();
                var data = {
                    action: 'payment_invoice_tax_name_rate_remove',
                    security: paypal_invoice_tax_params.paypal_invoice_tax,
                    value: $("#paypal_invoice_tax_form").serializeArray()
                };

                $.post(paypal_invoice_tax_params.ajax_url, data, function(response) {

                });
            }
        });
        
    });
})(jQuery);