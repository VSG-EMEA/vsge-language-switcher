import { useBlockProps } from '@wordpress/block-editor';
import { defaultIcon } from './index';

export const Save = ( { style, attributes: { buttonIcon } } ) => {
	const blockProps = useBlockProps.save( {
		style,
	} );

	return (
		<button { ...blockProps } id={ 'pls-language-switcher' }>
			<i
				dangerouslySetInnerHTML={ {
					__html: buttonIcon !== '' ? buttonIcon : defaultIcon,
				} }
			></i>
			[pls_switcher]
		</button>
	);
};
