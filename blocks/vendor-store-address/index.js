/**
 * Store address block editor component.
 *
 * @package
 * @since 1.0.0
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

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
					title={ __( 'Settings', 'the-another-blocks-for-dokan' ) }
					initialOpen={ true }
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
					title={ __(
						'Map Links',
						'the-another-blocks-for-dokan'
					) }
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
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<p className="tanbfd--vendor-store-address__text">
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
				</p>
				{ mapLinks.length > 0 && (
					<ul className="tanbfd--vendor-store-address__map-links">
						{ mapLinks.map( ( link ) => (
							<li
								key={ link.key }
								className={ `tanbfd--vendor-store-address__map-link tanbfd--vendor-store-address__map-link--${ link.key }` }
							>
								<a
									href="#map-link-preview"
									onClick={ ( event ) =>
										event.preventDefault()
									}
								>
									{ link.label }
								</a>
							</li>
						) ) }
					</ul>
				) }
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
