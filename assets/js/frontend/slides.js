import Swiper, {Autoplay, Navigation, Pagination} from 'swiper';

class SlidesWidget {
	selectors = {
		sliderContainer: '.swiper',
	};

	elements = {
		sliderContainers: document.querySelectorAll(this.selectors.sliderContainer),
	};

	constructor() {
		if (!this.elements.sliderContainers.length) {
			return;
		}

		this.initializeSliders();
	}

	initializeSliders() {
		this.elements.sliderContainers.forEach((item) => {
			const itemsPerSlide = item.getAttribute('data-items-per-slide') || 1;
			const delay = item.getAttribute('data-delay');
			const showArrows = item.getAttribute('data-show-arrows') === 'yes';
			const showPagination = item.getAttribute('data-show-pagination') === 'yes';

			const nextEl = item.querySelector('.swiper-button-next');
			const prevEl = item.querySelector('.swiper-button-prev');

			const options = {
				autoplay: {
					delay: Number(delay),
				},
				loop: true,
				slidesPerView: Number(itemsPerSlide),
				navigation: undefined,
				pagination: undefined,
			};

			if (showArrows) {
				options.navigation = {
					nextEl: nextEl,
					prevEl: prevEl,
				};
			}

			if (showPagination) {
				options.pagination = {
					el: item.querySelector('.swiper-pagination'),
					clickable: true,
				};
			}

			console.log( options );
			Swiper.use([Autoplay, Pagination, Navigation]);
			new Swiper(item, options);
		});
	}

}

document.addEventListener('DOMContentLoaded', () => {
	new SlidesWidget();
});
