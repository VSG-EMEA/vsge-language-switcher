/* global languageSwitcher */
import './scss/style.scss';
import { createCookie, eraseCookie } from './utils/cookie';
import { VLS_CLASSNAME } from './constants';
import { generateLanguageList, overlayOff, overlayOn } from './utils';

const PLS_COOKIE_DURATION = 'Session';

document.addEventListener( 'DOMContentLoaded', vls );

function vls() {
	/**
	 * The Modal Window elements
	 *
	 * @type {{languageSwitcherButton: *, overlayWrapper: *, languageSelect: *, regionSelect: *, selector: *}}
	 */
	const modal = {
		selector: document.getElementById( 'vls-modal-selector' ),
		overlayWrapper: document.getElementById( 'overlay-wrapper' ),
		languageSelect: document.getElementById( 'vls-language-select' ),
		regionSelect: document.getElementById( 'vls-region-select' ),
		languageSwitcherButton: document.getElementById( 'vls-button-submit' ),
	};

	const languageSwitchers = document.querySelectorAll( '.' + VLS_CLASSNAME );
	const datasets = document.querySelectorAll( '.vls-dataset' );

	modal.closeButtons = modal.selector.querySelectorAll( '.vls-button-close' );

	function submitLanguage( e ) {
		e.preventDefault();

		const formResult = {
			languageSelected: modal.languageSelect.options[ modal.languageSelect.selectedIndex ].value,
			regionSelected: modal.regionSelect.options[ modal.regionSelect.selectedIndex ].value,
			languageRedirectUri: modal.languageSelect.options[ modal.languageSelect.selectedIndex ].title,
		};

		const { basePath, baseDomain } = languageSwitcher;

		eraseCookie( 'pll_language' );
		createCookie( 'pll_language', formResult.languageSelected, PLS_COOKIE_DURATION, basePath, baseDomain );

		eraseCookie( 'vsge_region' );
		createCookie( 'vsge_region', formResult.regionSelected, PLS_COOKIE_DURATION, basePath, baseDomain );

		document.location.href = formResult.languageRedirectUri;
	}

	if ( datasets.length ) {
		// TODO: for the moment I need the dataset to always be printed as a list for the menu, but it should be better structured
		datasets.forEach( ( item ) => item.outerHTML = generateLanguageList( item.dataset?.languagesRaw ) );
	}

	// For each language switcher button listen for click
	languageSwitchers.forEach( ( button ) => button.addEventListener( 'click', () => overlayOn( modal ) ) );

	// watch for close buttons in order to close the modal window
	modal.closeButtons.forEach( ( button ) => button.addEventListener( 'click', () => overlayOff( modal ) ) );

	// listen for clicks on the outer wrapper
	modal.overlayWrapper.addEventListener( 'click', overlayOff );

	// listen for language form submit
	modal.languageSwitcherButton.addEventListener( 'click', submitLanguage );
}
