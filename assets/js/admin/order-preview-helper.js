const $ = jQuery;

class OrderPreviewHelper {
    constructor() {
        this.saveOrderIdFromPreviewButton();
    }

    saveOrderIdFromPreviewButton() {
        if (!document.body.classList.contains('post-type-shop_order')) {
            return;
        }

        const previewButtons = document.querySelectorAll( 'a.order-preview' );

        console.log(previewButtons)
        previewButtons.forEach( (button) => {
            button.addEventListener( 'click', () => {
                window.currentOrderPreviewId = button.dataset.orderId;
                this.handleOrderPreviewExtraFeatures();
            } );
        } );
    }

    handleOrderPreviewExtraFeatures() {
        jQuery(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.url.indexOf('action=woocommerce_get_order_details') > -1) {
                //AJAX call completed.

                const self = this;
                $.ajax( {
                    url: PlazaObj.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'plaza_admin_order_preview_data',
                        post_id: window.currentOrderPreviewId,
                    },
                    success: function ( response ) {
                        document.querySelector('.extra-order-preview-data-start').innerHTML = response.data;
                    },
                    error: function () {
                        document.querySelector('.extra-order-preview-data-start').innerHTML = 'خطا در گرفتن امکانات اضافه';
                    }
                } );
            }
        });

    }
}

new OrderPreviewHelper();