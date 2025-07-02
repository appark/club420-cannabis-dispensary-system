# Club420 Technical Architecture Guide

> **🏗️ Deep Technical Overview** | **System Design & Component Interactions** | **For Developers**

This document explains the complete technical architecture of the Club420 multi-store cannabis dispensary system.

## 🏛️ **High-Level System Architecture**

```
┌─────────────────────────────────────────────────────────────────────┐
│                        club420.com (WordPress)                      │
│  ┌─────────────┐  ┌──────────────┐  ┌─────────────────────────────┐ │
│  │  Age Gate   │  │ Store Picker │  │    Content Visibility       │ │
│  │     +       │  │      +       │  │         System              │ │
│  │ localStorage│  │ localStorage │  │  (.davis-content/           │ │
│  │   Bridge    │  │    State     │  │   .dixon-content)           │ │
│  └─────────────┘  └──────────────┘  └─────────────────────────────┘ │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │              Real-Time Product Scheduling System               │ │
│  │        ┌──────────────┐  ┌─────────────┐  ┌─────────────┐      │ │
│  │        │   Admin UI   │  │   Backend   │  │  Frontend   │      │ │
│  │        │  (Snippet 2) │  │ (Snippet 1) │  │(Snippet 5) │      │ │
│  │        └──────────────┘  └─────────────┘  └─────────────┘      │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                     │                               │
│                                     ▼                               │
└─────────────────────────────────────────────────────────────────────┘
                                      │
                               [Proxy Pass]
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    Tymber/Blaze E-commerce System                   │
│                                                                     │
│  ┌─────────────────────────┐     ┌─────────────────────────────┐   │
│  │    Davis Store          │     │    Dixon Store              │   │
│  │  /menu/f-street/        │     │  /menu/highway-80/          │   │
│  │                         │     │                             │   │
│  │  Reads localStorage:    │     │  Reads localStorage:        │   │
│  │  - last-store-selected  │     │  - last-store-selected      │   │
│  │  - tymber-user-has-     │     │  - tymber-user-has-         │   │
│  │    allowed-age          │     │    allowed-age              │   │
│  └─────────────────────────┘     └─────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

## 🔄 **Data Flow & State Management**

### **localStorage as Cross-System Bridge**

The entire system relies on **localStorage** to maintain state across the WordPress ↔ Tymber boundary:

```javascript
// State Keys
localStorage['last-store-selected'] = 'store-uuid'
localStorage['tymber-user-has-allowed-age'] = 'true'

// Store ID Mapping
Davis:  '79043044-f024-4b70-8714-4fcad409f978' → /menu/f-street/
Dixon:  '7029749f-9c6d-419e-b037-5c1b566f3df9' → /menu/highway-80/
```

### **Complete User Journey Data Flow**

```
1. User → club420.com
   ├── Age Gate reads localStorage['tymber-user-has-allowed-age']
   ├── If null → Show age verification
   └── If 'true' → Skip to store selection

2. Age Verification
   ├── User clicks "YES, I AM 21"
   ├── localStorage['tymber-user-has-allowed-age'] = 'true'
   └── Proceed to store selection

3. Store Selection
   ├── Store Picker reads localStorage['last-store-selected']
   ├── If null → Show store selection modal
   ├── User selects Davis/Dixon
   ├── localStorage['last-store-selected'] = store_uuid
   ├── URL updated: ?store_filter=davis or ?store_filter=dixon
   └── Page reloads with store context

4. Content Adaptation
   ├── JavaScript reads localStorage['last-store-selected']
   ├── showStoreSectionsSmooth() function executes
   ├── Davis selected: .davis-content visible, .dixon-content hidden
   ├── Dixon selected: .dixon-content visible, .davis-content hidden
   └── Location pickers update to show current store

