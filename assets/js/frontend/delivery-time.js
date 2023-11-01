class DeliveryTime {
	selectors = {
		billingCity: '#billing_city',
	};

	elements = {
		billingCity: document.querySelector(this.selectors.billingCity),
	};

	constructor() {
		this.elements.billingCity.addEventListener('change', this.handleBillingCityChange.bind(this));
		this.elements.billingCity.addEventListener('blur', this.handleBillingCityChange.bind(this));
	}

	handleBillingCityChange(e) {
		console.log(e);
	}
}

if (document.body.classList.contains('woocommerce-checkout')) {
	new DeliveryTime();
}