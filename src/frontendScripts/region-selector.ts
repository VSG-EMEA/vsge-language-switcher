import { VlsRegionGroup } from '../constants';

export const readRegionModel = (): VlsRegionGroup[] => {
	const element = document.querySelector< HTMLElement >(
		'[data-vls-region-model]'
	);
	if ( ! element?.dataset.vlsRegionModel ) {
		return [];
	}
	try {
		const model = JSON.parse( element.dataset.vlsRegionModel );
		return Array.isArray( model ) ? model : [];
	} catch {
		return [];
	}
};

export function hideClassesByRegion(
	region: string,
	groups: VlsRegionGroup[]
) {
	const selected = new Set(
		groups.flatMap( ( group ) =>
			group.entries
				.filter(
					( entry ) =>
						entry.type === 'internal' &&
						typeof entry.region === 'string' &&
						entry.region.toLowerCase() === region.toLowerCase()
				)
				.map( ( entry ) => `show-in--${ group.id }-${ entry.region }` )
		)
	);
	if ( selected.size === 0 ) {
		return;
	}
	document
		.querySelectorAll< HTMLElement >( '[class*="show-in--"]' )
		.forEach( ( element ) => {
			const visibilityClasses = Array.from( element.classList ).filter(
				( className ) => className.startsWith( 'show-in--' )
			);
			if (
				visibilityClasses.length &&
				! visibilityClasses.some( ( className ) =>
					selected.has( className )
				)
			) {
				element.hidden = true;
			}
		} );
}
