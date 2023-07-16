import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import {
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { defaultIcon } from './index';

export const Edit = ( props ) => {
	const {
		attributes: { displayAs, buttonIcon, style },
		setAttributes,
	} = props;

	return (
		<button
			{ ...useBlockProps( {
				style,
			} ) }
		>
			<InspectorControls key="setting">
				<Panel header="Settings">
					<PanelBody
						title="Block Settings"
						icon={ 'settings' }
						initialOpen={ true }
					>
						<PanelRow>
							<SelectControl
								value={ displayAs }
								label={ __( 'Display as:' ) }
								onChange={ ( value ) =>
									setAttributes( {
										displayAs: value,
									} )
								}
								options={ [
									{ value: 'modal', label: 'Modal Window' },
									{ value: 'dropdown', label: 'Dropdown' },
								] }
							></SelectControl>
						</PanelRow>
						<PanelRow>
							<TextControl
								value={ buttonIcon }
								label={ __( 'SVG Icon (UNESCAPED!)' ) }
								onChange={ ( value ) =>
									setAttributes( {
										buttonIcon: value,
									} )
								}
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
			<>
				<i
					dangerouslySetInnerHTML={ {
						__html: buttonIcon !== '' ? buttonIcon : defaultIcon,
					} }
				></i>
				{ __( 'Language Switcher' ) }
			</>
		</button>
	);
};
