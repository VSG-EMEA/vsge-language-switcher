/**
 * Returns the shortcode based on the displayAs value.
 *
 * @param {string} displayAs - The value indicating how the shortcode should be displayed.
 * @return {string} The shortcode corresponding to the displayAs value.
 */
const getShortcode = ( displayAs ) => {
	switch ( displayAs ) {
		case 'modal':
			return '[vls_switcher]';
		case 'dataset':
			return '[vls_dataset]';
		default:
			return '{[vls_list]}';
	}
};
/**
 * Generates a shortcode based on the provided props.
 *
 * @param {Object} props - The props object containing the displayAs property.
 * @return {string} The generated shortcode.
 */
export default function Shortcode( props ) {
	return getShortcode( props.displayAs );
}
