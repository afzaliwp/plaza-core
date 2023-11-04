class DeliveryTime {
	selectors = {
		billingCity: '#billing_city',
		tehranContainer: '.only-tehran.time-and-day-container',
		outOfTehranContainer: '.out-of-tehran.time-and-day-container',
		placeOrderButton: '#place_order',
	};

	elements = {
		billingCity: document.querySelector(this.selectors.billingCity),
		tehranContainer: document.querySelector(this.selectors.tehranContainer),
		outOfTehranContainer: document.querySelector(this.selectors.outOfTehranContainer),
		placeOrderButton: document.querySelector(this.selectors.placeOrderButton),
	};

	constructor() {
		this.elements.billingCity.addEventListener('keydown', this.handleBillingCityChange.bind(this));
		this.elements.billingCity.addEventListener('keyup', this.handleBillingCityChange.bind(this));
		this.elements.billingCity.addEventListener('blur', this.handleBillingCityChange.bind(this));
		this.elements.placeOrderButton.addEventListener('click', this.handlePlaceOrderClick.bind(this));
		this.handleCheckoutLoad();
	}

	handleBillingCityChange(e) {
		const cityName = e.target.value;

		if (cityName === 'تهران' || cityName === 'تهرا') {
			this.elements.tehranContainer.classList.add('show');
			this.elements.outOfTehranContainer.classList.remove('show');
		} else {
			this.elements.tehranContainer.classList.remove('show');
			this.elements.outOfTehranContainer.classList.add('show');
		}
	}

	handlePlaceOrderClick(e) {
		if (this.elements.tehranContainer.classList.contains('show') ) {
			this.elements.outOfTehranContainer.remove();
		} else {
			this.elements.tehranContainer.remove();
		}
	}

	handleCheckoutLoad() {
		const city = this.elements.billingCity.value;
		if (city === 'تهران' || city === 'تهرا') {
			this.elements.tehranContainer.classList.add('show');
			this.elements.outOfTehranContainer.classList.remove('show');
		} else {
			this.elements.tehranContainer.classList.remove('show');
			this.elements.outOfTehranContainer.classList.add('show');
		}
	}
}

if (document.querySelector('form.woocommerce-checkout')) {
	new DeliveryTime();
}
