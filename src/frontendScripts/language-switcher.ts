import {
	FormResult,
	languageSwitcherGlobs,
	ModalElements,
	PLS_COOKIE_DURATION,
	VLS_CLASSNAME,
	VLS_DOMAIN,
	VLS_REGIONS_MODE,
} from '../constants';
import { generateLanguageList, overlayOff, overlayOn } from '../utils';

declare global {
	interface Window {
		/** the language switcher globals data provided by php */
		languageSwitcher: languageSwitcherGlobs;
		/** Window object function that sets the language cookies */
		vls: {
			setLanguageCookies: (
				language: string,
				region: string,
				options: {}
			) => void;
		};
	}
}

/**
 * Retrieves VLS elements from the document and returns an object containing references to these elements.
 *
 * @return {Object} An object containing references to the selector, overlayWrapper, languageSelect, regionSelect, languageSwitcherButton, and closeButton elements.
 */
function getVlsElements(): ModalElements {
	const selector = document.getElementById( 'vls-modal-selector' );
	selector?.classList.remove( 'loading' );
	const modal = {
		selector,
		overlayWrapper: document.getElementById( 'overlay-wrapper' ),
		languageSelect: document.getElementById(
			'vls-language-select'
		) as HTMLSelectElement,
		regionSelect: document.getElementById(
			'vls-region-select'
		) as HTMLSelectElement,
		languageSwitcherButton: document.getElementById( 'vls-button-submit' ),
		closeButton: selector?.querySelector(
			'.vls-button-close'
		) as HTMLElement,
	};
	return modal as ModalElements;
}

/**
 *  Set the language cookies for the language and region
 * @param language - the language chosen
 * @param region   - the region chosen
 * @param options  - the cookie options
 */
async function setLanguageCookies(
	language: string,
	region: string,
	options: {}
) {
	const cookies = await import(
		/* webpackChunkName: 'vsge-cookie' */
		'js-cookie'
	);

	cookies.default.remove( 'pll_language' );

	if ( language ) {
		/** set the cookie for the language */
		cookies.default.remove( 'pll_language' );
		cookies.default.set( 'pll_language', language, options );
	}

	cookies.default.remove( VLS_DOMAIN + '_region' );

	if ( region ) {
		/** set the cookie for the region */
		cookies.default.set( VLS_DOMAIN + '_region', region, options );
	}
}

function handleAccordionSubmit( event: Event, modal: ModalElements ) {
	const target = event.target as HTMLAnchorElement;
	const formResult: FormResult = {
		languageSelected: target.dataset.language || '',
		regionSelected: target.dataset.region || '',
		languageRedirectUri: target.href,
	};

	return formResult;
}

function handleSelectSubmit( e: Event, modal: ModalElements ) {
	e.preventDefault();
	const languageSelectItem = modal.languageSelect as HTMLSelectElement;
	const regionSelectItem = modal.regionSelect as HTMLSelectElement;
	const formResult: FormResult = {
		languageSelected:
        languageSelectItem.options[ languageSelectItem.selectedIndex ].value,
		regionSelected:
        regionSelectItem.options[ regionSelectItem.selectedIndex ].value,
		languageRedirectUri:
        languageSelectItem.options[ languageSelectItem.selectedIndex ].title,
	};

	return formResult;
}

/**
 * Submit the form when the user selects a language
 * @param e     the submit event
 * @param modal the modal elements object
 */
async function submitLanguage( e: Event, modal: ModalElements ): Promise<void> {
	const formResult: FormResult = ( modal.languageSelect === null || modal.regionSelect === null )
		? handleAccordionSubmit( e, modal )
		: ( handleSelectSubmit( e, modal ) );

	const { cookiePath, cookieDomain } = window.languageSwitcher;

	setLanguageCookies( formResult.languageSelected, formResult.regionSelected, {
		expires:
            PLS_COOKIE_DURATION !== 'Session' ? PLS_COOKIE_DURATION : undefined,
		path: cookiePath,
		domain: cookieDomain || undefined,
	} )
		.then( () => {
			document.location.href = formResult.languageRedirectUri;
		} )
		.catch( ( err ) => {
			console.log( err );
		} );
}

