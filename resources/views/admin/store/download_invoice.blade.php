<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- <link href="{{ URL::asset('assets/css/main.css?v=1.1') }}" rel="stylesheet" type="text/css" /> -->
        <style>
            .invoice-container {    color: #ffffff;}
            a:focus, a, a:hover {
                outline: none;
                text-decoration: none;
                color: #ffffff;
                cursor: pointer;
            }
            h2 {
                font-size: 24px;
            }
            h1 {
                font-size: 26px;
            }
            .b-text {
                color: #12151C;
            }
            .second-color {
                color: #888888;
            }
            .inter-400 {
                font-weight: 400;
            }
            .inter-700 {
                font-weight: 700;
            }
            .inter-500 {
                font-weight: 500;
            }
            .invoice-container {
                max-width: 880px;
                margin: 0 auto;
                padding: 30px 15px;
            }
            .content-min-width{
                background: #12151C;
                border-radius: 20px 20px 0 0;
                padding:50px 50px 30px;
            }
            .logo img {
                height: 60px;
                width: auto;
            }
            .invoice-logo-content {
                display: flex;
                flex-wrap: nowrap;
                align-content: center;
                justify-content: space-between;
                align-items: center;
            }
            .invo-head-wrap {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .invoice-content-wrap {
                background: #fff;
                position: relative;
                height: 100%;
                width: 100%;
                box-shadow:0px 2px 8px #ccc;
                border-radius:20px 20px 0px 0px;
            }
            .invo-num-title {
                width: 60%;
            }
            .invo-no {
                width: 50%;
                font-size: 16px;
                line-height: 24px;
            }
            .invo-num {
                color: #888888;
                padding-left: 20px;
                font-size: 16px;
                line-height: 24px;
            }
            .invo-head-wrap.invoi-date-wrap {
                margin-top: 20px;
            }
            .invo-cont-wrap {
                display: flex;
                flex-wrap: nowrap;
                align-items: center;
            }
            .invoice-header-contact {
                display: inline-flex;
                padding-top: 36px;
            }
            .invo-cont-wrap.invo-contact-wrap {
                margin-right: 30px;
            }
            .invo-social-name {
                padding-left: 10px;
            }
            
            /************************* 3.Invoice Content CSS **************************/

            .container {
                max-width: 850px;
                margin: 0 auto;
                padding: 0 50px;
            }
            .invo-to {
                color: #12151C;
            }
            .invo-to-owner {
                color: #00BAFF;
                margin: 10px 0;
            }
            .invoice-owner-conte-wrap {
                display: flex;
                justify-content: space-between;
                -webkit-box-pack: justify;
            }
            .invo-to-wrap {
                text-align: left;
            }
            .invo-pay-to-wrap {
                text-align: right;
            }
            .invo-owner-address {
                color: #888888;
            }
            .table-wrapper {
                padding: 50px 0 20px;
            }
            .invoice-table {
                border-collapse: collapse;
                width: 100%;
                max-width: 750px;
                margin: 0 auto;
                white-space: nowrap;
                background-color: #ffffff;
            }
            .invoice-table td, .invoice-table th {
                text-align: left;
            }
            .invoice-table td {
                color: #888888;
                font-size: 14px;
                line-height: 22px;
                padding: 20px 0;
            }
            .invoice-table thead th {
                color: #12151C;
                padding: 10px 0;
            }
            .invo-tb-body .invo-tb-row {
                border-bottom: 1px solid #888888;
            }
            .invo-tb-body .invo-tb-row:last-child {
                border-bottom: none;
            }
            .invo-tb-body {
                border-bottom: 2px solid #12151C;
                border-top: 2px solid #12151C;
            }
            section {
                padding: 50px 0;
                position: relative;
                width: 100%;
                height: 100%;
            }
            .medium-font {
                font-size: 16px;
                line-height: 24px;
            }
            .serv-wid {
                width: 32%;
            }
            .desc-wid {
                width: 36%;
            }
            .qty-wid {
                width: 10%;
            }
            .pric-wid {
                width: 12%;
            }
            .tota-wid {
                width: 10%;
            }
            .invoice-table th.total-head, .invoice-table td.total-data {
                text-align: right;
            }
            .addi-info-title {
                color: #12151C;
                margin: 0 0 10px;
            }
            .add-info-desc {
                font-size: 14px;
                line-height: 22px;
                color: #888888;
            }
            .invo-total-price {
                font-weight: 500;
            }
            .invo-addition-wrap {
                display: flex;
            }
            .invo-add-info-content {
                width: 50%;
            }
            .invo-bill-total {
                width: 50%;
                position: relative;
            }
            .invo-bill-total table {
                width: 85%;
                border-collapse: collapse;
                white-space: nowrap;
                float: right;
            }
            .invo-total-table td.invo-total-data, .invo-total-table td.invo-total-price{
                text-align: right;
            }
            .tax-row.bottom-border, .disc-row.bottom-border {
                border-bottom: 2px solid #12151C;
            }
            .hotel-sub {
                padding-left: 70px!important;
            }
            .invo-total-table .tax-row td, .invo-grand-total td {
                padding: 20px 0;
            }

            /************************* 4.Bottom Content CSS **************************/
            .agency-bottom-content {
                background: #12151c;
                border-radius: 0px 0px 20px 20px;
                padding: 50px 0;
            }
            .invo-btns {
                display: inline-flex;
                align-items: center;
                margin: 0 1px;
            }
            .invo-buttons-wrap {
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .invo-buttons-wrap .invo-btns .print-btn {
                background: #00BAFF;
                padding: 12px 24px;
                border-radius: 24px 0px 0px 24px;
                display: -webkit-inline-box;
                display: -ms-inline-flexbox;
                display: inline-flex;
                -webkit-box-align: center;
                -ms-flex-align: center;
            }   
            .invo-buttons-wrap .invo-btns .download-btn {
                background: #00D061;
                padding: 12px 24px;
                border-radius: 0px 24px 24px 0px;
                display: -webkit-inline-box;
                display: -ms-inline-flexbox;
                display: inline-flex;
                -webkit-box-align: center;
                -ms-flex-align: center;
            }
            .invo-btns span {
                padding-left: 10px;
            }
            .invo-note-wrap, .note-title {
                display: flex;
                align-items: center;
            }
            .invo-note-wrap {
                padding-top: 30px;
            }
            .note-title span, .note-desc {
                padding-left: 10px;
            }
            .invoice-header.back-img-invoice {
                content: '';
                position: relative;
                background-image: url('assets/imgs/back-img-one.png');
                width: 100%;
                height: auto;
                top: 0;
                bottom: auto;
                left: 0;
                right: 0;
                background-repeat: no-repeat;
                background-position: center center;
                background-size: cover;
            }
            .invoice-header.back-img-invoice:before, .invoice-header.stadium-header:before {
                content: '';
                position: absolute;
                background-color: #12151C;
                opacity: 0.8;
                width: 100%;
                height: 100%;
                left: 0;
                right: 0;
                top: 0;
                border-radius: 20px 20px 0px 0px;
            }
            .back-img-invoice .invoice-logo-content, .back-img-invoice .invoice-header-contact{
                position: relative;
                z-index: 8;
            }
            .sno-wid  {
                width: 14%;
            }
            .re-desc-wid {
                width: 22%;
            }
            .re-price-wid {
                width: 14%;
            }
            .re-qty-wid {
                width: 15%;
            }
            .discount-price {
                color: #00D061;
            }
            .disc-row td {
                padding-bottom: 20px;
            }
            .payment-wrap {
                border: 2px solid #12151C;
                padding: 0px 20px 0px 20px;
                display: inline-block;
            }
            .res-pay-table {
                border-collapse: collapse;
            }
            .pay-data {
                border-bottom: 1px solid #888888;
            }
            .pay-type {
                padding: 20px 20px 20px 0px;
            }
            .refund-days {
                padding: 20px 0 20px 0px;
            }
            .res-pay-table tbody .pay-data:last-child {
                border-bottom: none;
            }
            .rest-payment-bill {
                display: flex;
            }
            .sign-img img, .money-img img  {
                width: 100%;
                height: auto;
            }
            .signature-wrap {
                text-align: center;
                align-items: center;
                position: relative;
                left: 19%;
                padding-top: 50px;
            }
            .manager-name {
                font-weight: 500;
                font-size: 14px;	
                line-height: 22px;
            }
            .thank-you-content {
                text-align: center;
                padding-top: 50px;
                font-size: 14px;	
                line-height: 22px;
            }
            .primary-color {
                color: #00BAFF;
            }
            .hotel-sub {
                padding-left: 70px!important;
            }
            .sm-text {
                font-size: 18px;
                line-height: 24px;
            }
        </style>
    </head>
    <body>
        <section class="content-main">
            <div class="invoice-container">
                <div class="invoice-content-wrap" id="download_section">
                    <header class="invoice-header back-img-invoice content-min-width" id="invo_header">
                        <div class="invoice-logo-content">
                            <div class="invoice-logo">
                                <a href="#0" class="logo"><img src="{{!empty($store_admin_details) && !empty($store_admin_details[0]->company_logo ) ? $store_admin_details[0]->company_logo  : '' }}" alt="this is a invoice logo"></a>
                            </div>
                            <div class="invo-head-content">
                                <div class="invo-head-wrap">
                                    <div class="invo-num-title invo-no inter-700">
                                        <div class="invo-social-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_6_94)"><path d="M5 4H9L11 9L8.5 10.5C9.57096 12.6715 11.3285 14.429 13.5 15.5L15 13L20 15V19C20 19.5304 19.7893 20.0391 19.4142 20.4142C19.0391 20.7893 18.5304 21 18 21C14.0993 20.763 10.4202 19.1065 7.65683 16.3432C4.8935 13.5798 3.23705 9.90074 3 6C3 5.46957 3.21071 4.96086 3.58579 4.58579C3.96086 4.21071 4.46957 4 5 4" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15 7C15.5304 7 16.0391 7.21071 16.4142 7.58579C16.7893 7.96086 17 8.46957 17 9" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15 3C16.5913 3 18.1174 3.63214 19.2426 4.75736C20.3679 5.88258 21 7.4087 21 9" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g><defs><clipPath id="clip0_6_94"><rect width="24" height="24" fill="white"></rect></clipPath></defs></svg>
                                        </div>
                                    </div>
                                    <div class="invo-num inter-400">
                                        <a href="tel:{{!empty($store_admin_details) && !empty($store_admin_details[0]->phone_number ) ? $store_admin_details[0]->phone_number  : '' }}" class="invo-hedaer-contact inter-400">{{!empty($store_admin_details) && !empty($store_admin_details[0]->phone_number ) ? $store_admin_details[0]->phone_number  : '' }}</a>
                                    </div>
                                </div>
                                <div class="invo-head-wrap invoi-date-wrap">
                                    <div class="invo-num-title invo-date inter-700">
                                        <div class="invo-social-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_6_108)"><path d="M19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5Z" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M3 7L12 13L21 7" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g><defs><clipPath id="clip0_6_108"><rect width="24" height="24" fill="white"></rect></clipPath></defs></svg>
                                        </div>
                                    </div>
                                    <div class="invo-num inter-400">
                                        <div class="invo-social-name">
                                            <a href="mailto:{{!empty($store_admin_details) && !empty($store_admin_details[0]->email ) ? $store_admin_details[0]->email  : '' }}" class="invo-hedaer-mail inter-400">{{!empty($store_admin_details) && !empty($store_admin_details[0]->email ) ? $store_admin_details[0]->email  : '' }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="invoice-header-contact">
                            <div class="invo-cont-wrap invo-contact-wrap">
                                <div class="invo-social-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_6_94)"><path d="M5 4H9L11 9L8.5 10.5C9.57096 12.6715 11.3285 14.429 13.5 15.5L15 13L20 15V19C20 19.5304 19.7893 20.0391 19.4142 20.4142C19.0391 20.7893 18.5304 21 18 21C14.0993 20.763 10.4202 19.1065 7.65683 16.3432C4.8935 13.5798 3.23705 9.90074 3 6C3 5.46957 3.21071 4.96086 3.58579 4.58579C3.96086 4.21071 4.46957 4 5 4" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15 7C15.5304 7 16.0391 7.21071 16.4142 7.58579C16.7893 7.96086 17 8.46957 17 9" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15 3C16.5913 3 18.1174 3.63214 19.2426 4.75736C20.3679 5.88258 21 7.4087 21 9" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g><defs><clipPath id="clip0_6_94"><rect width="24" height="24" fill="white"></rect></clipPath></defs></svg>
                                </div>
                                <div class="invo-social-name">
                                    <a href="tel:{{!empty($store_admin_details) && !empty($store_admin_details[0]->phone_number ) ? $store_admin_details[0]->phone_number  : '' }}" class="invo-hedaer-contact inter-400">{{!empty($store_admin_details) && !empty($store_admin_details[0]->phone_number ) ? $store_admin_details[0]->phone_number  : '' }}</a>
                                </div>
                            </div>
                            <div class="invo-cont-wrap">
                                <div class="invo-social-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_6_108)"><path d="M19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5Z" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M3 7L12 13L21 7" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g><defs><clipPath id="clip0_6_108"><rect width="24" height="24" fill="white"></rect></clipPath></defs></svg>
                                </div>
                                <div class="invo-social-name">
                                    <a href="mailto:{{!empty($store_admin_details) && !empty($store_admin_details[0]->email ) ? $store_admin_details[0]->email  : '' }}" class="invo-hedaer-mail inter-400">{{!empty($store_admin_details) && !empty($store_admin_details[0]->email ) ? $store_admin_details[0]->email  : '' }}</a>
                                </div>
                            </div>
                        </div> -->
                    </header>
                    <section class="agency-service-content restaurant-invoice-content" id="restaurant_bill">
                        <div class="container">
                            <div class="invoice-owner-conte-wrap">
                                <div class="invo-to-wrap">
                                    <div class="invoice-to-content">
                                        <p class="invo-to inter-700 medium-font mtb-0">Invoice To:</p>
                                        <h1 class="invo-to-owner inter-700 md-lg-font">{{!empty($invoice_details) && !empty($invoice_details[0]->store_name ) ? $invoice_details[0]->store_name  : '' }}</h1>
                                        <p class="invo-owner-address medium-font inter-400 mtb-0">Phone: {{!empty($invoice_details) && !empty($invoice_details[0]->store_phone_number ) ? $invoice_details[0]->store_phone_number  : '' }} <br> Email: {{!empty($invoice_details) && !empty($invoice_details[0]->email ) ? $invoice_details[0]->email  : '' }}</p>
                                    </div>
                                </div>
                                <div class="invo-pay-to-wrap">
                                    <div class="invoice-pay-content">
                                        <p class="invo-to inter-700 medium-font mtb-0">Pay To:</p>
                                        <h2 class="invo-to-owner inter-700 md-lg-font">{{!empty($store_admin_details) && !empty($store_admin_details[0]->company_name ) ? $store_admin_details[0]->company_name  : '' }}</h2>
                                        <p class="invo-owner-address medium-font inter-400 mtb-0">{{!empty($store_admin_details) && !empty($store_admin_details[0]->building_name ) ? $store_admin_details[0]->building_name  : '' }} <br> {{!empty($store_admin_details) && !empty($store_admin_details[0]->street_name ) ? $store_admin_details[0]->street_name  : '' }}<br> {{!empty($store_admin_details) && !empty($store_admin_details[0]->country_name ) ? $store_admin_details[0]->country_name  : '' }} , {{!empty($store_admin_details) && !empty($store_admin_details[0]->state_name ) ? $store_admin_details[0]->state_name  : '' }} , {{!empty($store_admin_details) && !empty($store_admin_details[0]->city_name ) ? $store_admin_details[0]->city_name  : '' }}.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="invoice-table">
                                    <thead>
                                        <tr class="invo-tb-header">
                                            <th class="invo-table-title sno-wid inter-700 medium-font">S. No.</th>
                                            <th class="invo-table-title re-desc-wid inter-700 medium-font">Shop Name</th>
                                            <th class="invo-table-title re-price-wid rate-title inter-700 medium-font">Validity</th>
                                            <th class="invo-table-title tota-wid inter-700 medium-font total-head">Package Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="invo-tb-body">
                                        @if(isset($invoice_details) && !empty($invoice_details))
                                            <tr class="invo-tb-row">
                                                <td class="invo-tb-data">1</td>
                                                <td class="invo-tb-data">{{!empty($invoice_details[0]->store_name ) ? $invoice_details[0]->store_name  : '' }}</td>
                                                <td class="invo-tb-data rate-data">{{!empty($invoice_details[0]->store_validity_date ) ? date("d-M-Y", strtotime($invoice_details[0]->store_validity_date))  : '' }}</td> 
                                                <td class="invo-tb-data total-data">SAR {{!empty($invoice_details[0]->package_amount ) ? $invoice_details[0]->package_amount  : '' }}</td>
                                            </tr>
                                        @else
                                            <tr><td colspan="4">Data not found..!</td></tr>
                                        @endif  
                                    </tbody>
                                </table>
                            </div>
                            <div class="invo-addition-wrap">
                                <div class="invo-add-info-content">
                                    <h3 class="addi-info-title inter-700 medium-font">Additional Information:</h3>
                                    <p class="add-info-desc inter-400 mtb-0">A ut vitae nullam risus at. Justo enim nisi elementum ac. Massa molestie metus vitae ornare turpis donec odio sollicitudin. Ac ut tellus eu donec dictum risus blandit. Quam diam dictum amet.</p>
                                </div>
                                <div class="invo-bill-total">
                                    <table class="invo-total-table">
                                        <tbody>
                                            <tr>
                                                <td class="inter-700 medium-font b-text hotel-sub">Sub Total:</td>
                                                <td class="invo-total-data inter-400 medium-font second-color">SAR {{!empty($invoice_details[0]->package_amount ) ? $invoice_details[0]->package_amount  : '' }}</td>
                                            </tr>
                                            <tr class="tax-row">
                                                <td class="inter-700 medium-font b-text hotel-sub">Tax <span class="invo-total-data inter-700 medium-font second-color">({{!empty($invoice_details[0]->tax_percentage ) ? $invoice_details[0]->tax_percentage  : '' }}%)</span></td>
                                                <td class="invo-total-data inter-400 medium-font second-color">SAR {{!empty($invoice_details[0]->tax_amount ) ? $invoice_details[0]->tax_amount  : '' }}</td>
                                            </tr>
                                            <tr class="disc-row bottom-border">
                                                <td class="inter-700 medium-font b-text hotel-sub">Discount</td>
                                                <input type="hidden" class="discount-type" value="{{!empty($invoice_details[0]->discount_type ) ? $invoice_details[0]->discount_type  : '' }}">
                                                <input type="hidden" class="discount" value="{{!empty($invoice_details[0]->discount ) ? $invoice_details[0]->discount  : '' }}">
                                                <input type="hidden" class="package-amount" value="{{!empty($invoice_details[0]->package_amount ) ? $invoice_details[0]->package_amount  : '' }}">
                                                <td class="invo-total-data inter-400 medium-font discount-price">SAR {{!empty($invoice_details[0]->discount_amount ) ? $invoice_details[0]->discount_amount  : '' }}</td>
                                            </tr>
                                            <tr class="invo-grand-total">
                                                <td class="inter-700 sm-text primary-color hotel-sub">Grand Total:</td>
                                                <td class="sm-text b-text invo-total-price">SAR {{!empty($invoice_details[0]->amount_payable ) ? $invoice_details[0]->amount_payable  : '' }}</td>
                                            </tr>
                                            <tr class="invo-grand-total {{!empty($invoice_details[0]->balance_amount) && $invoice_details[0]->balance_amount <= 0 ? 'dnone'  : '' }}">
                                                <td class="inter-700 sm-text primary-color hotel-sub">Paid Amount:</td>
                                                <td class="sm-text b-text invo-total-price">SAR {{!empty($invoice_details[0]->paid_amount ) ? $invoice_details[0]->paid_amount  : '' }}</td>
                                            </tr>
                                            <tr class="invo-grand-total {{!empty($invoice_details[0]->balance_amount) && $invoice_details[0]->balance_amount <= 0 ? 'dnone'  : '' }}">
                                                <td class="inter-700 sm-text primary-color hotel-sub">Balance Amount:</td>
                                                <td class="sm-text b-text invo-total-price">SAR {{!empty($invoice_details[0]->balance_amount ) ? $invoice_details[0]->balance_amount  : '' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <section class="agency-bottom-content d-print-none" id="agency_bottom">
                    <div class="container">
                        <div class="invo-note-wrap">
                            <div class="note-title">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_8_240)"><path d="M14 3V7C14 7.26522 14.1054 7.51957 14.2929 7.70711C14.4804 7.89464 14.7348 8 15 8H19" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M17 21H7C6.46957 21 5.96086 20.7893 5.58579 20.4142C5.21071 20.0391 5 19.5304 5 19V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H14L19 8V19C19 19.5304 18.7893 20.0391 18.4142 20.4142C18.0391 20.7893 17.5304 21 17 21Z" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9 7H10" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9 13H15" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13 17H15" stroke="#00BAFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g><defs><clipPath id="clip0_8_240"><rect width="24" height="24" fill="white"></rect>
                                </clipPath></defs></svg>
                                <span class="inter-700 medium-font">Note:</span>
                            </div>
                            <h3 class="inter-400 medium-font second-color note-desc mtb-0">This is computer generated receipt and does not require physical signature.</h3>
                        </div>
                    </div>
                </section> 
            </div>
        </section>
        <script src="{{ URL::asset('assets/js/vendors/jquery-3.6.0.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/vendors/bootstrap.bundle.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                discount = $(".discount").val(); 
                discount_type = $(".discount-type").val(); 
                package_amount = $(".package-amount").val();
                discount_price = 0;
                if(discount > 0 && discount_type == "flat" && package_amount > 0) 
                    discount_price = discount;
                if(discount > 0 && discount_type == "percentage" && package_amount > 0)
                    discount_price = (((package_amount / 100) * discount));
                $(".discount-price").text(Number(discount_price).toFixed(2)); 
            });
        </script>
    </body>
</html>