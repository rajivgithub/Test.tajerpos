<div class="modal fade quick-view-modal" id="modal_box" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="ion-android-close"></i></span>
            </button>
            <div class="modal_body product-modal-popup">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5 col-md-5 col-sm-12">
                            <div class="modal_tab product-images-modal-tab">
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-12 single_product product-cart-list single-product-details" style="border:none;">
                            <div class="modal_right">
                                <div class="modal_title mb-10">
                                    <a href="#" class="modal-product-url" target="_blank"><h2 class="modal-product-name"></h2></a>
                                </div>
                                <p><b class="category-name"></b></p>
                                <p><b class="sub-category-name"></b></p>
                                <div class="modal_price mb-10">
                                    <span class="new_price modal-product-price"></span>
                                </div>
                                <div class="modal_description mb-15"><p class="modal-product-description"></p></div>
                                <div class="variants_selects variants-product-features">
                                </div>
                                <div class="modal_add_to_cart">
                                    <form action="#">
                                        <input type="hidden" class="single-product-id product-id" value="">
                                        <input type="hidden" class="single-product-trackable" value="">
                                        <input type="hidden" class="modal-product-unit" value="">
                                        <input type="hidden" class="product-unit" value="">
                                        <input type="hidden" class="single-product-type" value="">
                                        <input type="hidden" class="single-product-variants-combination select-variants" value="">
                                        <input type="hidden" class="modal-variant-on-hand" value="">
                                        <input type="hidden" class="variant-on-hand variants-quantity" value="">
                                        <input type="hidden" class="product-price" value="">
                                        <input type="hidden" class="selected-element-class" value="">
                                        <div class="mb-10">
                                            <input min="1" max="100" step="1" value="1" onkeypress="return isNumber(event)" class="quantity add-product-quantity" type="number">
                                        </div>
                                        <span class="mb-10 error error-message"></span>
                                        <div class="d-flex mb-10">
                                            <button type="button" title="{{ __('customer.add_to_wishlist') }}" data-page="" class="product-wishlist"><i data-wishlist-type="add" class="wishlist-icon far fa-heart"></i></button>
                                            <button type="button" data-type="product-in-popup" class="product-add-to-cart add-to-cart">
                                                {{ __('customer.add_to_cart') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>