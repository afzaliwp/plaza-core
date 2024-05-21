const $ = jQuery;

class OrderPreviewZpalLink {

	selectors = {
		wrapper: '.send-zpal-link-order-preview',
		sendButton: '.send-zpal-link-button',
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
			if (event.target.matches('.send-zpal-link-button')) {
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
			url: PlazaObj.ajaxUrl, // Replace with the actual URL of your AJAX handler
			type: 'POST',
			data: {
				action: 'plaza_admin_send_zpal_link_sms',
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
	new OrderPreviewZpalLink();
} );