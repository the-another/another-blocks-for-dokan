# Store List Query Loop Pattern

## Overview

The Store List block has been transformed from a fixed-layout block with toggle options into a **flexible query loop pattern**. This gives users complete control over what fields are displayed and how they're arranged for each store in the list.

## What Changed

### Before (Old Pattern)
- Fixed layout with toggle options in the sidebar
- All stores displayed the same predefined fields
- Limited customization - could only show/hide fields
- Fields: Banner, Rating, Address, Phone, Open/Closed Status

### After (New Query Loop Pattern)
- Flexible InnerBlocks-based layout
- Users can add, remove, and arrange field blocks
- Full control over each store card's layout
- Can use core blocks (Group, Columns, Spacer, etc.) for advanced layouts

## Available Field Blocks

The following field blocks can be used inside the Store List query loop:

### 1. **Store Name** (`the-another/dokan-store-name`)
- Displays the vendor's store name
- Settings:
  - HTML Tag (h1-h6, p, div)
  - Link to Store (toggle)
- Supports: Typography, Colors, Spacing

### 2. **Store Avatar** (`the-another/dokan-store-avatar`)
- Displays the vendor's avatar/logo
- Settings:
  - Avatar Size (32-200px)
  - Link to Store (toggle)
- Supports: Spacing

### 3. **Store Rating** (`the-another/dokan-store-rating`)
- Displays star rating and review count
- Settings:
  - Show Review Count (toggle)
- Supports: Colors, Spacing

### 4. **Store Address** (`the-another/dokan-store-address`)
- Displays the store's physical address
- Settings:
  - Show Icon (toggle)
- Supports: Typography, Colors, Spacing

### 5. **Store Phone** (`the-another/dokan-store-phone`)
- Displays the store's phone number
- Settings:
  - Show Icon (toggle)
  - Make Clickable (tel: link)
- Supports: Typography, Colors, Spacing

### 6. **Store Status** (`the-another/dokan-store-status`)
- Displays open/closed status
- No settings
- Supports: Typography, Colors, Spacing

### 7. **Store Banner** (`the-another/dokan-store-banner`)
- Displays the store's banner image
- Settings:
  - Banner Height (100-500px)
  - Link to Store (toggle)
- Supports: Spacing

## How It Works

### Technical Architecture

1. **Context Provision**: The Store List block provides `dokan/vendorId` context to all inner blocks
2. **Query Loop**: For each vendor in the query, the inner blocks are rendered with that vendor's ID in context
3. **Field Blocks**: Each field block reads the `dokan/vendorId` from context and displays the appropriate data

### Block Hierarchy

```
Store List (Query Container)
├── Query Settings (perPage, orderBy, featured only)
├── Layout Settings (grid/list, columns)
└── InnerBlocks (Template for each store)
    ├── Store Avatar
    ├── Store Name
    ├── Store Rating
    ├── Store Address
    ├── Store Phone
    └── ... (any combination of field blocks)
```

## Usage Examples

### Example 1: Simple Card Layout (Default)
```
Store List
└── Store Avatar (80px)
└── Store Name (h3)
└── Store Rating (with count)
└── Store Address (with icon)
└── Store Phone (with icon)
```

### Example 2: Banner + Content Layout
```
Store List
└── Store Banner (200px height)
└── Group
    └── Store Avatar (60px)
    └── Store Name (h2)
    └── Store Rating
    └── Columns
        └── Column
            └── Store Address
        └── Column
            └── Store Phone
            └── Store Status
```

### Example 3: Minimal Layout
```
Store List
└── Store Name (h4, no link)
└── Store Rating (no count)
```

## Benefits

1. **Maximum Flexibility**: Users can create any layout they want
2. **Reusable Field Blocks**: Field blocks can be used in other contexts too
3. **Core Block Integration**: Can use core blocks (Group, Columns, Spacer) for advanced layouts
4. **Better UX**: Visual editing - see exactly what you're building
5. **Future-Proof**: Easy to add new field blocks without changing the query loop

## Migration Notes

### For Users
- Existing Store List blocks will need to be recreated with the new pattern
- The default template mimics the old layout for easy migration
- More customization options available now

### For Developers
- Field blocks use `usesContext: ['dokan/vendorId']` to access vendor data
- All field blocks have a `parent: ['the-another/dokan-store-list']` restriction
- Render functions check context first, then fall back to attributes

## File Structure

```
blocks/
├── store-list/              # Query loop container
│   ├── block.json          # Provides context
│   ├── index.js            # InnerBlocks implementation
│   └── render.php          # Loops through vendors
├── store-name/              # Field block
│   ├── block.json          # Uses context
│   ├── index.js
│   └── render.php
├── store-avatar/            # Field block
├── store-rating/            # Field block
├── store-address/           # Field block
├── store-phone/             # Field block
├── store-status/            # Field block
└── store-banner/            # Field block
```

## Next Steps

1. Test the new blocks in the WordPress editor
2. Create block patterns for common layouts
3. Add more field blocks as needed (e.g., Store Description, Store Products Count)
4. Consider adding a block variation system for quick layouts
5. Update documentation and user guides

## Technical Details

### Context System
- **Provider**: `store-list` block provides `dokan/vendorId` via `providesContext`
- **Consumers**: All field blocks declare `usesContext: ['dokan/vendorId']`
- **Rendering**: PHP render functions access context via `$block->context['dokan/vendorId']`

### InnerBlocks Template
The default template is defined in `store-list/index.js`:
```javascript
const TEMPLATE = [
  [ 'the-another/dokan-store-avatar', { size: 80 } ],
  [ 'the-another/dokan-store-name', { tagName: 'h3' } ],
  [ 'the-another/dokan-store-rating', { showCount: true } ],
  [ 'the-another/dokan-store-address', { showIcon: true } ],
  [ 'the-another/dokan-store-phone', { showIcon: true } ],
];
```

### Allowed Blocks
Field blocks + core layout blocks:
```javascript
const ALLOWED_BLOCKS = [
  'the-another/dokan-store-name',
  'the-another/dokan-store-avatar',
  'the-another/dokan-store-rating',
  'the-another/dokan-store-address',
  'the-another/dokan-store-phone',
  'the-another/dokan-store-status',
  'the-another/dokan-store-banner',
  'core/group',
  'core/columns',
  'core/column',
  'core/separator',
  'core/spacer',
];
```
