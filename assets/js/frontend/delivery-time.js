const $ = jQuery;

class DeliveryTime {
	selectors = {
		billingCity: '#billing_city',
		tehranContainer: '.only-tehran.time-and-day-container',
		outOfTehranContainer: '.out-of-tehran.time-and-day-container',
		placeOrderButton: '#place_order',
		daySelect: '#plaza-day-select',
		timeSelect: '#plaza-time-select',
	};

	elements = {
		billingCity: document.querySelector( this.selectors.billingCity ),
		tehranContainer: document.querySelector( this.selectors.tehranContainer ),
		outOfTehranContainer: document.querySelector( this.selectors.outOfTehranContainer ),
		placeOrderButton: document.querySelector( this.selectors.placeOrderButton ),
		daySelect: document.querySelector( this.selectors.daySelect ),
		timeSelect: document.querySelector( this.selectors.timeSelect ),
	};

	originalTimeOptions = null;

	constructor() {
		// this.elements.billingCity.addEventListener( 'keydown', this.handleBillingCityChange.bind( this ) );
		// this.elements.billingCity.addEventListener( 'keyup', this.handleBillingCityChange.bind( this ) );
		// this.elements.billingCity.addEventListener( 'blur', this.handleBillingCityChange.bind( this ) );
		$(this.elements.billingCity).on('change', this.handleBillingCityChange .bind( this ));
		this.elements.placeOrderButton.addEventListener( 'click', this.handlePlaceOrderClick.bind( this ) );
		this.elements.daySelect.addEventListener( 'change', this.handleDaySelectChange.bind( this ) );
		this.handleCheckoutLoad();
	}

	handleDaySelectChange( e ) {
		const day = e.target.value;
		const firstDayOption = this.elements.daySelect.options[ 0 ].value;

		// If the selected day is not the first option, show both time options
		if ( day !== firstDayOption ) {
			this.elements.timeSelect.innerHTML = `
                <option value="12-16">۱۲ الی ۱۶</option>
                <option value="16-20">۱۶ الی ۲۰</option>
            `;
		} else {
			// If the selected day is the first option, show the original time options
			this.elements.timeSelect.innerHTML = this.originalTimeOptions;
		}
	}

	handleBillingCityChange( e ) {
		const selectedOption = e.target.selectedOptions[0];
		if ( ! selectedOption ) {
			return;
		}

		const cityName = selectedOption.text;

		if ( cityName === 'تهران' ) {
			this.elements.tehranContainer.classList.add( 'show' );
			this.elements.outOfTehranContainer.classList.remove( 'show' );
		} else {
			this.elements.tehranContainer.classList.remove( 'show' );
			this.elements.outOfTehranContainer.classList.add( 'show' );
		}
	}

	handlePlaceOrderClick( e ) {
		if ( this.elements.tehranContainer.classList.contains( 'show' ) ) {
			this.elements.outOfTehranContainer.remove();
		} else {
			this.elements.tehranContainer.remove();
		}
	}

	handleCheckoutLoad() {
		const city = this.elements.billingCity.value;
		if ( city === 'تهران' || city === 'تهرا' ) {
			this.elements.tehranContainer.classList.add( 'show' );
			this.elements.outOfTehranContainer.classList.remove( 'show' );
		} else {
			this.elements.tehranContainer.classList.remove( 'show' );
			this.elements.outOfTehranContainer.classList.add( 'show' );
		}

		// Store the original time options
		this.originalTimeOptions = this.elements.timeSelect.innerHTML;
	}
}

if ( document.querySelector( 'form.woocommerce-checkout' ) ) {
	new DeliveryTime();
}
