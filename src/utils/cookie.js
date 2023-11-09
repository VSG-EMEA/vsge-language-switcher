export function createCookie( name, value, expires, basePath, baseDomain ) {
	if ( expires ) {
		if ( typeof expires === 'number' ) {
			const date = new Date();
			date.setTime(
				date.getTime() + expires * 24 * 60 * 60 * 1000
			);
			expires = '; expires=' + date.toGMTString();
		} else {
			expires = '; expires=' + expires;
		}
	} else {
		expires = '; ';
	}
	document.cookie = `${ name }=${ value }${ expires }; path=${ basePath }; domain=${ baseDomain }`;
}

export function eraseCookie( name ) {
	createCookie( name, '', -1 );
}
