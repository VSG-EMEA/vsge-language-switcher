import { disableBodyScroll, enableBodyScroll } from 'body-scroll-lock';
import { ModalElements } from '../constants';

interface VlsCountryData {
	id : string; //  language id
	slug: string; //  language code used in urls
	name: string; //  language name
	url: string; //  url of the translation
	flag: string; //  url of the flag
	locale: string; //  locale code
	order: number; //  language order
	current_lang: boolean; //  true if this is the current language, false otherwise
	no_translation: boolean; //  true if there is no available translation, false otherwise
	classes: string[]; //  array of classes to add to the language item
}

/**
 * Generate a list of languages in HTML format.
 * I need it in conjunction with the megamenu that is in this repo https://github.com/erikyo/getwid-megamenu
 *
 * @example `<!-- wp:vsge/language-switcher {"displayAs":"dataset"} /-->`
 *
 * @param {Object} data - An object containing language information.
 * @return {string} - The generated HTML for the language list.
 */
export function generateLanguageList( data: string | undefined ): string {
	if ( ! data ) {
		return '';
	}
	const languagesData = JSON.parse( data );
	let menuHTML = '<ul>';

	Object.entries( languagesData.languages as VlsCountryData[] ).forEach( ( [ langCode, lang ] ) => {
		const classes = Object.values( lang.classes ).join( ' ' );
		menuHTML += `
      <li class="wp-block-megamenu-subitem vsge-language-item ${ classes }">
        <a href="${ lang.url }" class="menu-item-link">
          <span class="vsge-language language-${ langCode }">${ lang.name }</span>
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
export function overlayOn( modal: ModalElements ) {
	disableBodyScroll( document.body );
	if ( modal ) {
		if ( modal.selector ) {
			modal.selector.classList.add( 'active' );
		}
		if ( modal.overlayWrapper ) {
			modal.overlayWrapper.classList.add( 'active' );
		}
	}
}

/**
 * removes the responsive menu shadow
 * @param {Object} modal - the Object that hold the elements needed to show/hide the modal window
 */
export function overlayOff( modal: ModalElements ) {
	enableBodyScroll( document.body );
	if ( modal ) {
		if ( modal.overlayWrapper ) {
			modal.overlayWrapper.classList.remove( 'active' );
		}
		if ( modal.selector ) {
			modal.selector.classList.remove( 'active' );
		}
	}
}
