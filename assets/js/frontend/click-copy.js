class ClickCopy {
	selectors = {
		copyWrapper: '.click-copy',
	};

	elements = {
		copyWrappers: document.querySelectorAll(this.selectors.copyWrapper),
	};

	constructor() {
		if (!this.elements.copyWrappers.length) {
			return;
		}

		this.handleCopy();
	}

	handleCopy() {
		this.elements.copyWrappers.forEach((item) => {
			item.addEventListener('click', () => {
				let textToCopy = '';

				if (item.hasAttribute('data-copy')) {
					textToCopy = item.getAttribute('data-copy');
				} else if (item.tagName === 'INPUT') {
					textToCopy = item.value;
				} else {
					textToCopy = item.innerText;
				}

				const tempTextArea = document.createElement('textarea');
				tempTextArea.value = textToCopy;
				document.body.appendChild(tempTextArea);
				tempTextArea.select();
				document.execCommand('copy');
				document.body.removeChild(tempTextArea);

				this.showTooltip(item);
			});
		});
	}

	showTooltip(element) {
		const tooltip = document.createElement('div');
		tooltip.classList.add('copy-tooltip');
		tooltip.textContent = 'کپی شد';
		element.appendChild(tooltip);

		setTimeout(() => {
			tooltip.classList.add('show');
		}, 0);

		setTimeout(() => {
			tooltip.classList.remove('show');
			setTimeout(() => {
				tooltip.remove();
			}, 500);
		}, 1000);
	}
}

new ClickCopy();