/**
 * Generate the options for the language switcher
 *
 * @param el - The element that contains the dataset
 */
function generateOptions( el: NodeListOf<HTMLElement> ): void {
	const datasets = document.querySelectorAll<HTMLElement>( '.vls-dataset' );

	if ( datasets.length ) {
		// TODO: for the moment I need the dataset to always be printed as a list for the menu, but it should be better structured
		datasets.forEach(
			( item: HTMLElement ) =>
				( item.outerHTML = generateLanguageList(
					item.dataset?.languagesRaw
				) )
		);
	}
}

/**
 * The handleAccordion function is used to handle the accordion behavior of the language switcher
 *
 * @param  languageSwitchers - the language switcher elements
 * @param  modal
 * @return {void} - void
 */
function handleAccordion( languageSwitchers: NodeListOf<HTMLElement>, modal ) {
	const accordions = modal.selector.querySelectorAll( '.accordion-item' );

	if ( ! accordions ) {
		console.log( 'Unable to find accordion items' );
	}

	accordions.forEach( ( accordion: HTMLElement ) => {
		const header: HTMLElement | null = accordion.querySelector( '.accordion-header' );
		const content: HTMLElement | null = accordion.querySelector( '.accordion-content' );

		if ( ! header || ! content ) {
			console.log( 'unable to find accordion header or content' );
			return;
		}

		// Init closed
		content.style.maxHeight = '0px';
		content.style.overflow = 'hidden';

		header.addEventListener( 'click', () => {
			const isOpen = accordion.classList.contains( 'active' );

			// Close all
			accordions.forEach( ( item: HTMLDivElement ) => {
				const itemContent = item.querySelector<HTMLElement>( '.accordion-content' );
				item.classList.remove( 'active' );
				if ( itemContent ) {
					itemContent.style.maxHeight = '0px';
				}
			} );

			// If it was closed, open this one
			if ( ! isOpen ) {
				accordion.classList.add( 'active' );
				content.style.maxHeight = content.scrollHeight + 'px';
			}
		} );

		// for each item in the language switcher list, add a click event listener
		content.querySelectorAll( 'li a' ).forEach( ( item: Element ) => {
			item.addEventListener( 'click', ( e ) => {
				const elDataset = ( item as HTMLElement ).dataset;
				const language = elDataset.language;
				const region = elDataset.region;
				submitLanguage( e, modal );
			} );
		} );
	} );
}

function handleSelect( languageSwitchers: NodeListOf<HTMLElement> ) {
	if ( languageSwitchers.length ) {
		generateOptions( languageSwitchers );
	} else {
		console.log( 'Unable to find the language switcher, please check the selector' );
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

	modal.selector?.classList.remove( 'loading' );

	/**
	 * the language switcher wrapper el
	 */
	const languageSwitchers = document.querySelectorAll( `.${ VLS_CLASSNAME }` );

	if ( languageSwitchers.length === 0 || ! modal.selector ) {
		console.log( 'Unable to find the language switcher, please check the selector' );
		return;
	}

	if ( VLS_REGIONS_MODE === 'accordion' ) {
		handleAccordion( languageSwitchers as NodeListOf<HTMLElement>, modal );
	} else {
		handleSelect( languageSwitchers as NodeListOf<HTMLElement> );
	}

	// For each language switcher button listen for click
	languageSwitchers.forEach( ( button ) =>
		button.addEventListener( 'click', () => overlayOn( modal ) )
	);

	/**
	 * Watch for close buttons in order to close the modal window
	 */
	modal.closeButton?.addEventListener( 'click', ( e ) => overlayOff( e, modal ) );

	/**	listen for clicks on the outer wrapper*/
	modal.overlayWrapper?.addEventListener( 'click', ( e ) =>
		overlayOff( e, modal )
	);

	/** listen for language form submit */
	modal.languageSwitcherButton?.addEventListener( 'click', ( e: MouseEvent ) =>
		submitLanguage( e, modal )
	);
}

window.vls = {
	setLanguageCookies,
};
