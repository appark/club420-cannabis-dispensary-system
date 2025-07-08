# Club420 Cannabis Dispensary - Complete Multi-Store Integration System

> **🚀 Status: PRODUCTION READY** | **All Components Fully Tested & Working** | **Shop Menu Collapse Added - July 2025**

## 🏗️ **What This System Does**

Complete WordPress/WooCommerce + Tymber integration for cannabis dispensaries with:
- **Real-time product scheduling** - Products show/hide at exact scheduled times
- **Dual store locations** - Davis (F-Street) and Dixon (Highway-80) with independent inventories  
- **Cannabis compliance** - Age gate verification with cross-system persistence
- **Seamless user experience** - One-time verification across WordPress ↔ Tymber
- **Store-specific content** - Same pages, different content based on store selection
- **Advanced location pickers** - Desktop header + mobile menu transformation with optimized UX
- **🆕 Shop menu collapse** - Mobile shop categories now collapsible for better UX

## 🎯 **System Architecture**

```
WordPress Site (club420.com)
├── Age Gate + Store Picker → localStorage state management
├── Desktop Location Picker → Optimized UX with action-focused design
├── Mobile Location Picker → WordPress menu transformation
├── Mobile Shop Collapse → Collapsible shop categories (NEW)
├── Store-Specific Content → CSS class visibility system
├── WooCommerce Products → Real-time scheduling + store filtering
└── Menu Navigation → Text-based routing to Tymber

Tymber Integration (Proxy Pass)
├── /menu/f-street/ → Davis Store Inventory (Tymber)
├── /menu/highway-80/ → Dixon Store Inventory (Tymber)
└── Shared localStorage → No re-verification needed
```

## ✅ **Complete Component Status**

| Component | Status | Purpose | Implementation |
|-----------|--------|---------|----------------|
| **PHP Snippet 1** | ✅ Production | Store filtering + WP Engine cache exclusion | Code Snippets plugin |
| **PHP Snippet 2** | ✅ Production | Product scheduler admin interface | Code Snippets plugin |
| **PHP Snippet 3** | ✅ Production | Admin store filter (dropdown + columns) | Code Snippets plugin |
| **PHP Snippet 4** | ✅ Production | Auto button generator (no flash) | Code Snippets plugin |
| **PHP Snippet 5** | ✅ Production | BlazeSlider carousel `[club420_deals_carousel]` | Code Snippets plugin |
| **PHP Snippet 6** | ✅ Production | Scheduler dashboard `[club420_scheduler_dashboard]` | Code Snippets plugin |
| **Age Gate + Store Picker** | ✅ Production | Cannabis compliance + localStorage | Divi Body JavaScript |
| **Desktop Location Picker** | ✅ Production | Optimized UX with action-focused design | Divi Builder (Code module) |
| **Mobile Location Picker** | ✅ Production | LOCATIONS menu styled as collapsible button | WordPress Menu + CSS + JavaScript |
| **🆕 Mobile Shop Collapse** | ✅ Production | Collapsible shop categories with + / − icons | CSS + JavaScript Integration |
| **Content Visibility System** | ✅ Production | `.davis-content` / `.dixon-content` classes | CSS + JavaScript |
| **Store Dropdown Sync** | ✅ Production | Multiple synchronized dropdowns | Auto-detection pattern |
| **Menu Navigation** | ✅ Production | Text-based routing to Tymber categories | JavaScript interception |

## 🔧 **Quick Installation Summary**

### **Required WordPress Setup:**
1. **Plugins**: Code Snippets, WooCommerce, Divi Theme
2. **Install 6 PHP snippets** via Code Snippets plugin
3. **Add complete Body JavaScript** to Divi Theme Options → Integration → Body (now includes shop collapse)
4. **Add mobile menu CSS** to Divi → Customize → Additional CSS (now includes shop collapse)
5. **Create WordPress menu** with LOCATIONS item (CSS class: `dt-hide-on-desktop`)
6. **Add desktop location picker** to Divi Builder header (Code module)

### **Required Hosting Setup:**
- **Proxy pass configuration** for `/menu/` URLs to Tymber
- **WP Engine cache exclusion** for real-time scheduling

## 🎨 **Store Configuration**

### **Store IDs & Mapping:**
```javascript
Davis Store: '79043044-f024-4b70-8714-4fcad409f978' → /menu/f-street/
Dixon Store: '7029749f-9c6d-419e-b037-5c1b566f3df9' → /menu/highway-80/
```

