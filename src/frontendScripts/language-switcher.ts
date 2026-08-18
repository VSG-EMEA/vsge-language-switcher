import { VlsLanguage } from '../constants';

let lastTrigger: HTMLElement | null = null;

const setRegionCookie = ( region: string ) => {
	if ( ! region ) {
		return;
	}
	document.cookie = `vsge_region=${ encodeURIComponent(
		region
	) }; path=/; SameSite=Lax`;
};

const selectedLanguageUrl = ( select: HTMLSelectElement | null ) =>
	select?.selectedOptions[ 0 ]?.dataset.url || '';

const closeDialog = ( dialog: HTMLDialogElement ) => {
	if ( dialog.open ) {
		dialog.close();
	}
};

const setupDialog = ( dialog: HTMLDialogElement ) => {
	dialog
		.querySelectorAll< HTMLElement >( '[data-vls-close]' )
		.forEach( ( button ) =>
			button.addEventListener( 'click', () => closeDialog( dialog ) )
		);
	dialog.addEventListener( 'click', ( event ) => {
		if ( event.target !== dialog ) {
			return;
		}
		const bounds = dialog.getBoundingClientRect();
		if (
			event.clientX < bounds.left ||
			event.clientX > bounds.right ||
			event.clientY < bounds.top ||
			event.clientY > bounds.bottom
		) {
			closeDialog( dialog );
		}
	} );
	dialog.addEventListener( 'close', () => {
		lastTrigger?.focus();
		lastTrigger = null;
	} );

	dialog
		.querySelectorAll< HTMLButtonElement >( '.vls-accordion-trigger' )
		.forEach( ( button ) => {
			button.addEventListener( 'click', () => {
				const panel = document.getElementById(
					button.getAttribute( 'aria-controls' ) || ''
				);
				if ( ! panel ) {
					return;
				}
				const expanded =
					'true' === button.getAttribute( 'aria-expanded' );
				button.setAttribute( 'aria-expanded', String( ! expanded ) );
				panel.toggleAttribute( 'hidden', expanded );
			} );
		} );

	const regionSelect = dialog.querySelector< HTMLSelectElement >(
		'[data-vls-region-select]'
	);
	const languageSelect = dialog.querySelector< HTMLSelectElement >(
		'[data-vls-language-select]'
	);
	regionSelect?.addEventListener( 'change', () => {
		const language = regionSelect.selectedOptions[ 0 ]?.dataset.language;
		if ( language && languageSelect ) {
			const option = Array.from( languageSelect.options ).find(
				( item ) => item.value === language
			);
			if ( option ) {
				languageSelect.value = option.value;
			}
		}
	} );
	dialog
		.querySelector< HTMLElement >( '[data-vls-apply]' )
		?.addEventListener( 'click', () => {
			setRegionCookie( regionSelect?.value || '' );
			const url = selectedLanguageUrl( languageSelect );
			if ( url ) {
				window.location.assign( url );
			}
		} );
	dialog
		.querySelectorAll< HTMLElement >( '[data-vls-region-link]' )
		.forEach( ( link ) =>
			link.addEventListener( 'click', () =>
				setRegionCookie( link.dataset.region || '' )
			)
		);
};

const setupDataset = ( element: HTMLElement ) => {
	if ( ! element.dataset.vlsDataset || element.childElementCount ) {
		return;
	}
	try {
		const data = JSON.parse( element.dataset.vlsDataset );
		const languages: VlsLanguage[] = Array.isArray( data.languages )
			? data.languages
			: [];
		const list = document.createElement( 'ul' );
		languages.forEach( ( language ) => {
			if ( ! language?.url || ! language?.name ) {
				return;
			}
			const item = document.createElement( 'li' );
			item.className = 'wp-block-megamenu-subitem vsge-language-item';
			const anchor = document.createElement( 'a' );
			anchor.className = 'menu-item-link';
			anchor.href = language.url;
			const label = document.createElement( 'span' );
			label.className = `vsge-language language-${ language.slug }`;
			label.textContent = language.name;
			anchor.append( label );
			item.append( anchor );
			list.append( item );
		} );
		element.replaceChildren( list );
	} catch {
		// A malformed legacy dataset must not break the rest of the page.
	}
};

export const initializeLanguageSwitcher = () => {
	document
		.querySelectorAll< HTMLElement >( '.vls-dataset' )
		.forEach( setupDataset );
	document
		.querySelectorAll< HTMLSelectElement >(
			'.vls-block--dropdown [data-vls-language-select]'
		)
		.forEach( ( select ) =>
			select.addEventListener( 'change', () => {
				const url = selectedLanguageUrl( select );
				if ( url ) {
					window.location.assign( url );
				}
			} )
		);

	const dialog = document.querySelector< HTMLDialogElement >(
		'#vls-language-dialog'
	);
	if ( ! dialog || typeof dialog.showModal !== 'function' ) {
		return;
	}
	setupDialog( dialog );
	document
		.querySelectorAll< HTMLElement >( '[data-vls-open-modal]' )
		.forEach( ( trigger ) =>
			trigger.addEventListener( 'click', () => {
				lastTrigger = trigger;
				if ( ! dialog.open ) {
					dialog.showModal();
				}
				dialog
					.querySelector< HTMLElement >( '[data-vls-close]' )
					?.focus();
			} )
		);
};
