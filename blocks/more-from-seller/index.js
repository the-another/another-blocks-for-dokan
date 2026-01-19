/**
 * More from seller block editor component.
 *
 * @package DokanBlocks
 * @since 1.0.0
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

/**
 * More from seller block edit component.
 *
 * @return {JSX.Element} Block edit component.
 */
function Edit() {
	const blockProps = useBlockProps();

	return (
		< div { ...blockProps } >
			< div className = "dokan-more-from-seller-placeholder" >
				< h2 > { __( 'More from Seller', 'dokan-blocks' ) } < / h2 >
				< p > { __( 'This block will display more products from the same vendor.', 'dokan-blocks' ) } < / p >
			< / div >
		< / div >
	);
}

/**
 * More from seller block save component.
 *
 * @return {null} Always null for server-side blocks.
 */
function Save() {
	return null;
}

registerBlockType(
	metadata.name,
	{
		...metadata,
		edit: Edit,
		save: Save,
	}
);
