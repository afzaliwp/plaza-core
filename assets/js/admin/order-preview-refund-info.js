const $ = jQuery;

class OrderPreviewRefundInfo {

	selectors = {
		wrapper: '.send-refund-info-order-preview',
		sendButton: '.send-refund-info-button',
		result: '.results',
	}

	elements = {
		wrapper: '',
		sendButton: '',
		result: '',
	}

	constructor() {
		this.handleSendSms();
	}

	handleSendSms() {
		document.body.addEventListener('click', (event) => {
			if (event.target.matches('.send-refund-info-button')) {
				const button = event.target;
				this.elements.wrapper = event.target.closest( this.selectors.wrapper );
				this.elements.result = this.elements.wrapper.querySelector( this.selectors.result );
				event.preventDefault();
				button.disabled = true;
				button.textContent = 'در حال ارسال';
				this.elements.sendButton = button;

				const orderId = window.currentOrderPreviewId;
				this.sendSms( orderId );
			}
		});
	}

	sendSms( orderId ) {
		const self = this;
		$.ajax( {
			url: PlazaObj.ajaxUrl,
			type: 'POST',
			data: {
				action: 'plaza_admin_send_refund_info_sms',
				post_id: orderId,
			},
			success: function ( response ) {
				self.elements.result.innerText = response.data;

				self.elements.sendButton.disabled = false;
				self.elements.sendButton.textContent = 'ارسال';
			},
			error: function () {
				self.elements.result.innerText = 'خطا در ارسال';
				self.elements.sendButton.disabled = false;
				self.elements.sendButton.textContent = 'ارسال';
			}
		} );
	}
}

window.addEventListener( 'load', () => {
	new OrderPreviewRefundInfo();
} );