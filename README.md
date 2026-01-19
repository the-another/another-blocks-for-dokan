# Dokan Blocks

FSE-compatible Gutenberg blocks for Dokan multi-vendor marketplace. Convert Dokan templates into dynamic blocks for Full Site Editing.

## Requirements

- WordPress 6.0+
- WooCommerce 7.0+
- Dokan (latest stable version)
- PHP 8.3+

## Installation

1. Clone or download this plugin
2. Place it in your WordPress plugins directory
3. Install dependencies:
   ```bash
   composer install
   npm install
   ```
4. Build blocks:
   ```bash
   npm run build
   ```
5. Activate the plugin through WordPress admin

## Development

### Build Blocks

```bash
npm run build
```

### Watch for Changes

```bash
npm start
```

### Run Tests

```bash
composer test
```

### Run Code Standards Check

```bash
composer lint
```

### Fix Code Standards Issues

```bash
composer lint-fix
```

## Available Blocks

### Store Profile Blocks
- **Store Header** (`the-another/dokan-store-header`) - Display vendor store header
- **Store Products** (`the-another/dokan-store-products`) - Display vendor products
- **Store Sidebar** (`the-another/dokan-store-sidebar`) - Store sidebar with widgets
- **Store Tabs** (`the-another/dokan-store-tabs`) - Store navigation tabs

### Vendor Listing Blocks
- **Store List** (`the-another/dokan-store-list`) - Grid/list of vendor stores
- **Store Card** (`the-another/dokan-store-card`) - Individual vendor store card
- **Store Search** (`the-another/dokan-store-search`) - Search and filter stores

### Product Integration Blocks
- **Product Vendor Info** (`the-another/dokan-product-vendor-info`) - Vendor info on product pages
- **More from Seller** (`the-another/dokan-more-from-seller`) - More products from same vendor

### Account/Registration Blocks
- **Become Vendor CTA** (`the-another/dokan-become-vendor-cta`) - Call-to-action to become a vendor

### Widget Blocks
- **Store Contact Form** (`the-another/dokan-store-contact-form`) - Contact vendor form
- **Store Location** (`the-another/dokan-store-location`) - Store location map
- **Store Hours** (`the-another/dokan-store-hours`) - Store opening hours

## FSE Templates

The plugin provides FSE block templates:

- `templates/store.html` - Single vendor store page
- `templates/store-lists.html` - Vendor listing page

These templates automatically replace PHP templates in block themes.

## Testing

Tests run without requiring WordPress/Dokan installation using Brain Monkey and Mockery for mocking.

```bash
# Run all tests
composer test

# Run unit tests only
composer test:unit

# Run integration tests only
composer test:integration

# Generate coverage report
composer test:coverage
```

## License

GPL v2 or later
