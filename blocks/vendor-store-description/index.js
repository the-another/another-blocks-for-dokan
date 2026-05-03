/**
 * Store description block editor component.
 *
 * @package
 * @since 1.0.4
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

/**
 * Store description block edit component.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update attributes.
 * @param {Object}   props.context       Block context.
 * @return {JSX.Element} Block edit component.
 */
function Edit( { attributes, setAttributes, context } ) {
	const { allowHtml = true } = attributes;
	const vendor = context[ 'dokan/vendor' ] || {};

	const description =
		vendor.description ||
		__( 'No store description set.', 'the-another-blocks-for-dokan' );

	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Settings', 'the-another-blocks-for-dokan' ) }
					initialOpen={ true }
				>
					<ToggleControl
						label={ __( 'Allow HTML', 'the-another-blocks-for-dokan' ) }
						help={ __(
							'Render the description with safe HTML tags. Disable to display as plain text.',
							'the-another-blocks-for-dokan'
						) }
						checked={ allowHtml }
						onChange={ ( value ) =>
							setAttributes( { allowHtml: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>{ description }</div>
		</>
	);
}

/**
 * Store description block save component.
 *
 * @return {null} Always null for server-side blocks.
 */
function Save() {
	return null;
}

registerBlockType( metadata.name, {
	...metadata,
	edit: Edit,
	save: Save,
} );
