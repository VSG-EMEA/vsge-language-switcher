/** import styles */
import './scss/style.scss';
import { vls } from './frontendScripts/language-switcher';
import { getCookieValue, hideClassesByRegion } from './frontendScripts/region-selector';
import { appendCF7Afield } from './frontendScripts/wpcf7';
import { FALLBACK_REGION, VLS_DOMAIN } from './constants';

/**
 * The modulrRegionalController function modifies the behavior of a web page based on the user's
 * region, including hiding unnecessary data and appending a custom hidden input field to each form.
 */
export function vsgeRegionalController() {
	// get the current region from the cookie
	let region: string | undefined = getCookieValue( VLS_DOMAIN + '_region' );

	/** the language switcher */
	document.addEventListener( 'DOMContentLoaded', vls );

	if ( ! region ) {
		const language = getCookieValue( 'pll_language' ) ?? '';
		/** if the language is French or German, we can safety assume that the region is the same, but this is not true for the English language */
		if ( [ 'de', 'fr' ].includes( language ) ) {
			region = language.toLowerCase();
		}
		region = FALLBACK_REGION;
	}

	// hide the regions html elements based on the current region
	hideClassesByRegion( region );

	// appends a custom hidden input field to each form
	appendCF7Afield( region );
}

vsgeRegionalController();
