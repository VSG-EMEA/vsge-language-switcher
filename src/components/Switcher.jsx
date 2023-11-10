import { __ } from '@wordpress/i18n';

/**
 * A function that returns the text to display for a language switcher.
 *
 * @param {Object} props                 - The props object containing the displayAs and currentLanguage properties.
 * @param {string} props.displayAs       - The display style of the language switcher.
 * @param {Object} props.currentLanguage - The currently selected language object.
 * @return {string} The text to display for the language switcher.
 */
export default function Switcher( props ) {
	const { displayAs, currentLanguage } = props;
	switch ( displayAs ) {
		case 'modal':
			return (
				currentLanguage?.name ?? __( 'Language Switcher' )
			);
		case 'dropdown':
			return (
				currentLanguage?.name ?? __( 'Language Dropdown' )
			);
		default:
			return __( 'Language Switcher' );
	}
}
