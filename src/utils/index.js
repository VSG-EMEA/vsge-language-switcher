import { disableBodyScroll, enableBodyScroll } from 'body-scroll-lock';

/**
 * Generate a list of languages in HTML format.
 * I need it in conjunction with the megamenu that is in this repo https://github.com/erikyo/getwid-megamenu
 *
 * @example `<!-- wp:vsge/language-switcher {"displayAs":"dataset"} /-->`
 *
 * @param {Object} data - An object containing language information.
 * @return {string} - The generated HTML for the language list.
 */
export function generateLanguageList( data ) {
	const languagesData = JSON.parse( data );
	let menuHTML = '<ul>';

	Object.entries( languagesData.languages ).forEach( ( [ langcode, lang ] ) => {
		const classes = Object.values( lang.classes ).join( ' ' );

		menuHTML += `
      <li class="wp-block-megamenu-subitem vsge-language-item ${ classes }">
        <a href="${ lang.url }" class="menu-item-link">
          <span class="vsge-language language-${ langcode }">${ lang.name }</span>
        </a>
      </li>
    `;
	} );

	return menuHTML + '</ul>';
}

/**
 * adds the responsive menu shadow
 * @param {Object} modal - the Object that hold the elements needed to show/hide the modal window
 */
export function overlayOn( modal ) {
	disableBodyScroll( document.body );
	modal.selector.style.display = 'block';
	modal.overlayWrapper.style.display = 'flex';
}

/**
 * removes the responsive menu shadow
 * @param {Object} modal - the Object that hold the elements needed to show/hide the modal window
 */
export function overlayOff( modal ) {
	enableBodyScroll( document.body );
	modal.overlayWrapper.style.display = 'none';
	modal.selector.style.display = 'none';
}