### **localStorage Keys:**
```javascript
'last-store-selected' → Store preference (Davis/Dixon)
'tymber-user-has-allowed-age' → Cannabis compliance verification
```

### **Content Organization:**
```css
.davis-content → Divi sections visible only to Davis users
.dixon-content → Divi sections visible only to Dixon users  
.davis-menu → Menu items visible only to Davis users
.dixon-menu → Menu items visible only to Dixon users
```

## 🛠️ **How Each Component Works**

### **Backend PHP System (6 Snippets)**
1. **Store Filter** - WooCommerce product filtering by store with cache bypass
2. **Product Scheduler** - Admin interface for scheduling products (UTC ↔ Los Angeles)
3. **Admin Filter** - Backend dropdown to filter products by store availability
4. **Auto Buttons** - Dynamic "View Products" buttons on product pages
5. **Carousel System** - `[club420_deals_carousel]` with real-time store filtering
6. **Dashboard** - `[club420_scheduler_dashboard]` for monitoring scheduled products

### **Frontend JavaScript System**
- **Age Gate** - Cannabis compliance with bot detection for performance testing
- **Store Picker** - Location selection with cross-system persistence
- **Content Visibility** - Shows/hides sections based on store selection
- **Menu Navigation** - Intercepts clicks and routes to appropriate Tymber URLs
- **Location Pickers** - Desktop (optimized UX) + Mobile (menu transformation)
- **🆕 Shop Collapse** - Collapsible shop categories with accordion behavior

### **🆕 Mobile Shop Menu Collapse System**
- **Surgical Targeting** - Only affects shop categories, leaves LOCATIONS untouched
- **CSS Selector** - `:not(.dt-hide-on-desktop)` excludes LOCATIONS system
- **Toggle Icons** - + / − icons for expand/collapse state
- **Accordion Effect** - Only one shop submenu open at a time
- **Smooth Animation** - slideDown animation for submenu expansion
- **Navigation Preservation** - Submenu items still navigate normally

### **Desktop Location Picker - Optimized UX**
- **Action-focused design** - Highlights switchable location, not current location
- **Clear status indicators** - Current location shows checkmark, other location shows in brand yellow
- **Consistent 9px border radius** - Button and dropdown perfectly aligned
- **28px arrows** - Properly sized for visibility and accessibility
- **No hover effects** - Clean, professional appearance

### **User Experience Flow**
1. **User lands** on club420.com → Age gate appears
2. **Age verification** → localStorage: `'tymber-user-has-allowed-age': 'true'`
3. **Store selection** → localStorage: `'last-store-selected': store_id`
4. **Content adapts** → Davis/Dixon specific sections become visible
5. **🆕 Mobile menu** → Shop categories collapsed with + icons, LOCATIONS unchanged
6. **Menu navigation** → Routes to appropriate Tymber store (/menu/f-street/ or /menu/highway-80/)
7. **Tymber pages** → Read localStorage, no re-verification needed

## 📁 **Repository Structure**

```
├── README.md                          # This overview document (UPDATED)
├── INSTALLATION.md                    # Complete step-by-step setup guide (UPDATED)
├── ARCHITECTURE.md                    # Deep technical architecture explanation (UPDATED)
├── php-snippets/                      # 6 PHP files for Code Snippets plugin
│   ├── snippet-1-store-filter.php    # Store filtering + cache exclusion
│   ├── snippet-2-product-scheduler.php # Admin scheduling interface  
│   ├── snippet-3-admin-filter.php    # Admin products page enhancements
│   ├── snippet-4-auto-buttons.php    # Dynamic button generation
│   ├── snippet-5-carousel.php        # BlazeSlider carousel system
│   └── snippet-6-dashboard.php       # Scheduling monitoring dashboard
├── javascript/                       # Frontend JavaScript components
│   ├── body-javascript-complete.html # Complete Divi body integration (UPDATED)
│   ├── desktop-location-picker.html  # Optimized header location picker
│   └── mobile-menu-transformation.js # Mobile LOCATIONS menu override
├── css/                              # Styling components
│   ├── mobile-menu-complete.css     # Complete mobile menu transformation (UPDATED)
│   └── store-dropdown-styles.css    # Page dropdown styling
├── divi-integration/                 # Divi-specific setup files
│   ├── store-dropdown-examples.html # Multiple dropdown HTML examples
│   ├── content-organization.md      # CSS class system guide
│   └── header-setup-guide.md       # Desktop location picker installation
├── docs/                            # Technical documentation
│   ├── user-flow-complete.md       # End-to-end user journey (UPDATED)
│   ├── tymber-integration.md       # Proxy setup + localStorage sharing
│   ├── scheduling-system.md        # Real-time scheduling deep dive
│   ├── troubleshooting.md          # Common issues & solutions (UPDATED)
│   └── customization-guide.md      # Adapting for other businesses
└── assets/                         # Screenshots and diagrams
    ├── desktop-location-picker.png
    ├── mobile-menu-transformation.png
    ├── mobile-shop-collapse.png     # NEW: Shop collapse demo
    ├── admin-scheduler-interface.png
    └── system-architecture-diagram.png
```