5. Menu Navigation
   ├── User clicks "FLOWER" in WordPress menu
   ├── JavaScript intercepts click (preventDefault)
   ├── Text detection: "FLOWER" → '/categories/flower/'
   ├── Store context: Davis → /menu/f-street/categories/flower/
   ├── Store context: Dixon → /menu/highway-80/categories/flower/
   └── Navigation to Tymber with store context

6. Tymber Integration
   ├── User arrives at /menu/f-street/ or /menu/highway-80/
   ├── Tymber reads localStorage['tymber-user-has-allowed-age']
   ├── If 'true' → No re-verification needed
   ├── Tymber reads localStorage['last-store-selected']
   └── Shows appropriate store inventory
```

## 🧩 **Component Architecture**

### **Backend PHP System (6 Snippets)**

#### **Snippet 1: Store Filter + Cache Exclusion**
```php
Purpose: Core product filtering and performance optimization

Key Functions:
- club420_realtime_filter_with_scheduling() → Filters WooCommerce queries
- club420_bypass_cache_for_scheduled_products() → WP Engine cache exclusion
- Detects ?store_filter=davis/dixon in URL
- Applies meta_query filters for store-specific products
- Bypasses cache when scheduled products are active

Integration Points:
- Hooks: woocommerce_shortcode_products_query
- Reads: $_GET['store_filter']
- Exception: Bypasses filtering when 'club420_carousel_query' = true
```

#### **Snippet 2: Product Scheduler Interface**
```php
Purpose: Admin UI for product scheduling with timezone handling

Key Functions:
- club420_add_enhanced_product_fields_with_scheduler() → Admin UI
- club420_save_enhanced_product_fields_with_scheduler() → Save handler
- club420_calculate_enabled_status() → Real-time status calculation
- Timezone conversion: Los Angeles ↔ UTC

Database Schema:
- _club420_davis_visibility → 'always'|'scheduled'|'disabled'
- _club420_dixon_visibility → 'always'|'scheduled'|'disabled'  
- _club420_davis_schedule_start → UTC timestamp
- _club420_davis_schedule_end → UTC timestamp
- _club420_dixon_schedule_start → UTC timestamp
- _club420_dixon_schedule_end → UTC timestamp
- _club420_davis_enabled → 'yes'|'no' (calculated)
- _club420_dixon_enabled → 'yes'|'no' (calculated)
```

#### **Snippet 3: Admin Store Filter**
```php
Purpose: Backend admin tools for product management

Key Functions:
- club420_add_inline_store_filter() → Admin dropdown
- club420_filter_products_by_store() → Admin query filtering
- club420_add_store_columns() → Status columns
- club420_display_store_columns() → Visual indicators

Admin Interface:
- Dropdown: "Davis Only", "Dixon Only", "Both Stores", "Neither Store"
- Columns: Davis (✓/✗), Dixon (✓/✗)
- Real-time filtering of products list
```

#### **Snippet 4: Auto Button Generator**
```php
Purpose: Dynamic product page button generation

Key Functions:
- club420_auto_generate_buttons() → Button HTML generation
- club420_get_current_store_cached() → Performance optimization
- club420_generate_deals_url() → Category-specific routing

Logic Flow:
1. Reads ?store_filter from URL
2. Gets product meta: _club420_davis_url, _club420_dixon_url
3. If Davis selected + Davis URL exists → Generate Davis buttons
4. If Dixon selected + Dixon URL exists → Generate Dixon buttons
5. Never shows both sets of buttons (prevents flash)
```

#### **Snippet 5: BlazeSlider Carousel**
```php
Purpose: Real-time product carousels with store filtering

Key Functions:
- club420_blazeslider_adaptation() → Shortcode handler
- club420_should_product_show() → Real-time visibility check
- Forces fresh data for non-logged-in users

Carousel Logic:
1. Sets 'club420_carousel_query' = true (bypasses Snippet 1)
2. Applies own store filtering based on ?store_filter
3. For each product: club420_should_product_show() real-time check
4. Only displays products that pass all filters
5. Integrates with YITH badges and product thumbnails
```

#### **Snippet 6: Scheduler Dashboard**
```php
Purpose: Live monitoring and system status

