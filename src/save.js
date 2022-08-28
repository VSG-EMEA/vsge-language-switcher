import { useBlockProps } from '@wordpress/block-editor';

export const Save = ( { attributes } ) => {
	return (
		<div { ...useBlockProps.save() }>
      Language Switcher
		</div>
	);
};
