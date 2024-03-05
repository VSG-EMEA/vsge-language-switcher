import { VSG_ALLOWED_REGIONS } from '../constants';

/**
 * The function `getCookieValue` is a TypeScript function that retrieves the value of a cookie by its
 * name.
 *
 * @param name - The name parameter is a string that represents the name of the cookie whose value you
 *             want to retrieve.
 */
export const getCookieValue = ( name: string ) =>
	document.cookie.match( '(^|;)\\s*' + name + '\\s*=\\s*([^;]+)' )?.pop() ||
	'';

/**
 * The function generates a string of CSS class selectors to hide regions based on a given region and a
 * list of allowed regions.
 *
 * @param {string}   region         - The `region` parameter is a string that represents the current region. It
 *                                  is used to determine which region classes should be shown or hidden.
 * @param {string[]} regionsAllowed - An array of strings representing the regions that are allowed to
 *                                  be shown.
 * @return a string that represents a CSS selector.
 */
function generateRegionClassesToHide(
	region: string,
	regionsAllowed: string[]
) {
	const classesToShow = regionsAllowed.map( ( regionClassname ) =>
		regionClassname !== region
			? '.region-show-' + regionClassname
			: '.region-show-default'
	);

	return classesToShow.join( ', ' );
}

/**
 * The function "hideClassesByRegion" hides HTML elements based on the specified region.
 *
 * @param region - The "region" parameter is a string that represents the region for which classes
 *               should be hidden.
 */
export function hideClassesByRegion( region: string ) {
	// get all the classes to hide
	const classesToHide = generateRegionClassesToHide(
		region,
		VSG_ALLOWED_REGIONS
	);

	// hide the regions that aren't needed
	const elementsToHide: NodeListOf< HTMLElement > =
		document.querySelectorAll( classesToHide );

	elementsToHide.forEach( ( element ) => {
		element.style.display = 'none';
	} );
}