## 🎯 **Critical Success Factors**

### **What Makes This System Work:**
1. **localStorage Bridge** - Seamless state sharing between WordPress and Tymber
2. **Real-time Scheduling** - Products activate/deactivate at exact times
3. **CSS Class Visibility** - Same page, different content per store
4. **Text-based Navigation** - Menu routing based on link text, not URLs
5. **Cache Exclusion** - WP Engine cache bypassed for scheduled content
6. **Nuclear Mobile Override** - Complete Divi menu behavior replacement for LOCATIONS
7. **Optimized Desktop UX** - Action-focused design with clear status indicators
8. **🆕 Surgical Shop Collapse** - Collapsible shop menus without affecting LOCATIONS

### **Key Technical Dependencies:**
- **Store IDs** must match between WordPress and Tymber systems
- **Proxy pass** configuration for /menu/ URLs
- **localStorage access** across club420.com domain
- **WordPress menu structure** with specific CSS classes
- **Divi Builder** for header location picker
- **Code Snippets plugin** for PHP implementation

## 🚀 **Recent UX Improvements**

### **🆕 Mobile Shop Menu Collapse (Latest Addition):**
- **Collapsible Categories** - Shop, Flower, Cartridges, Edibles, etc. now collapse
- **+ / − Toggle Icons** - Clear visual indicators for expand/collapse state
- **Accordion Behavior** - Only one shop submenu open at a time
- **LOCATIONS Preserved** - Zero conflicts with existing LOCATIONS system
- **Smooth Animations** - Professional slideDown effects for menu expansion

### **Desktop Location Picker Enhancements:**
- **Action-focused design** - Yellow highlights the switchable location, not current
- **Clear status indicators** - Current location shows checkmark (✓), other location shows in action color
- **Consistent border radius** - 9px for both button and dropdown
- **Larger arrows** - 28px for better visibility
- **No hover effects** - Clean, professional appearance
- **Optimized for accessibility** - Proper contrast and visual hierarchy

## 🚀 **Getting Started**

### **For New Developers:**
1. Read [INSTALLATION.md](INSTALLATION.md) for complete setup (updated with shop collapse)
2. Review [ARCHITECTURE.md](ARCHITECTURE.md) for system understanding (updated)
3. Test with [user-flow-complete.md](docs/user-flow-complete.md) (updated)

### **For System Maintenance:**
1. Monitor with `[club420_scheduler_dashboard]` shortcode
2. Add products using Snippet 2 admin interface
3. Update store content using CSS class system
4. Shop collapse works automatically - no maintenance needed

### **For Troubleshooting:**
1. Check [troubleshooting.md](docs/troubleshooting.md) for common issues (updated)
2. Verify localStorage values in browser console
3. Confirm proxy pass configuration
4. Test shop collapse functionality separately from LOCATIONS

## ⚠️ **Important Notes**

- **Cannabis Compliance**: Age gate is legally required - never bypass for real users
- **Bot Detection**: System bypasses age gate for performance testing tools only
- **Store IDs**: Hardcoded throughout system - see customization guide if changing
- **Cache Exclusion**: Critical for real-time scheduling functionality
- **Mobile Menu**: Requires specific WordPress menu structure to work
- **Desktop UX**: Optimized for action-focused user experience
- **🆕 Shop Collapse**: Uses surgical CSS targeting to avoid LOCATIONS conflicts

## 📞 **Support & Documentation**

**Complete documentation available in `/docs/` folder**

**System created**: 2025 | **Language**: PHP/JavaScript | **Framework**: WordPress/Divi | **Integration**: Tymber/Blaze

**Latest Update**: July 2025 - Added mobile shop menu collapse functionality

---

**🍃 Club420: Complete Cannabis Dispensary Integration - Production Ready System with Mobile Shop Collapse**
