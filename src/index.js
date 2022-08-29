/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
// eslint-disable-next-line prettier/prettier
export const blockIcon = ( <svg xmlns="http://www.w3.org/2000/svg" width="282.73" height="316.04" viewBox="0 0 282.7 316"><path fill="#EF4136" d="M160.2 153.4c0 30.6-4.7 43-19.9 67.1-8 12.7 27.7 26.1 29.3 41.1v52.2c0 .2 0 2.1-2 2.1-4 .2-5.1.1-9 .1A158 158 0 1 1 169.5.3c6 .4 8.2.8 9.2 1.2l1.5.7c1 .5 3.5 4.4 3 9.3-5.2 38.8-37.6 34.2-24.8 62.6 8.8 19.6 2.1 56 2.1 79l-.3.3z"/><path fill="#D1D3D4" d="M158.8 169.4c4.4 1 12 3.1 24.6 6.5 15 4 33.3 14.7 48.8 19 16 4.3 21.2 5 40 4a111.5 111.5 0 0 1-92.8 63.9c-3 .1-5 .3-8 .2-57.6-1-103.6-42.6-106.6-99.7-2.6-49.5 41.7-119.5 102.5-122a110.7 110.7 0 0 1 105.3 65.6c0 .1 21.3 42 1.7 88l-.3-.5c-.8-2.4-7.5-20.3-13.8-35.5-9.3-22.5-25.5-28.3-25.7-28.3-7.8 1-29-12.5-36-10.8-24.8 6.2-42.8 49-39.6 49.7z"/><path fill="#414042" d="M160.4 170.2s25.8 9 29.6 10.5c11.4 4.6 25.3 23 32.1 24 8 1.1 50.3-5.6 50.3-5.6s-9.6 23.5-33 41a110.9 110.9 0 0 1-70.3 22.6c-44.1-1.2-3.8-65-3.8-65l-10-24m2.3-4.3a50 50 0 0 1 11.6-24.2c9.7-12.6 14.3-17.9 21-22 9.5-5.7 18.3-.5 25.5 2.7a74.4 74.4 0 0 0 18.3 5.6c4.9 1 17.2 13.7 17.2 13.7s-8-23-16.7-25.9c-8.6-2.9-34.2-17.3-47.5-14.2a110 110 0 0 0-31.9 13.2c-2 1.5-7.5 6.8-7.2 10.7.4 3.9 9.8 40.3 9.8 40.3v.1z"/><path fill="#EFEFEF" d="M169.1 142.4c-.4 60-2.2 59.6.4 120 0 .4.1.5-.2.5h-1.8a111 111 0 0 1-.7-221.6h2.8c-.6 20.2 0 41-.5 101.5v-.3z"/><path fill="#FFF" d="M164.8 118.2c.7 23.7-18 43.3-42 43.9-24 .5-44-18.2-44.7-41.9a42.7 42.7 0 0 1 42-43.8c24-.5 44 18.2 44.7 42z"/><path d="M157.2 115.3c.4 14.2-10.9 26-25.2 26.3a26 26 0 0 1-26.8-25 25.5 25.5 0 0 1 25.1-26.4 26.1 26.1 0 0 1 26.9 25.1z"/><path fill="#FFF" d="M151 110.6c.3 7-5.3 13-12.5 13.1a13 13 0 0 1-13.4-12.5c-.2-7.1 5.4-13 12.6-13.2a13 13 0 0 1 13.4 12.6z"/><path fill="#EFEFEF" d="M167.5 41.1h2v221.7h-2V41.1z"/><path fill="#EF4136" d="M168.7 263.3h.8V314h-.8v-50.8z"/></svg> );

// The block configuration
const blockConfig = require( './block.json' );

import { Edit } from './edit';
import { Save } from './save';

// Register the block
/// https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
registerBlockType( blockConfig.name, {
	...blockConfig,
	icon: blockIcon,
	apiVersion: 2,
	edit: Edit,
	save: Save,
	// https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/
	supports: {
		align: true,
		anchor: true,
		className: true,
		color: {
			background: true,
			link: true,
			text: true,
			gradients: true,
		},
		spacing: {
			margin: true, // Enable margin UI control.
			padding: true, // Enable padding UI control.
			blockGap: true, // Enables block spacing UI control.
		},
	},
  attributes: {
		style: {
			type: 'object',
			default: {
				color: {
					background: 'transparent',
					text: '#000000',
					link: '#00A0D2',
				},
				spacing: {
					padding: {
						top: '4px',
						left: '8px',
						bottom: '4px',
						right: '8px',
					},
				},
			},
		},
    displayAs: {
      type: 'string',
      default: 'modal',
    },
    buttonIcon: {
      type: 'string',
      default: '<svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path d="M12 22q-2.05 0-3.875-.788-1.825-.787-3.187-2.15-1.363-1.362-2.15-3.187Q2 14.05 2 12q0-2.075.788-3.887.787-1.813 2.15-3.175Q6.3 3.575 8.125 2.787 9.95 2 12 2q2.075 0 3.887.787 1.813.788 3.175 2.151 1.363 1.362 2.15 3.175Q22 9.925 22 12q0 2.05-.788 3.875-.787 1.825-2.15 3.187-1.362 1.363-3.175 2.15Q14.075 22 12 22Zm0-2.05q.65-.9 1.125-1.875T13.9 16h-3.8q.3 1.1.775 2.075.475.975 1.125 1.875Zm-2.6-.4q-.45-.825-.787-1.713Q8.275 16.95 8.05 16H5.1q.725 1.25 1.812 2.175Q8 19.1 9.4 19.55Zm5.2 0q1.4-.45 2.487-1.375Q18.175 17.25 18.9 16h-2.95q-.225.95-.562 1.837-.338.888-.788 1.713ZM4.25 14h3.4q-.075-.5-.113-.988Q7.5 12.525 7.5 12t.037-1.012q.038-.488.113-.988h-3.4q-.125.5-.188.988Q4 11.475 4 12t.062 1.012q.063.488.188.988Zm5.4 0h4.7q.075-.5.113-.988.037-.487.037-1.012t-.037-1.012q-.038-.488-.113-.988h-4.7q-.075.5-.112.988Q9.5 11.475 9.5 12t.038 1.012q.037.488.112.988Zm6.7 0h3.4q.125-.5.188-.988Q20 12.525 20 12t-.062-1.012q-.063-.488-.188-.988h-3.4q.075.5.112.988.038.487.038 1.012t-.038 1.012q-.037.488-.112.988Zm-.4-6h2.95q-.725-1.25-1.813-2.175Q16 4.9 14.6 4.45q.45.825.788 1.712.337.888.562 1.838ZM10.1 8h3.8q-.3-1.1-.775-2.075Q12.65 4.95 12 4.05q-.65.9-1.125 1.875T10.1 8Zm-5 0h2.95q.225-.95.563-1.838.337-.887.787-1.712Q8 4.9 6.912 5.825 5.825 6.75 5.1 8Z"/></svg>',
    },
	},
} );
