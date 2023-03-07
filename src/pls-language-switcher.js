import './scss/style.scss';

console.log( 'language switcher ready' );

document.addEventListener( 'DOMContentLoaded', function () {
	const page = document.querySelector( '.wp-site-blocks' );
	const mastHead =
		document.getElementById( 'masthead' ) || page.querySelector( 'header' );

	const overlayWrapper = document.getElementById( 'overlay-wrapper' );
	const languageSwitcher = document.getElementById( 'pls-language-switcher' );

	const modalSelector = document.getElementById( 'pls-modal-selector' ); // modal window
	const languageSelect = document.getElementById( 'pls-language-select' );
	const languageSwitcherButton =
		document.getElementById( 'pls-button-submit' );
	const closeButton = modalSelector.querySelector( '.pls-button-close' );

	// Lock the body scroll (triggered after menu active)
	function disableBodyScroll( scrollDisabled = true ) {
		if ( ! window.tempScrollTop ) {
			window.tempScrollTop = window.pageYOffset;
		}

		if ( scrollDisabled ) {
			mastHead.classList.add( 'pls-no-animations' );
			document.body.classList.add( 'pls-no-scroll' );
			page.style.transform = `translateY(-${ window.tempScrollTop }px)`;
			mastHead.style.transform = `translateY(${ window.tempScrollTop }px)`;
		} else {
			mastHead.classList.remove( 'pls-no-animations' );
			document.body.classList.remove( 'pls-no-scroll' );
			page.style.transform = null;
			mastHead.style.transform = null;
			window.scrollTo( { top: window.tempScrollTop } );
			window.tempScrollTop = 0;
		}
	}

	// adds the responsive menu shadow
	function overlayOn() {
		overlayWrapper.style.display = 'flex';
		disableBodyScroll();
	}

	// removes the responsive menu shadow
	function overlayOff() {
		overlayWrapper.style.display = 'none';
		modalSelector.style.display = 'none';
		disableBodyScroll( false );
	}

	languageSwitcher.addEventListener( 'click', ( e ) => {
		overlayOn();
		modalSelector.style.display = 'block';
	} );

	overlayWrapper.addEventListener( 'click', ( e ) => {
		overlayOff();
	} );

	closeButton.addEventListener( 'click', ( e ) => {
		overlayOff();
	} );

	languageSwitcherButton.addEventListener( 'click', ( e ) => {
		e.preventDefault();

		const language_selected =
			languageSelect.options[ languageSelect.selectedIndex ].value;
		const language_redirect_uri =
			languageSelect.options[ languageSelect.selectedIndex ].title;

		const basePath = wp.cookiePath;
		const baseDomain = wp.cookieDomain;

		function createCookie( name, value, days ) {
			let expires = '';
			if ( days ) {
				const date = new Date();
				date.setTime( date.getTime() + days * 24 * 60 * 60 * 1000 );
				expires = '; expires=' + date.toGMTString();
			} else {
				expires = '; ';
			}
			document.cookie =
				name +
				'=' +
				value +
				expires +
				'; path=' +
				basePath +
				'; domain=' +
				baseDomain;
		}

		function eraseCookie( name ) {
			createCookie( name, '', -1 );
		}

		eraseCookie( 'pll_language' );
		createCookie( 'pll_language', language_selected, 365 );

		document.location.href = language_redirect_uri;
	} );
} );
