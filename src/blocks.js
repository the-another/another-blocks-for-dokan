/**
 * Block entry point for webpack build.
 *
 * This file imports all block editor JavaScript files for building.
 * Blocks are registered here for the editor, but rendered server-side via PHP.
 *
 * @package DokanBlocks
 * @since 1.0.0
 */

// Import all block editor components (relative to src/).
import '../blocks/store-header/index.js';
import '../blocks/store-sidebar/index.js';
import '../blocks/store-tabs/index.js';
import '../blocks/store-terms-conditions/index.js';
import '../blocks/store-query-loop/index.js';
import '../blocks/store-query-pagination/index.js';
import '../blocks/store-card/index.js';
import '../blocks/store-search/index.js';

// Store field blocks (for use inside store-list query loop).
import '../blocks/store-name/index.js';
import '../blocks/store-avatar/index.js';
import '../blocks/store-rating/index.js';
import '../blocks/store-address/index.js';
import '../blocks/store-phone/index.js';
import '../blocks/store-status/index.js';
import '../blocks/store-banner/index.js';

import '../blocks/product-vendor-info/index.js';
import '../blocks/more-from-seller/index.js';
import '../blocks/store-contact-form/index.js';
import '../blocks/store-location/index.js';
import '../blocks/store-hours/index.js';
import '../blocks/become-vendor-cta/index.js';
