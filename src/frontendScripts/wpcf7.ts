import { VLS_DOMAIN } from '../constants';

export function appendCF7Afield( region: string ) {
	if ( ! region ) {
		return;
	}
	document
		.querySelectorAll< HTMLElement >( '.localized-form .wpcf7 form' )
		.forEach( ( form ) => {
			const selector = `input[name="_${ VLS_DOMAIN }_region"]`;
			let input = form.querySelector< HTMLInputElement >( selector );
			if ( ! input ) {
				input = document.createElement( 'input' );
				input.type = 'hidden';
				input.name = `_${ VLS_DOMAIN }_region`;
				form.append( input );
			}
			input.value = region;
		} );
}
