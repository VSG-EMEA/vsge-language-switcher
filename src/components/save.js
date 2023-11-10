import { useBlockProps } from '@wordpress/block-editor';
import { VLS_CLASSNAME } from '../constants';
import Shortcode from './Shortcode';
import { defaultIcon } from './icons';

export const Save = ( { style, attributes: { buttonIcon, displayAs } } ) => {
	const blockProps = useBlockProps.save( {
		className: VLS_CLASSNAME,
		style,
	} );

	if ( displayAs === 'dataset' ) {
		return <Shortcode displayAs={ displayAs } />;
	}

	return (
		<button { ...blockProps }>
			<i
				dangerouslySetInnerHTML={ {
					__html: buttonIcon !== '' ? buttonIcon : defaultIcon,
				} }
			></i>
			<Shortcode displayAs={ displayAs } />
		</button>
	);
};
