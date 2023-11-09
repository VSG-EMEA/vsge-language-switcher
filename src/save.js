import { useBlockProps } from '@wordpress/block-editor';
import { defaultIcon } from './index';
import { VLS_CLASSNAME } from './constants';

export const Save = ( { style, attributes: { buttonIcon } } ) => {
	const blockProps = useBlockProps.save( {
		className: VLS_CLASSNAME,
		style,
	} );

	return (
		<button { ...blockProps }>
			<i
				dangerouslySetInnerHTML={ {
					__html: buttonIcon !== '' ? buttonIcon : defaultIcon,
				} }
			></i>
			[pls_switcher]
		</button>
	);
};
