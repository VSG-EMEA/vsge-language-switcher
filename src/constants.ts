/** the class name for the language switcher block */
export const VLS_CLASSNAME: string = 'wp-block-vsge-language-switcher';

/** the language switcher domain (eg. vsge) */
export const VLS_DOMAIN: string = window.languageSwitcher?.namespace || 'vsge';

/** The allowed regions for the language switcher (eu) */
export const VSG_ALLOWED_REGIONS: Record<
	string,
	string | { [key: string]: string }
> = window.languageSwitcher.regions || {
	europe: { europe: 'Europe' },
};

/** The modal windows elements */
export interface ModalElements {
	selector: HTMLElement | null;
	overlayWrapper: HTMLElement | null;
	languageSelect: HTMLSelectElement | null;
	regionSelect: HTMLSelectElement | null;
	languageSwitcherButton: HTMLElement | null;
	closeButton: HTMLElement | null;
}

/** The language switcher globals */
export interface languageSwitcherGlobs {
	/** the language switcher domain */
	siteurl: string;
	/** the allowed regions for the language switcher */
	regions: typeof VSG_ALLOWED_REGIONS;
	/** the cookie path for the language switcher link  */
	cookiePath: string;
	/** the cookie domain */
	cookieDomain: string;
	/** the language switcher name */
	namespace: string;
}

/** The language switcher cookie duration */
export const PLS_COOKIE_DURATION = 'Session';
