export const VLS_CLASSNAME = 'wp-block-vsge-language-switcher';
export const VLS_DOMAIN = 'vsge';

export interface VlsLanguage {
	slug: string;
	name: string;
	url: string;
	locale?: string;
	current_lang?: boolean;
}

export interface VlsRegionEntry {
	id: string;
	label: string;
	type: 'internal' | 'external';
	region?: string;
	language?: string;
	external_url?: string;
}

export interface VlsRegionGroup {
	id: string;
	label: string;
	entries: VlsRegionEntry[];
}
