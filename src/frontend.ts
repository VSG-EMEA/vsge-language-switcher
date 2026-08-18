import './scss/style.scss';
import { initializeLanguageSwitcher } from './frontendScripts/language-switcher';
import {
	hideClassesByRegion,
	readRegionModel,
} from './frontendScripts/region-selector';
import { appendCF7Afield } from './frontendScripts/wpcf7';
import { VLS_DOMAIN } from './constants';

const readCookie = ( name: string ) =>
	document.cookie.match(
		new RegExp(
			`(?:^|;\\s*)${ name.replace(
				/[.*+?^${}()|[\\]\\]/g,
				'\\$&'
			) }=([^;]*)`
		)
	)?.[ 1 ];

const initialize = () => {
	const model = readRegionModel();
	const region = readCookie( `${ VLS_DOMAIN }_region` );
	if ( region ) {
		hideClassesByRegion( decodeURIComponent( region ), model );
		appendCF7Afield( decodeURIComponent( region ) );
	}
	initializeLanguageSwitcher();
};

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initialize, { once: true } );
} else {
	initialize();
}