Key Functions:
- club420_scheduler_dashboard_shortcode() → [club420_scheduler_dashboard]
- calculate_store_status() → Real-time status per store
- get_products_with_schedules() → Database query for scheduled products

Dashboard Features:
- Live system status
- All scheduled products with current status
- Real-time activation countdown
- Admin-only access control
```

### **Frontend JavaScript System**

#### **Age Gate & Store Picker**
```javascript
// Location: Divi Body JavaScript

Components:
- showAgeGate() → Cannabis compliance modal
- verifyAge() → localStorage state management  
- selectStore() → Store selection and URL routing
- isBot() → Performance testing bypass

State Management:
- localStorage['tymber-user-has-allowed-age'] = 'true'
- localStorage['last-store-selected'] = store_uuid
- Persists across page loads and navigation
```

#### **Content Visibility System**
```javascript
// Core Function: showStoreSectionsSmooth()

Logic:
1. Read localStorage['last-store-selected']
2. Get all elements: .davis-content, .dixon-content, .davis-menu, .dixon-menu
3. Hide all store-specific elements
4. Show elements for selected store only
5. Handle Divi Builder detection (show all for editing)

CSS Classes:
.davis-content → Sections visible only to Davis users
.dixon-content → Sections visible only to Dixon users
.davis-menu → Menu items visible only to Davis users  
.dixon-menu → Menu items visible only to Dixon users
```

#### **Location Picker Components**

**Desktop Location Picker:**
```javascript
// Location: Divi Builder Header (Code Module)

Structure:
- Custom HTML component with black button design
- Elegant dropdown with Davis/Dixon grid
- State synchronization with localStorage
- Auto-hide on mobile devices

Integration:
- Calls global setStore() function
- Updates visual state immediately
- Triggers page reload with ?store_filter
```

**Mobile Location Picker:**
```javascript
// Location: Divi Body JavaScript + WordPress Menu

Nuclear Override Approach:
1. Find WordPress LOCATIONS menu item
2. Completely override Divi menu behavior:
   - preventDefault() 
   - stopPropagation()
   - stopImmediatePropagation()
3. Manual dropdown toggle: classList.toggle('open')
4. Text-based detection: find Davis/Dixon by link text
5. Force close dropdown after selection
6. Update button text to show current store

CSS Transformation:
- LOCATIONS styled as black button (matches desktop)
- Davis/Dixon in 2-column dropdown
- Yellow selection state for current store
```

#### **Menu Navigation System**
```javascript
// Text-Based Navigation Detection

Navigation Map:
'FLOWER' → '/categories/flower/'
'CARTRIDGES' → '/categories/cartridge/'
'EDIBLES' → '/categories/edible/'
'PRE-ROLLS' → '/categories/preroll/'
'EXTRACTS' → '/categories/extract/'
'SHOP ALL' → '/'

Integration Logic:
1. Intercept all menu link clicks
2. Check link text against navigation map
3. Get current store from localStorage
4. Build full URL: /menu/{store-slug}{category-path}
5. Navigate to Tymber with full context
```

#### **Store Dropdown Synchronization**
```javascript
// Auto-Detection Pattern: styled-store-select-*

Synchronization Logic:
1. Find all dropdowns with ID starting 'styled-store-select-'
2. Set initial values from localStorage
3. Add change event listeners to all dropdowns
4. When any dropdown changes:
   - Update all other dropdowns to match
   - Call setStoreSmoothReload()
   - Trigger page reload with store context
