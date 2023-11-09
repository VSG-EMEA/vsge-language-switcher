import './scss/style.scss';
import { disableBodyScroll, enableBodyScroll } from 'body-scroll-lock';
import { createCookie, eraseCookie } from './utils/cookie';
import { VLS_CLASSNAME } from './constants';

const PLS_COOKIE_DURATION = 'Session';

document.addEventListener( 'DOMContentLoaded', function() {
	const overlayWrapper = document.getElementById( 'overlay-wrapper' );
	const languageSwitchers = document.querySelectorAll( '.' + VLS_CLASSNAME );

	const modalSelector = document.getElementById( 'vls-modal-selector' ); // modal window
	const languageSelect = document.getElementById( 'vls-language-select' );
	const regionSelect = document.getElementById( 'vls-region-select' );

	const languageSwitcherButton =
		document.getElementById( 'vls-button-submit' );
	const closeButtons = modalSelector.querySelectorAll( '.vls-button-close' );

  const lockedItem = document.querySelector('.wp-site-blocks');

	// adds the responsive menu shadow
	function overlayOn() {
    disableBodyScroll( lockedItem );
		modalSelector.style.display = 'block';
		overlayWrapper.style.display = 'flex';
	}

	// removes the responsive menu shadow
	function overlayOff() {
		enableBodyScroll( lockedItem );
		overlayWrapper.style.display = 'none';
		modalSelector.style.display = 'none';
	}

	languageSwitchers.forEach( ( button ) => button.addEventListener( 'click', overlayOn ) );

	closeButtons.forEach( ( button ) => button.addEventListener( 'click', overlayOff ) );

	overlayWrapper.addEventListener( 'click', overlayOff );

	languageSwitcherButton.addEventListener( 'click', ( e ) => {
		e.preventDefault();

		const languageSelected =
			languageSelect.options[ languageSelect.selectedIndex ].value;
		const languageRedirectUri =
			languageSelect.options[ languageSelect.selectedIndex ].title;
		const regionSelected =
			regionSelect.options[ regionSelect.selectedIndex ].value;

		const basePath = vls.cookiePath;
		const baseDomain = vls.cookieDomain;

		eraseCookie( 'pll_language' );
		createCookie( 'pll_language', languageSelected, PLS_COOKIE_DURATION, basePath, baseDomain );

		eraseCookie( 'vsge_region' );
		createCookie( 'vsge_region', regionSelected, PLS_COOKIE_DURATION, basePath, baseDomain );

		document.location.href = languageRedirectUri;
	} );
} );
