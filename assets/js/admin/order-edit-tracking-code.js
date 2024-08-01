const $ = jQuery;

class OrderEditTrackingCode {

	selectors = {
		smsFormWrapper: '.send-sms-to-user-meta-box',
		sendButton: '.send-button',
		result: '.result',
	}

	elements = {
		smsFormWrapper: document.querySelector( this.selectors.smsFormWrapper ),
		sendButton: document.querySelector( this.selectors.smsFormWrapper + ' ' + this.selectors.sendButton ),
		result: document.querySelector( this.selectors.smsFormWrapper + ' ' + this.selectors.result ),
	}

	constructor() {
		this.handleSendSms();
	}

	handleSendSms() {
		if ( !this.elements.smsFormWrapper ) {
			return;
		}

		this.elements.smsFormWrapper.classList.remove('loading');
		this.elements.sendButton.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			this.elements.sendButton.disabled = true;
			this.elements.sendButton.textContent = 'در حال ارسال';

			const fields = this.elements.smsFormWrapper.querySelectorAll( 'input, select, textarea' );

			console.log( 'fields' )
			console.log( fields )

			const fieldsData = Array.from(fields).reduce((obj, field) => {
				obj[field.name] = field.value;
				return obj;
			}, {});


			this.sendSms( fieldsData );

		} );

	}

	sendSms( data ) {
		console.log( 'data' )
		console.log( data )
		const self = this;
		$.ajax( {
			url: PlazaObj.ajaxUrl,
			type: 'POST',
			data: {
				action: 'plaza_admin_send_sms',
				post_id: data.post_id,
				pattern: data.pattern,
				shipping_company: data.shipping_company,
				tracking_code: data.tracking_code,
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
	new OrderEditTrackingCode();
} );