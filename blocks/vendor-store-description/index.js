/**
 * Store description block editor component.
 *
 * @package
 * @since 1.0.4
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import './style.scss';

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
	const {
		allowHtml = true,
		showLabel = true,
		label = 'Description',
	} = attributes;
	const vendor = context[ 'dokan/vendor' ] || {};

	const description =
		vendor.description ||
		__( 'No store description set.', 'the-another-blocks-for-dokan' );

	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Label Settings',
						'the-another-blocks-for-dokan'
					) }
					initialOpen={ true }
				>
					<ToggleControl
						label={ __(
							'Show label',
							'the-another-blocks-for-dokan'
						) }
						checked={ showLabel }
						onChange={ ( value ) =>
							setAttributes( { showLabel: value } )
						}
					/>
					{ showLabel && (
						<TextControl
							label={ __(
								'Label',
								'the-another-blocks-for-dokan'
							) }
							value={ label }
							onChange={ ( value ) =>
								setAttributes( { label: value } )
							}
						/>
					) }
				</PanelBody>
				<PanelBody
					title={ __( 'Settings', 'the-another-blocks-for-dokan' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __(
							'Allow HTML',
							'the-another-blocks-for-dokan'
						) }
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

			<div { ...blockProps }>
				<dl>
					{ showLabel && label && (
						<dt className="tanbfd--vendor-store-description__label">
							{ label }
						</dt>
					) }
					<dd className="tanbfd--vendor-store-description__value">
						{ description }
					</dd>
				</dl>
			</div>
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
