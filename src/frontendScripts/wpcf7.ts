import { VLS_DOMAIN } from '../constants';

/**
 * It creates a hidden input field with a name and value
 *
 * @param {string} key      - the name of the field
 * @param {string} value    - The value of the field.
 * @param {string} [prefix] - The prefix for the field name.
 *
 * @return {HTMLElement} A new input element with the type of hidden, name of the key, and value of the value.
 */
const createFormHiddenField = (
	key: string,
	value: string | object,
	prefix: string = VLS_DOMAIN
): HTMLElement => {
	const e = document.createElement( 'input' );
	e.setAttribute( 'type', 'hidden' );
	e.setAttribute( 'name', '_' + prefix + key );
	e.setAttribute(
		'value',
		typeof value === 'string' ? value : JSON.stringify( value )
	);
	return e;
};

/**
 * The function appends a hidden input field with the name "_region" and the value of the "region"
 * parameter to all forms on the page with the class "wpcf7".
 *
 * @param {string} region - The "region" parameter is a string that represents the region value that
 *                        you want to append to the CF7 form fields.
 */
export function appendCF7Afield( region: string ) {
	// get all the form of the page
	const wpcf7Forms = document.querySelectorAll( '.localized-form .wpcf7' );

	if ( wpcf7Forms ) {
		for ( const wpcf7Form of wpcf7Forms ) {
			const hiddenInputsContainer =
				wpcf7Form.querySelector( 'form > div' );

			if ( hiddenInputsContainer ) {
				hiddenInputsContainer.append(
					createFormHiddenField( '_region', region )
				);
			}
		}
	}
}
