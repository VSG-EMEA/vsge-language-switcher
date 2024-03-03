/**
 * Creates a cookie with the specified name, value, expiration, base path, and base domain.
 *
 * @param {string}        name       - The name of the cookie
 * @param {string}        value      - The value of the cookie
 * @param {number|string} expires    - The expiration time in days or a specific date string
 * @param {string}        basePath   - The base path for the cookie
 * @param {string}        baseDomain - The base domain for the cookie
 * @return {void}
 */
export function createCookie( name: string, value: string, expires: any, basePath?: string, baseDomain?: string ): void {
	if ( expires ) {
		if ( typeof expires === 'number' ) {
			const date = new Date();
			date.setTime(
				date.getTime() + ( expires * 24 * 60 * 60 * 1000 )
			);
			//  the Date.prototype.toGMTString() method was deprecated in ES3
			//  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Deprecated_and_obsolete_features#date
			expires = '; expires=' + date.toUTCString();
		} else {
			expires = '; expires=' + expires;
		}
	} else {
		expires = '; ';
	}
	let newCookie = `${ name }=${ value }${ expires }`;
	if ( newCookie && basePath ) {
		newCookie += `; path=${ basePath }`;
	}
	if ( newCookie && baseDomain ) {
		newCookie += `; domain=${ baseDomain }`;
	}
	document.cookie += newCookie;
}

/**
 * Function to erase a specific cookie.
 *
 * @param {string} name - the name of the cookie to be erased
 * @return {void}
 */
export function eraseCookie( name: string ) {
	createCookie( name, '', -1 );
}