```

## ⚡ **Real-Time Scheduling Architecture**

### **Database Schema**
```sql
-- Product Meta Keys
_club420_davis_visibility     ENUM('always','scheduled','disabled')
_club420_dixon_visibility     ENUM('always','scheduled','disabled')
_club420_davis_schedule_start BIGINT (UTC timestamp)
_club420_davis_schedule_end   BIGINT (UTC timestamp)  
_club420_dixon_schedule_start BIGINT (UTC timestamp)
_club420_dixon_schedule_end   BIGINT (UTC timestamp)
_club420_davis_enabled        ENUM('yes','no') -- Calculated field
_club420_dixon_enabled        ENUM('yes','no') -- Calculated field
```

### **Real-Time Processing Flow**
```
1. Admin Interface (Snippet 2)
   ├── User sets schedule in Los Angeles timezone
   ├── JavaScript converts to UTC: new DateTime(input, wp_timezone())
   ├── Saves UTC timestamp to database
   └── Calculates current enabled status

2. Frontend Query (Snippet 1)
   ├── Gets current UTC time: current_time('timestamp', true)
   ├── Applies meta_query filter:
   │   WHERE (start_time <= current_utc)
   │   AND (end_time >= current_utc)
   └── Products auto-appear/disappear at exact times

3. Carousel Processing (Snippet 5)  
   ├── For each product: club420_should_product_show()
   ├── Direct database read (bypasses caching)
   ├── Real-time calculation: 
   │   current_utc >= start_time && current_utc <= end_time
   └── Dynamic product list updates

4. Cache Management
   ├── WP Engine cache exclusion for pages with active schedules
   ├── Cache-Control headers: no-cache, must-revalidate
   ├── Clean post cache when products are saved
   └── Force fresh queries for scheduled content
```

### **Timezone Handling**
```javascript
// Admin Display (Los Angeles timezone)
wp_timezone() → America/Los_Angeles
user_input → "2025-07-15 14:30"

// Database Storage (UTC)  
conversion → new DateTime(user_input, wp_timezone())
utc_timestamp → datetime.getTimestamp()

// Real-time Queries (UTC)
current_utc → current_time('timestamp', true)
comparison → current_utc >= start_timestamp && current_utc <= end_timestamp
```

## 🚀 **Performance Architecture**

### **Caching Strategy**
```php
// WP Engine Cache Exclusion
if (has_active_schedules() || isset($_GET['store_filter'])) {
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
}

// Conditional Script Loading
if (is_front_page() || is_shop() || is_product_category()) {
    add_action('wp_footer', 'club420_load_optimized_scripts', 20);
}

// Database Optimization
clean_post_cache($product_id);
wc_delete_product_transients($product_id);
```

### **JavaScript Performance**
```javascript
// Conditional Loading
window.addEventListener('DOMContentLoaded', function() {
    // Only initialize relevant components
    if (age_gate_needed) initializeAgeGate();
    if (location_picker_present) initializeLocationPicker();
    if (store_dropdowns_found) initializeStoreDropdowns();
});

// Event Cleanup
document.removeEventListener('click', closeOnClickOutside);

// Memory Management
const dropdowns = document.querySelectorAll('[id^="styled-store-select"]');
dropdowns.forEach(dropdown => /* process once */);
```

### **Bot Detection for Performance Testing**
```javascript
function isBot() {
    const botPatterns = [
        /pagespeed|lighthouse|gtmetrix/i,
        /googlebot|bingbot|slurp/i,
        /pingdom|uptime|monitor/i
    ];
    
    // Bypass age gate for performance testing tools
    // Maintains full functionality for real users
    return botPatterns.some(pattern => pattern.test(navigator.userAgent));
}
```

## 🔒 **Security Architecture**

### **Cannabis Compliance**
```javascript
// Legal Age Verification
- Required by cannabis regulations
- localStorage persistence prevents re-verification
- Bot detection bypasses only for testing tools
- No personal data stored

// Cross-System Verification
WordPress → Sets localStorage['tymber-user-has-allowed-age'] = 'true'
Tymber → Reads localStorage, no re-verification needed
```

### **Data Security**
```javascript
// localStorage Contents (No Sensitive Data)
{
    'last-store-selected': 'store-uuid',        // Store preference only
    'tymber-user-has-allowed-age': 'true'       // Age verification only
}

