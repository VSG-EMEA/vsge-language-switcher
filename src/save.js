import { useBlockProps } from '@wordpress/block-editor';

export const Save = ( { style, attributes: { buttonIcon } } ) => {
	const blockProps = useBlockProps.save( {
		style: style,
	} );

	return (
		<button { ...blockProps } id={ 'pls-language-switcher' }>
			<i dangerouslySetInnerHTML={ { __html: buttonIcon } }></i>
			[pls_switcher]
		</button>
	);
};
