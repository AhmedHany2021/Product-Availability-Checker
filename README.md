# Product Availability Checker

A WordPress plugin that allows WooCommerce store owners to manage product availability by ZIP code. Customers can check if products are available for delivery in their area before adding items to their cart.

## Description

Product Availability Checker enables you to:

- **Manage ZIP Code Availability**: Add, edit, and delete ZIP codes with availability status
- **Custom Messages**: Set custom messages for each ZIP code to inform customers about delivery options
- **Frontend Integration**: Display a ZIP code checker on product pages
- **Smart Cart Control**: Automatically disable "Add to Cart" button when delivery is unavailable
- **User-Friendly Interface**: Simple admin interface integrated into WooCommerce settings

Perfect for businesses that need to restrict delivery to specific areas or want to provide clear delivery information to customers.

## Features

- ✅ **ZIP Code Management**: Full CRUD operations for ZIP codes
- ✅ **Custom Messages**: Add personalized messages per ZIP code
- ✅ **AJAX-Powered**: Fast, seamless ZIP code checking without page reloads
- ✅ **Pagination Support**: Efficient handling of large ZIP code lists
- ✅ **Cookie Support**: Remembers customer's ZIP code for future visits
- ✅ **WooCommerce Integration**: Native integration with WooCommerce settings
- ✅ **Responsive Design**: Works on all devices
- ✅ **Performance Optimized**: Uses WordPress transients for fast lookups
- ✅ **Security First**: Capability checks, input validation, and sanitization
- ✅ **Translation Ready**: Fully internationalized

## Requirements

- **WordPress**: 5.8 or higher
- **WooCommerce**: 5.0 or higher
- **PHP**: 7.4 or higher

## Installation

### From WordPress Admin

1. Download the plugin ZIP file
2. Go to **Plugins → Add New → Upload Plugin**
3. Choose the ZIP file and click **Install Now**
4. Activate the plugin
5. Go to **WooCommerce → Settings → Product Availability** to configure

### Manual Installation

1. Upload the `product-availability-checker` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Go to **WooCommerce → Settings → Product Availability** to configure

### Via WP-CLI

```bash
wp plugin install product-availability-checker --activate
```

## Usage

### Admin Configuration

1. Navigate to **WooCommerce → Settings → Product Availability**
2. **Add New ZIP Code**:
   - Enter the ZIP code
   - Select availability status (Available/Unavailable)
   - Optionally add a custom message
   - Click **Add ZIP Code**

3. **Manage Existing ZIP Codes**:
   - Edit status using the dropdown
   - Update custom messages in the textarea
   - Click **Save** to update
   - Click **Delete** to remove a ZIP code

4. Use pagination to navigate through large lists of ZIP codes

### Frontend Experience

1. On single product pages, customers will see a ZIP code checker form
2. Customers enter their ZIP code and click **Check**
3. The system displays:
   - ✅ **Available**: Delivery is available (custom message shown if set)
   - ❌ **Unavailable**: Delivery is not available (custom message shown if set)
4. If unavailable, the "Add to Cart" button is automatically disabled
5. The ZIP code is saved in a cookie for future visits

### Custom Messages

Custom messages are displayed to customers when they check availability. Examples:

- **Available**: "Free delivery in your area! Orders arrive within 2-3 business days."
- **Unavailable**: "We currently don't deliver to this area. Please contact us for alternative arrangements."

## Screenshots

### Admin Interface
The plugin adds a new tab in WooCommerce Settings where you can manage all ZIP codes.

### Frontend Checker
Customers can check availability directly on product pages before adding items to cart.

## Development

### File Structure

```
product-availability-checker/
├── assets/
│   ├── admin/
│   │   ├── css/
│   │   └── js/
│   └── front/
│       ├── css/
│       └── js/
├── includes/
│   ├── autoloader.php
│   ├── class-admin.php
│   ├── class-front.php
│   ├── class-pac-plugin.php
│   └── class-zip-data.php
├── templates/
│   ├── admin/
│   └── front/
├── index.php
├── product-availability-checker.php
└── README.md
```

### Hooks and Filters

The plugin uses standard WordPress and WooCommerce hooks. Custom hooks may be added in future versions.

### Data Storage

ZIP code data is stored in WordPress options table using `get_option()` and `update_option()`. The plugin uses transients for performance optimization.
