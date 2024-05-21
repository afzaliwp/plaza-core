const $ = jQuery;

class OrderPreviewRefundInfo {

    selectors = {
        form: '#plaza-set-order-categories-form',
        categoriesSelectField: '#plaza-order-categories',
        orderId: '#order-categories-order-id',
        submitButton: '#plaza-order-categories-submit',
        result: '.results',
    }

    elements = {
        form: '',
        categoriesSelectField: '',
        orderId: '',
        submitButton: '',
        result: '',
    }

    constructor() {
        this.handleSubmitCategory();
    }

    handleSubmitCategory() {
        document.body.addEventListener('submit', (event) => {
            if (event.target.matches(this.selectors.form)) {
                event.preventDefault();
                const form = event.target;
                this.elements.categoriesSelectField = form.querySelector(this.selectors.categoriesSelectField);
                this.elements.result = form.querySelector(this.selectors.result);
                this.elements.submitButton = form.querySelector(this.selectors.submitButton);
                const orderId = form.querySelector(this.selectors.orderId).value;

                event.preventDefault();
                this.elements.submitButton.disabled = true;
                this.elements.submitButton.textContent = 'در حال ارسال';

                this.setCategory(orderId, this.elements.categoriesSelectField.value);
            }
        });
    }

    setCategory(orderId, category) {
        const self = this;
        $.ajax({
            url: PlazaObj.ajaxUrl,
            type: 'POST',
            data: {
                action: 'plaza_admin_set_order_category',
                post_id: orderId,
                category: category,
            },
            success: function (response) {
                self.elements.result.innerText = response.data;

                self.elements.submitButton.disabled = false;
                self.elements.submitButton.textContent = 'ارسال';
            },
            error: function () {
                self.elements.result.innerText = 'خطا در ارسال';
                self.elements.submitButton.disabled = false;
                self.elements.submitButton.textContent = 'ارسال';
            }
        });
    }
}

window.addEventListener('load', () => {
    new OrderPreviewRefundInfo();
});