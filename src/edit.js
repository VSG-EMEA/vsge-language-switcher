import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import {Panel, PanelBody, PanelRow, SelectControl, TextControl} from '@wordpress/components';

export const Edit = ( { style, attributes: {displayAs}, setAttributes } ) => {
	return (
    <div
      { ...useBlockProps( {
        style: { style },
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
                onChange={ ( e ) =>
                  setAttributes( {
                    displayAs: e
                  } )
                 }
                options={ [
                  { value: 'modal', label: 'Modal Window' },
                  { value: 'dropdown', label: 'Dropdown' },
                ] }
              ></SelectControl>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
			<p>Language Switcher</p>
		</div>
	);
};
