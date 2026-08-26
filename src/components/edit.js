import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	ColorPalette,
	PanelBody,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { originalLanguageSwitcherIcon } from './original-language-switcher-icon';

const labels = {
	modal: __( 'Modal selector', 'vsge-language-switcher' ),
	dropdown: __( 'Language dropdown', 'vsge-language-switcher' ),
	dataset: __( 'Navigation dataset', 'vsge-language-switcher' ),
};

export const Edit = ( {
	attributes: {
		displayAs = 'modal',
		buttonIcon = '',
		iconColor = '',
		style = {},
	},
	setAttributes,
} ) => {
	const hasIcon = '' !== buttonIcon;
	const textStyle = {
		color: style?.color?.text,
		fontFamily: style?.typography?.fontFamily,
		fontSize: style?.typography?.fontSize,
		fontStyle: style?.typography?.fontStyle,
		fontWeight: style?.typography?.fontWeight,
		letterSpacing: style?.typography?.letterSpacing,
		lineHeight: style?.typography?.lineHeight,
		textDecoration: style?.typography?.textDecoration,
		textTransform: style?.typography?.textTransform,
	};

	return (
		<span { ...useBlockProps( { className: 'vls-editor-preview' } ) }>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Language switcher',
						'vsge-language-switcher'
					) }
					initialOpen
				>
					<SelectControl
						label={ __(
							'Block presentation',
							'vsge-language-switcher'
						) }
						value={ displayAs }
						onChange={ ( value ) =>
							setAttributes( { displayAs: value } )
						}
						options={ [
							{ value: 'modal', label: labels.modal },
							{ value: 'dropdown', label: labels.dropdown },
							{ value: 'dataset', label: labels.dataset },
						] }
					/>
					<ToggleControl
						label={ __(
							'Show language icon',
							'vsge-language-switcher'
						) }
						checked={ hasIcon }
						onChange={ ( value ) =>
							setAttributes( {
								buttonIcon: value ? 'original' : '',
							} )
						}
					/>
					{ hasIcon && (
						<ColorPalette
							label={ __(
								'Icon colour',
								'vsge-language-switcher'
							) }
							value={ iconColor }
							onChange={ ( value ) =>
								setAttributes( { iconColor: value || '' } )
							}
							clearable
						/>
					) }
				</PanelBody>
			</InspectorControls>
			{ hasIcon && (
				<span
					className="vls-editor-preview__icon"
					style={ { color: iconColor || undefined } }
					aria-hidden="true"
				>
					{ originalLanguageSwitcherIcon }
				</span>
			) }
			<span className="vls-editor-preview__text" style={ textStyle }>
				{ __( 'Language switcher', 'vsge-language-switcher' ) }
			</span>
		</span>
	);
};
