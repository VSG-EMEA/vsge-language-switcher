import { createCookie, eraseCookie } from '../utils/cookie';
import { languageSwitcherGlobs, ModalElements, PLS_COOKIE_DURATION, VLS_CLASSNAME, VLS_DOMAIN } from '../constants';
import { generateLanguageList, overlayOff, overlayOn } from '../utils';

declare global {
	interface Window {
		languageSwitcher : languageSwitcherGlobs
	}
}

/**
 * Retrieves VLS elements from the document and returns an object containing references to these elements.
 *
 * @return {Object} An object containing references to the selector, overlayWrapper, languageSelect, regionSelect, languageSwitcherButton, and closeButtons elements.
 */
function getVlsElements(): ModalElements {
	const selector = document.getElementById( 'vls-modal-selector' );
	const modal = {
		selector,
		overlayWrapper: document.getElementById( 'overlay-wrapper' ),
		languageSelect: document.getElementById( 'vls-language-select' ) as HTMLSelectElement,
		regionSelect: document.getElementById( 'vls-region-select' ) as HTMLSelectElement,
		languageSwitcherButton: document.getElementById( 'vls-button-submit' ),
		closeButtons: selector?.querySelectorAll( '.vls-button-close' ) as NodeListOf<HTMLElement>,
	};
	return modal as ModalElements;
}

/**
 * Submit the form when the user selects a language
 * @param e     the submit event
 * @param modal the modal elements object
 */
function submitLanguage( e: Event, modal: ModalElements ): void {
	e.preventDefault();

	if ( modal.languageSelect === null || modal.regionSelect === null ) {
		console.log( 'unable to find language selector' );
		return;
	}

	const formResult = {
		languageSelected: modal.languageSelect.options[ modal.languageSelect.selectedIndex ].value,
		regionSelected: modal.regionSelect.options[ modal.regionSelect.selectedIndex ].value,
		languageRedirectUri: modal.languageSelect.options[ modal.languageSelect.selectedIndex ].title,
	};

	const { cookiePath, cookieDomain } = window.languageSwitcher;

	eraseCookie( 'pll_language' );
	createCookie( 'pll_language', formResult.languageSelected, PLS_COOKIE_DURATION, cookiePath, cookieDomain );

	eraseCookie( VLS_DOMAIN + '_region' );
	createCookie( VLS_DOMAIN + '_region', formResult.regionSelected, PLS_COOKIE_DURATION, cookiePath, cookieDomain );

	document.location.href = formResult.languageRedirectUri;
}

/**
 * Generate the options for the language switcher
 *
 * @param el - The element that contains the dataset
 */
function generateOptions( el: NodeListOf<HTMLElement> ): void {
	const datasets = document.querySelectorAll< HTMLElement >( '.vls-dataset' );

	if ( datasets.length ) {
		// TODO: for the moment I need the dataset to always be printed as a list for the menu, but it should be better structured
		datasets.forEach( ( item: HTMLElement ) => item.outerHTML = generateLanguageList( item.dataset?.languagesRaw ) );
	}
}

/**
 * The vls function is executed when the page is loaded.
 * will generate the language switcher on the page
 * and listen for clicks on the language switcher
 */
export function vls() {
	/**
	 * The Modal Window elements
	 */
	const modal: ModalElements = getVlsElements();

	/**
	 * the language switcher select elements scripts
	 */
	const languageSwitchers = document.querySelectorAll( `.${ VLS_CLASSNAME }` );

	if ( languageSwitchers.length ) {
		generateOptions( languageSwitchers as NodeListOf<HTMLElement> );

		// For each language switcher button listen for click
		languageSwitchers.forEach(
			( button ) => button.addEventListener( 'click', () => overlayOn( modal ) )
		);
	} else {
		console.log( 'unable to find language switcher' );
	}

	/**
	 * Watch for close buttons in order to close the modal window
	 */
	modal.closeButtons?.forEach(
		( button ) => button.addEventListener( 'click', () => overlayOff( modal ) )
	);

	/**	listen for clicks on the outer wrapper*/
	modal.overlayWrapper?.addEventListener( 'click', () => overlayOff( modal ) );

	/** listen for language form submit */
	modal.languageSwitcherButton?.addEventListener( 'click',
		( e: MouseEvent ) => submitLanguage( e, modal )
	);
}
