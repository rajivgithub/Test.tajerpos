<footer class="footer_widgets">
    <div class="container">
        <div class="shipping_area">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="single_shipping d-flex align-items-center">
                        <div class="shipping_icon"><img src="{{ URL::asset('assets/customer/images/others/shipping1.png') }}" alt=""></div>
                        <div class="shipping_text">
                            <h3>{{ __('customer.easy_returns') }}</h3>
                            <!-- <p>Capped at SAR 39 per order</p> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="single_shipping d-flex align-items-center">
                        <div class="shipping_icon"><img src="{{ URL::asset('assets/customer/images/others/shipping2.png') }}" alt=""></div>
                        <div class="shipping_text">
                            <h3>{{ __('customer.secure_payments') }}</h3>
                            <!-- <p>12 Months Installments</p> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="single_shipping d-flex align-items-center">
                        <div class="shipping_icon">
                            <img src="{{ URL::asset('assets/customer/images/others/shipping3.png') }}" alt="">
                        </div>
                        <div class="shipping_text">
                            <h3>{{ __('customer.assured_quality') }}</h3>
                            <!-- <p>Shop with Confidence</p> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="main_footer">
            <div class="row">
                <div class="col-12">
                    <div class="main_footer_inner d-flex">
                        <div class="footer_widget_list contact footer_list_width">
                            <h3>{{ __('customer.contact_us') }}</h3>
                            <div class="footer_contact_desc">
                                <p class="footer-contact-desc">{{ __('customer.contact_us_description') }} <a href="#" class="customer-store-email-id"></a></p>
                            </div>
                            <div class="footer_contact_info">
                                <div class="footer_contact_info_list d-flex align-items-center">
                                    <div class="footer_contact_info_icon">
                                        <span class="pe-7s-map-marker"></span>
                                    </div>
                                    <div class="footer_contact_info_text">
                                        <p class="customer-store-address"></p>
                                    </div>
                                </div>
                                <div class="footer_contact_info_list d-flex align-items-center">
                                    <div class="footer_contact_info_icon">
                                        <span class="pe-7s-phone"></span>
                                    </div>
                                    <div class="footer_contact_info_text">
                                        <ul>
                                            <li><a class="customer-store-phone-number" href="#"></a></li>
                                            <!-- <li><a href="tel:+0123456789">+ 0 123 456 789</a></li> -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="footer_menu_widget footer_list_width middle d-flex">
                            <div class="footer_widget_list">
                                <h3>{{ __('customer.information') }}</h3>
                                <div class="footer_menu">
                                    <ul>
                                        <li><a href="#0">{{ __('customer.about_us') }}</a></li>
                                        <li><a href="#0">{{ __('customer.terms_conditions') }}</a></li>
                                        <li><a href="#0">{{ __('customer.shipping_policy') }}</a></li>
                                        <li><a href="#0">{{ __('customer.returns_policy') }}</a></li>
                                        <li><a href="#0">{{ __('customer.privacy_policy') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="footer_widget_list">
                                <h3>{{ __('customer.account') }}</h3>
                                <div class="footer_menu">
                                    <ul>
                                        <li><a href="#0">{{ __('customer.my_account') }}</a></li>
                                        <li><a href="#0">{{ __('customer.my_orders') }}</a></li>
                                        <li><a href="#0">{{ __('customer.returns') }}</a></li>
                                        <li><a href="#0">{{ __('customer.shipping') }}</a></li>
                                        <li><a href="#0">{{ __('customer.wishlist') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="footer_widget_list footer_list_width">
                            <h3>{{ __('customer.newsletter') }}</h3>
                            <div class="footer_newsletter">
                                <div class="newsletter_desc">
                                    <p>{{ __('customer.contact_us_description') }}
                                    <a class="customer-store-email-id" href="#"></a></p>
                                </div>
                                <div class="newsletter_subscribe">
                                    <form id="mc-form" class="dnone">
                                        <input id="mc-email" type="email" autocomplete="off" placeholder="Email Address">
                                        <button id="mc-submit"><i class="ion-arrow-right-c"></i></button>
                                    </form>
                                    <div class="mailchimp-alerts text-centre">
                                        <div class="mailchimp-submitting"></div>
                                        <div class="mailchimp-success"></div>
                                        <div class="mailchimp-error"></div>
                                    </div>
                                </div>
                                <div class="footer_paypal">
                                    <a href="#"><img src="{{ URL::asset('assets/customer/images/others/paypal.png') }}" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer_bottom">
            <div class="copyright_right text-center">
                <p class="copyright-content"></p>
            </div>
        </div>
    </div>
</footer>