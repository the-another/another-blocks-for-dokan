/**
 * Store phone block editor component.
 *
 * @package
 * @since 1.0.0
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import './style.scss';

/**
 * Store phone block edit component.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update attributes.
 * @param {Object}   props.context       Block context.
 * @return {JSX.Element} Block edit component.
 */
function Edit( { attributes, setAttributes, context } ) {
	const {
		showIcon = true,
		isLink = true,
		showLabel = true,
		label = 'Phone',
	} = attributes;
	const vendor = context[ 'dokan/vendor' ] || {};

	const phone =
		vendor.phone || __( 'No phone number', 'the-another-blocks-for-dokan' );
	const hasPhone = Boolean( vendor.phone );

	const blockProps = useBlockProps();

	const phoneContent = (
		<>
			{ showIcon && (
				<span
					className="dokan-vendor-store-phone-icon"
					aria-hidden="true"
				>
					📞
				</span>
			) }
			<span className="dokan-vendor-store-phone-number">{ phone }</span>
		</>
	);

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
							'Show Icon',
							'the-another-blocks-for-dokan'
						) }
						help={ __(
							'Display a phone icon before the number.',
							'the-another-blocks-for-dokan'
						) }
						checked={ showIcon }
						onChange={ ( value ) =>
							setAttributes( { showIcon: value } )
						}
					/>
					<ToggleControl
						label={ __(
							'Make Clickable',
							'the-another-blocks-for-dokan'
						) }
						help={ __(
							'Make the phone number a clickable tel: link.',
							'the-another-blocks-for-dokan'
						) }
						checked={ isLink }
						onChange={ ( value ) =>
							setAttributes( { isLink: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<dl>
					{ showLabel && label && (
						<dt className="tanbfd--vendor-store-phone__label">
							{ label }
						</dt>
					) }
					<dd className="tanbfd--vendor-store-phone__value">
						{ isLink && hasPhone ? (
							<a
								href={ `tel:${ phone }` }
								onClick={ ( e ) => e.preventDefault() }
							>
								{ phoneContent }
							</a>
						) : (
							phoneContent
						) }
					</dd>
				</dl>
			</div>
		</>
	);
}

/**
 * Store phone block save component.
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
