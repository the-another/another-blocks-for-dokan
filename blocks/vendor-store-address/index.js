/**
 * Store address block editor component.
 *
 * @package
 * @since 1.0.0
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import './style.scss';

/**
 * Format address object into a string.
 *
 * @param {Object} address Address object.
 * @return {string} Formatted address string.
 */
function formatAddress( address ) {
	if ( ! address ) {
		return __( 'No address available', 'dokan-blocks' );
	}

	const parts = [];

	if ( address.street_1 ) {
		parts.push( address.street_1 );
	}
	if ( address.street_2 ) {
		parts.push( address.street_2 );
	}

	const cityStateZip = [];
	if ( address.city ) {
		cityStateZip.push( address.city );
	}
	if ( address.state ) {
		cityStateZip.push( address.state );
	}
	if ( address.zip ) {
		cityStateZip.push( address.zip );
	}

	if ( cityStateZip.length > 0 ) {
		parts.push( cityStateZip.join( ', ' ) );
	}

	if ( address.country ) {
		parts.push( address.country );
	}

	return parts.length > 0
		? parts.join( ', ' )
		: __( 'No address available', 'dokan-blocks' );
}

/**
 * Store address block edit component.
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
		showGoogleMaps = false,
		showAppleMaps = false,
		showOpenStreetMap = false,
		buttonStyle = '',
		buttonSize = '',
		showLabel = true,
		label = 'Address',
	} = attributes;
	const vendor = context[ 'dokan/vendor' ] || {};

	const address = vendor.address || {};
	const formattedAddress = formatAddress( address );

	const mapLinks = [
		showGoogleMaps && {
			key: 'google',
			label: __( 'Google Maps', 'the-another-blocks-for-dokan' ),
		},
		showAppleMaps && {
			key: 'apple',
			label: __( 'Apple Maps', 'the-another-blocks-for-dokan' ),
		},
		showOpenStreetMap && {
			key: 'osm',
			label: __( 'OpenStreetMap', 'the-another-blocks-for-dokan' ),
		},
	].filter( Boolean );

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
							'Show Icon',
							'the-another-blocks-for-dokan'
						) }
						help={ __(
							'Display a location icon before the address.',
							'the-another-blocks-for-dokan'
						) }
						checked={ showIcon }
						onChange={ ( value ) =>
							setAttributes( { showIcon: value } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Map Links', 'the-another-blocks-for-dokan' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __(
							'Google Maps',
							'the-another-blocks-for-dokan'
						) }
						help={ __(
							'Show a link that opens the address in Google Maps.',
							'the-another-blocks-for-dokan'
						) }
						checked={ showGoogleMaps }
						onChange={ ( value ) =>
							setAttributes( { showGoogleMaps: value } )
						}
					/>
					<ToggleControl
						label={ __(
							'Apple Maps',
							'the-another-blocks-for-dokan'
						) }
						help={ __(
							'Show a link that opens the address in Apple Maps.',
							'the-another-blocks-for-dokan'
						) }
						checked={ showAppleMaps }
						onChange={ ( value ) =>
							setAttributes( { showAppleMaps: value } )
						}
					/>
					<ToggleControl
						label={ __(
							'OpenStreetMap',
							'the-another-blocks-for-dokan'
						) }
						help={ __(
							'Show a link that opens the address in OpenStreetMap.',
							'the-another-blocks-for-dokan'
						) }
						checked={ showOpenStreetMap }
						onChange={ ( value ) =>
							setAttributes( { showOpenStreetMap: value } )
						}
					/>
					<SelectControl
						label={ __(
							'Button Style',
							'the-another-blocks-for-dokan'
						) }
						value={ buttonStyle }
						options={ [
							{
								label: __(
									'Default',
									'the-another-blocks-for-dokan'
								),
								value: '',
							},
							{
								label: __(
									'Outline',
									'the-another-blocks-for-dokan'
								),
								value: 'outline',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { buttonStyle: value } )
						}
					/>
					<SelectControl
						label={ __(
							'Button Size',
							'the-another-blocks-for-dokan'
						) }
						value={ buttonSize }
						options={ [
							{
								label: __(
									'Default',
									'the-another-blocks-for-dokan'
								),
								value: '',
							},
							{
								label: __(
									'Small',
									'the-another-blocks-for-dokan'
								),
								value: 'small',
							},
							{
								label: __(
									'Medium',
									'the-another-blocks-for-dokan'
								),
								value: 'medium',
							},
							{
								label: __(
									'Large',
									'the-another-blocks-for-dokan'
								),
								value: 'large',
							},
							{
								label: __(
									'Extra Large',
									'the-another-blocks-for-dokan'
								),
								value: 'x-large',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { buttonSize: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<dl>
					{ showLabel && label && (
						<dt className="tanbfd--vendor-store-address__label">
							{ label }
						</dt>
					) }
					<dd className="tanbfd--vendor-store-address__value">
						<span className="tanbfd--vendor-store-address__text">
							{ showIcon && (
								<span
									className="dokan-vendor-store-address-icon"
									aria-hidden="true"
								>
									📍
								</span>
							) }
							<span className="dokan-vendor-store-address-text">
								{ formattedAddress }
							</span>
						</span>
						{ mapLinks.length > 0 && (
							<div className="wp-block-buttons tanbfd--vendor-store-address__map-links">
								{ mapLinks.map( ( link ) => {
									const wrapperClasses = [
										'wp-block-button',
										'tanbfd--vendor-store-address__map-link',
										`tanbfd--vendor-store-address__map-link--${ link.key }`,
									];
									if ( buttonStyle ) {
										wrapperClasses.push(
											`is-style-${ buttonStyle }`
										);
									}

									const linkClasses = [
										'wp-block-button__link',
										'wp-element-button',
									];
									if ( buttonSize ) {
										linkClasses.push(
											`has-${ buttonSize }-font-size`
										);
									}

									return (
										<div
											key={ link.key }
											className={ wrapperClasses.join(
												' '
											) }
										>
											<a
												className={ linkClasses.join(
													' '
												) }
												href="#map-link-preview"
												onClick={ ( event ) =>
													event.preventDefault()
												}
											>
												{ link.label }
											</a>
										</div>
									);
								} ) }
							</div>
						) }
					</dd>
				</dl>
			</div>
		</>
	);
}

/**
 * Store address block save component.
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
