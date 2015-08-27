var last_tr_id = "";
function print_today() {
    var now = new Date();
    var months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    var date = ((now.getDate() < 10) ? "0" : "") + now.getDate();
    function fourdigits(number) {
        return (number < 1000) ? number + 1900 : number;
    }
    var today = months[now.getMonth()] + " " + date + ", " + (fourdigits(now.getYear()));
    return today;
}

function roundNumber(number, decimals) {
    var newString;
    decimals = Number(decimals);
    if (decimals < 1) {
        newString = (Math.round(number)).toString();
    } else {
        var numString = number.toString();
        if (numString.lastIndexOf(".") == -1) {
            numString += ".";
        }
        var cutoff = numString.lastIndexOf(".") + decimals;
        var d1 = Number(numString.substring(cutoff, cutoff + 1));
        var d2 = Number(numString.substring(cutoff + 1, cutoff + 2));
        if (d2 >= 5) {
            if (d1 == 9 && cutoff > 0) {
                while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
                    if (d1 != ".") {
                        cutoff -= 1;
                        d1 = Number(numString.substring(cutoff, cutoff + 1));
                    } else {
                        cutoff -= 1;
                    }
                }
            }
            d1 += 1;
        }
        if (d1 == 10) {
            numString = numString.substring(0, numString.lastIndexOf("."));
            var roundedNum = Number(numString) + 1;
            newString = roundedNum.toString() + '.';
        } else {
            newString = numString.substring(0, cutoff) + d1.toString();
        }
    }
    if (newString.lastIndexOf(".") == -1) {
        newString += ".";
    }
    var decs = (newString.substring(newString.lastIndexOf(".") + 1)).length;
    for (var i = 0; i < decimals - decs; i++)
        newString += "0";
    return newString;
}
(function ($) {
    function get_current_class_name() {
        $(".cost, .qty, .tax_rate, .invoice_descount").keydown(function (e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 || (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) || (e.keyCode >= 35 && e.keyCode <= 40)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    }
    function count_total_table_row() {
        if (last_tr_id.toString().length == 0 || last_tr_id == '') {
            last_tr_id = parseInt($('#total_count_value').val());
        }
    }
    function update_balance() {
        var invoice_currency_saymbol = $('#invoice_currency_saymbol').val();
        var due = roundNumber($("#subtotal").val().replace(invoice_currency_saymbol, ""), 2);
        $('#due_balance').attr('value',invoice_currency_saymbol + ' '+ parseFloat(due).toFixed(2));
    }
    function update_total() {
        var total = 0;
        var tax_include = 0;
        var invoice_currency_saymbol = $('#invoice_currency_saymbol').val();
        if (typeof invoice_currency_saymbol === 'undefined') {
                return false;
        }
        var tax = $('#total_tax').val().replace(invoice_currency_saymbol, "");
        $('.price').each(function (i) {
            price = $(this).attr('value').replace(invoice_currency_saymbol, "");
            if (price.toString().length > 0) {
                total += parseInt(price);
            }
        });
        if (isNaN(tax) || tax.toString().length == 0) {
            tax = 0;
        }
        total = roundNumber(total, 2);
        tax_include = parseFloat(tax) + parseFloat(total);
        $('#subtotal').attr('value', '');
        $('#total').attr('value', '');
        $('#invoice_descount').attr('value', '');
        $('#subtotal').attr('value', invoice_currency_saymbol + ' ' + parseFloat(total).toFixed(2));
        $('#total').attr('value',invoice_currency_saymbol + ' ' + parseFloat(tax_include).toFixed(2));
        $('#total_pay').attr('value', invoice_currency_saymbol + ' ' + parseFloat(tax_include).toFixed(2));
        $('#invoice_descount').attr('value', invoice_currency_saymbol + ' 0.00');
        update_balance();
    }
    function update_new_total() {
        var tax = 0;
        var invoice_currency_saymbol = $('#invoice_currency_saymbol').val();
        if (typeof invoice_currency_saymbol === 'undefined') {
                return false;
        }
        var i = 1;
        if (last_tr_id > 0) {
            for (i = 1; i <= last_tr_id; i++) {
                if ($('#item_tax' + i).is('[id]')) {
                    tax += parseFloat($('#item_tax' + i).val().replace(invoice_currency_saymbol, ""));
                }
            }
        } else {
            tax = parseFloat($('#item_tax1').val().replace(invoice_currency_saymbol, ""));
        }
        if (isNaN(tax)) {
            tax = '0';
        }
        $('#total_tax').attr("value", invoice_currency_saymbol + ' ' + parseFloat(tax).toFixed(2));
        update_total();
    }
    function update_total_with_tax() {
        var row = $(this).parents('.item-row');
        var invoice_currency_saymbol = $('#invoice_currency_saymbol').val();
        if (typeof invoice_currency_saymbol === 'undefined') {
                return false;
        }
        var invoice_tax_detail = row.find('.tax_rate').val().replace(invoice_currency_saymbol, "");
        var tax_rate = invoice_tax_detail.match(/[0-9.]+/g);        
        //var tax_rate = row.find('.tax_rate').val().replace(invoice_currency_saymbol, "");
        if (isNaN(tax_rate)) {
            tax_rate = 0;
        }
        
        tax_rate = parseFloat(tax_rate) / 100;
        var item_price = row.find('.price').val().replace(invoice_currency_saymbol, "");
        var count_tax = parseFloat(tax_rate) * parseFloat(item_price);
        row.find('.item_tax').attr("value", invoice_currency_saymbol + ' ' + parseFloat(count_tax).toFixed(2));
        update_new_total();
    }
    function update_total_with_tax_onchange(row) {
        var invoice_currency_saymbol = $('#invoice_currency_saymbol').val();
        //var tax_rate = parseFloat(row.find(".tax_rate").val().replace(invoice_currency_saymbol, ""));
        var invoice_tax_detail = row.find('.tax_rate').val().replace(invoice_currency_saymbol, "");
        var tax_rate = invoice_tax_detail.match(/[0-9.]+/g);
        
        if (isNaN(tax_rate)) {
            tax_rate = 0;
        }
        tax_rate = parseFloat(tax_rate) / 100;
        var item_price = row.find('.price').val().replace(invoice_currency_saymbol, "");
        var count_tax = parseFloat(tax_rate) * parseFloat(item_price);
        row.find('.item_tax').attr("value", invoice_currency_saymbol + ' ' + parseFloat(count_tax).toFixed(2));
        update_new_total();
    }
    function update_total_pay() {
        var invoice_currency_saymbol = $('#invoice_currency_saymbol').val();
        var due = $("#total").val().replace(invoice_currency_saymbol, "") - $("#paid").val().replace(invoice_currency_saymbol, "");
        due = roundNumber(due, 2);
        $('#total_pay').attr('value', invoice_currency_saymbol + ' ' + parseFloat(due).toFixed(2));
    }
    function update_balance_pay() {
        var invoice_currency_saymbol = $('#invoice_currency_saymbol').val();
        var due = $("#total").val().replace(invoice_currency_saymbol, "") - $("#invoice_descount").val().replace(invoice_currency_saymbol, "");
        due = roundNumber(due, 2);
        $('#total_pay').attr('value', invoice_currency_saymbol + ' ' + parseFloat(due).toFixed(2));
    }
    function update_price() {
        var row = $(this).parents('.item-row');
        var invoice_currency_saymbol = $('#invoice_currency_saymbol').val();
        var price = row.find('.cost').val().replace(invoice_currency_saymbol, "") * row.find('.qty').val();

        price = roundNumber(price, 2);
        isNaN(price) ? row.find('.price').html("N/A") : row.find('.price').attr("value", invoice_currency_saymbol + ' ' + parseFloat(price).toFixed(2));
        update_total();
        update_total_with_tax_onchange(row);
    }
    function bind() {
        get_current_class_name();
        $(".cost").blur(update_price);
        $(".qty").blur(update_price);
        $(".tax_rate").blur(update_total_with_tax);
        $(".invoice_descount").blur(update_balance_pay);
        count_total_table_row();
    }
    $('input').click(function () {
        $(this).select();
    });
    $("#paid").blur(update_total_pay);
    //$("#invoice_descount").blur(update_balance_pay);
    $("#addrow").click(function () {
        
        $('#paypal_invoice_item > tbody > tr.item-row').each(function () {
            last_tr_id = parseInt(1) + parseInt(this.id);
            $('#total_count_value').attr('value', "");
            $('#total_count_value').attr('value', last_tr_id);
        });
         
        
        var invoice_tax_options = '';
        if ( 'on' == $("#paypal_invoice_tax_enable_disable").val()) {
             invoice_tax_options = $("#text_rate > option").clone();
             var add_invoice_tax_option = "";
             add_invoice_tax_option = '<td><select id="tax_rate"  name="tax_rate' + last_tr_id + '" class="tax_rate" style="width: 100px"></select></td>';
        }
        
         $('#paypal_invoice_item').append('<tr id="' + last_tr_id + '" class="item-row"><td><input type="text" value="" name="item_name' + last_tr_id + '" placeholder="Item Name" id="Item"></td><td><input type="text" value="" class="cost textarea" name="item_cost' + last_tr_id + '" id="Quantity"></td><td><input type="text" value="" class="qty textarea" name="item_qty' + last_tr_id + '" id="UnitCost"></td>'+ add_invoice_tax_option +'<td><input type="text" readonly="readonly" value="0.00" name="item_price' + last_tr_id + '" class="price input_box" id="Price"><input type="text" class ="item_tax" id="item_tax' + last_tr_id + '" name="item_tax' + last_tr_id + '" value="0" hidden></td><td><a class="delete button button-small" title="Add New Invoice Item" id="delete">X</a></td></tr><tr id="item_details' + last_tr_id + '"><td colspan="6"><textarea placeholder="Description (optional) " name="item_description' + last_tr_id + '" id="Item_details" rows="1" cols="82"></textarea></td></tr>');
         $('select[name="tax_rate' + last_tr_id + '"]').append(invoice_tax_options);       
        if ($(".delete").length > 0) $(".delete").show();
        bind();
        if ($('#datepicker1').is('[id]')) {
            $('.date_display').datepicker();
        }
    });
    bind();
    $(".delete").live('click', function () {
        var item_details_id = $(this).parents('.item-row').attr('id');
        $(this).parents('.item-row').remove();
        $("#item_details" + item_details_id).remove();
        if ($(".delete").length < 1) $(".delete").hide();
        update_new_total();
        update_total();
    });    
    $('.same_address').click(function () {
        if ($(this).prop("checked") == true) {
            $('.same_address_tbody').show(500);
            $('.diffrent_shiping_address').attr('value', 'yes');

        }
        else if ($(this).prop("checked") == false) {
            $('.same_address_tbody').hide(500);
            $('.diffrent_shiping_address').attr('value', 'no');
        }
    });
}(jQuery))