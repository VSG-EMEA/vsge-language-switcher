/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

import './scss/admin.scss';
import { Edit } from './components/edit';
import { Save } from './components/save';
import { blockIcon } from './components/icons';

// Register the block
/// https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/

// The block configuration
import blockConfig from '../block.json';

registerBlockType( blockConfig.name, {
	...blockConfig,
	icon: blockIcon,
	edit: Edit,
	save: Save,
} );