// No Personal Information Stored:
- No names, addresses, emails
- No payment information  
- No browsing history
- No user accounts
```

### **Input Sanitization**
```php
// All User Inputs Sanitized
$store_location = sanitize_text_field($_GET['store_filter']);
$start_input = sanitize_text_field($_POST['_club420_davis_schedule_start']);

// Database Queries Use Prepared Statements
$wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE meta_key = %s", $meta_key);
```

## 🔧 **Integration Architecture**

### **WordPress ↔ Tymber Bridge**
```
WordPress (club420.com)
├── Sets localStorage state
├── Age verification
├── Store selection  
└── Content filtering

Proxy Pass Layer
├── /menu/f-street/ → Davis Tymber
├── /menu/highway-80/ → Dixon Tymber
└── Maintains domain consistency

Tymber System
├── Reads localStorage state
├── No re-verification needed
├── Store-specific inventory
└── Seamless user experience
```

### **Divi Theme Integration**
```
Divi Builder Components:
├── Header Location Picker (Code Module)
├── Content Sections (.davis-content/.dixon-content classes)
├── Store Dropdowns (Code Modules)
└── Product Carousels ([club420_deals_carousel] shortcode)

Divi Theme Options:
├── Body JavaScript (complete system)
├── Custom CSS (mobile menu transformation)
└── Menu Integration (WordPress → Tymber routing)
```

### **WooCommerce Integration**
```
Product Meta System:
├── Store URLs (Davis/Dixon)
├── Visibility settings (Always/Scheduled/Disabled)
├── Schedule timestamps (UTC)
└── Calculated enabled status

Admin Interface:
├── Product editing enhancements
├── Bulk store filtering
├── Visual status indicators
└── Real-time scheduling dashboard

Frontend Integration:
├── Auto button generation
├── Store-specific filtering
├── Real-time product carousels
└── Cache exclusion management
```

## 📊 **Monitoring & Debugging**

### **System Health Monitoring**
```php
// Scheduler Dashboard Shortcode
[club420_scheduler_dashboard]

Displays:
- Current system time (Los Angeles + UTC)
- All scheduled products with live status
- Product activation countdown
- System health indicators
```

### **JavaScript Console Logging**
```javascript
// Debug Messages
'Club420: Age verified - access granted'
'Club420: Store selected - davis'  
'Club420: Mobile LOCATIONS integration complete!'
'Club420: Menu navigation to: /menu/f-street/categories/flower/'
'Club420: NUCLEAR - Completely blocking Divi'
```

### **Performance Metrics**
```
System Impact:
- Age gate: ~2ms load time addition
- Mobile menu override: ~1ms initialization
- Scheduling queries: Real-time, cache-excluded  
- Location pickers: Minimal DOM manipulation
- Overall: Maintains Grade A+ performance
```

## 🎯 **Technical Dependencies**

### **Critical Dependencies**
```
WordPress Requirements:
- WordPress 6.0+
- Divi Theme (active)
- WooCommerce Plugin
- Code Snippets Plugin
- SSL Certificate (cannabis compliance)

Hosting Requirements:
- WP Engine (recommended for cache exclusion)
- Proxy pass configuration
- localStorage browser support
- JavaScript enabled

Tymber Integration:
- Active Tymber/Blaze account
- Store configuration matching WordPress
- Domain proxy pass setup
- Same-domain localStorage access
```

### **Browser Compatibility**
```
Fully Supported:
- Chrome 70+
- Firefox 70+  
- Safari 12+
- Edge 79+

Partially Supported:
- Internet Explorer 11 (fallback styling)
- Older mobile browsers (basic functionality)
```

---

**🏗️ This architecture enables the complete Club420 multi-store cannabis dispensary system with real-time scheduling, cross-system state management, and cannabis compliance.**
