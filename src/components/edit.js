import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import {
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { select } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import Switcher from './Switcher';
import { defaultIcon } from './icons';

export const Edit = ( props ) => {
	const {
		attributes: { displayAs, buttonIcon, style },
		setAttributes,
	} = props;
	const [ currentLanguage, setCurrentLanguage ] = useState();

	/**
	 * Gets the default language.
	 *
	 * @return {Object} The default Language.
	 */
	function getDefaultLanguage() {
		const languages = select( 'pll/metabox' ).getLanguages();
		return Array.from( languages.values() ).find( ( lang ) => lang.active );
	}

	useEffect( () => {
		setCurrentLanguage( getDefaultLanguage() );
	}, [] );

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
									{ value: 'dataset', label: 'dataset' },
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
				<Switcher displayAs={ displayAs } currentLanguage={ currentLanguage } />
			</>
		</button>
	);
};
