const $ = jQuery;

class ContentTable {

	selectors = {
		contentTableWrapper: '.content-table-wrapper',
		content: '.contentstyle',
		headings: [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ]
	};

	elements = {
		content: document.querySelector( this.selectors.content ),
		contentTableWrappers: document.querySelectorAll( this.selectors.contentTableWrapper )
	};

	constructor() {
		if ( !document.querySelector( 'main.single-post' ) ) {
			return;
		}
		if ( !this.elements.contentTableWrappers ) {
			return;
		}

		this.handleTableContent();
	}

	handleTableContent() {
		const headings = this.elements.content.querySelectorAll( this.selectors.headings.join( ',' ) );
		let tocHTML = '<p class="content-table-title">فهرست مطالب</p><ul>';

		headings.forEach( ( heading, index ) => {
			const headingId = 'toc-' + ( index + 1 );
			heading.setAttribute( 'id', headingId );

			tocHTML += `<li><a href="#${ headingId }">${ heading.textContent }</a></li>`;
		} );
		tocHTML += '</ul>';

		this.elements.contentTableWrappers.forEach( ( contentTableWrapper ) => {
			contentTableWrapper.innerHTML += tocHTML;
		} );
	}
}

new ContentTable();
