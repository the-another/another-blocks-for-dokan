/**
 * Store website block editor component.
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
 * Store website block edit component.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update attributes.
 * @param {Object}   props.context       Block context.
 * @return {JSX.Element} Block edit component.
 */
function Edit( { attributes, setAttributes, context } ) {
	const { showIcon = true, openInNewTab = true } = attributes;
	const vendor = context[ 'dokan/vendor' ] || {};

	const website = vendor.website || __( 'No website set', 'the-another-blocks-for-dokan' );
	const hasWebsite = Boolean( vendor.website );

	const blockProps = useBlockProps();

	const websiteContent = (
		<>
			{ showIcon && (
				<span
					className="dashicons dashicons-admin-links"
					aria-hidden="true"
				/>
			) }
			<span className="tanbfd--vendor-store-website-url">
				{ website }
			</span>
		</>
	);

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Settings', 'the-another-blocks-for-dokan' ) }
					initialOpen={ true }
				>
					<ToggleControl
						label={ __( 'Show Icon', 'the-another-blocks-for-dokan' ) }
						help={ __(
							'Display a link icon before the URL.',
							'the-another-blocks-for-dokan'
						) }
						checked={ showIcon }
						onChange={ ( value ) =>
							setAttributes( { showIcon: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Open in new tab', 'the-another-blocks-for-dokan' ) }
						help={ __(
							'Open the website link in a new browser tab.',
							'the-another-blocks-for-dokan'
						) }
						checked={ openInNewTab }
						onChange={ ( value ) =>
							setAttributes( { openInNewTab: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ hasWebsite ? (
					<a
						href={ vendor.website }
						onClick={ ( e ) => e.preventDefault() }
					>
						{ websiteContent }
					</a>
				) : (
					websiteContent
				) }
			</div>
		</>
	);
}

/**
 * Store website block save component.
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
