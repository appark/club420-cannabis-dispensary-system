# Club420 Complete Installation Guide

> **📋 Complete Step-by-Step Setup** | **Estimated Time: 2-3 hours** | **Skill Level: Intermediate**

This guide walks you through installing the complete Club420 multi-store cannabis dispensary system from scratch.

## 📋 **Prerequisites**

### **Required WordPress Setup:**
- ✅ **WordPress 6.0+** with admin access
- ✅ **Divi Theme** (active and updated)
- ✅ **WooCommerce Plugin** (installed and configured)
- ✅ **Code Snippets Plugin** (install if not present)
- ✅ **WP Engine Hosting** (recommended for cache exclusion features)

### **Required Technical Setup:**
- ✅ **Proxy pass configuration** for `/menu/` URLs to Tymber
- ✅ **SSL certificate** (cannabis compliance requirement)
- ✅ **Backup system** (recommend full backup before installation)

### **Tymber/Blaze Requirements:**
- ✅ **Active Tymber account** with store configurations
- ✅ **Store IDs** for Davis and Dixon locations
- ✅ **Domain pointing** to Tymber for proxy pass URLs

## 🔧 **Step 1: Install Required WordPress Plugins**

### **Install Code Snippets Plugin:**
1. Go to **WordPress Admin → Plugins → Add New**
2. Search for **"Code Snippets"**
3. Install and activate **Code Snippets by Code Snippets Pro**

### **Verify Required Plugins:**
- ✅ **Divi Theme** (active)
- ✅ **WooCommerce** (active) 
- ✅ **Code Snippets** (active)

## 📁 **Step 2: Install PHP Snippets (Backend System)**

### **Install All 6 PHP Snippets:**

**Go to**: WordPress Admin → **Snippets → Add New**

**Install these snippets in order:**

#### **Snippet 1: Store Filter + Cache Exclusion**
```php
// Copy content from php-snippets/snippet-1-store-filter.php
// Title: "Club420 Store Filter + Cache Exclusion"
// Activate: YES
```

#### **Snippet 2: Product Scheduler Interface**
```php
// Copy content from php-snippets/snippet-2-product-scheduler.php  
// Title: "Club420 Product Scheduler Interface"
// Activate: YES
```

#### **Snippet 3: Admin Store Filter**
```php
// Copy content from php-snippets/snippet-3-admin-filter.php
// Title: "Club420 Admin Store Filter" 
// Activate: YES
```

#### **Snippet 4: Auto Button Generator**
```php
// Copy content from php-snippets/snippet-4-auto-buttons.php
// Title: "Club420 Auto Button Generator"
// Activate: YES
```

#### **Snippet 5: BlazeSlider Carousel**
```php
// Copy content from php-snippets/snippet-5-carousel.php
// Title: "Club420 BlazeSlider Carousel"
// Activate: YES
```

#### **Snippet 6: Scheduler Dashboard**
```php
// Copy content from php-snippets/snippet-6-dashboard.php
// Title: "Club420 Scheduler Dashboard"
// Activate: YES
```

### **Verify PHP Installation:**
After installing all snippets, you should see:
- ✅ **6 active snippets** in Code Snippets dashboard
- ✅ **New dropdown** on WooCommerce → Products admin page
- ✅ **New "CLUB420 Store Settings"** section when editing products

## 🎨 **Step 3: Install Mobile Menu CSS**

### **Add Complete Mobile Menu CSS:**

**Go to**: Divi → **Customize → Additional CSS**

**Add this CSS:**
```css
/* Copy complete content from css/mobile-menu-complete.css */

/* Hide WooCommerce Cart Icon */
.et-cart-info { display: none !important; }

/* Hide Menu item on Desktop */
@media (min-width: 981px) {
  .dt-hide-on-desktop {
    display: none !important;
  }
}

/* [Complete mobile menu CSS - see file for full content] */
```

**Save Changes**

## 🖥️ **Step 4: Install Body JavaScript (Core System)**

### **Add Complete Body JavaScript:**

**Go to**: Divi → **Theme Options → Integration → Body**

**Add this JavaScript:**
```html
<!-- Copy complete content from javascript/body-javascript-complete.html -->

<!-- Age Gate, Store Picker, Content Visibility, Menu Navigation -->
<!-- [Complete body JavaScript - see file for full content] -->
```

**Save Options**

## 📱 **Step 5: Create WordPress Menu Structure**

### **Create LOCATIONS Menu:**

**Go to**: WordPress Admin → **Appearance → Menus**

**Create this exact structure:**
```
📋 LOCATIONS (Parent Menu Item)
├── Navigation Label: "Locations"
├── URL: [Leave empty - DO NOT use #]
├── CSS Classes: dt-hide-on-desktop
└── Sub-items:
    ├── Davis
    │   ├── Navigation Label: "Davis"
    │   ├── URL: # (or leave empty)
    │   └── CSS Classes: dt-hide-on-desktop
    └── Dixon
        ├── Navigation Label: "Dixon"
        ├── URL: # (or leave empty)
        └── CSS Classes: dt-hide-on-desktop
```

### **Critical Menu Requirements:**
- ✅ **Parent URL must be empty** (not #)
- ✅ **CSS class `dt-hide-on-desktop`** on all items
- ✅ **Exact text**: "Locations", "Davis", "Dixon"
- ✅ **Menu assigned** to primary navigation

## 🖥️ **Step 6: Create Desktop Location Picker**

### **Add Location Picker to Header:**

**Go to**: Divi → **Customize → Header & Navigation → Header → Edit Global Header Layout**

**In Divi Builder:**
1. **Find header row** with menu
2. **Add new column** (or use existing)
3. **Add Code Module** to column
4. **Insert location picker code:**

```html
<!-- Copy complete content from javascript/desktop-location-picker.html -->

<div class="club420-location-picker">
  <div class="location-button" id="locationButton">
    <!-- [Complete desktop picker HTML - see file for full content] -->
  </div>
</div>

<style>
/* [Complete desktop picker CSS - see file for full content] */
</style>

<script>
/* [Complete desktop picker JavaScript - see file for full content] */
</script>
```

**Save header changes**

## 🎨 **Step 7: Configure Store Content Classes**

### **Organize Content by Store:**

**In Divi Builder** (when editing pages):

#### **For Davis-only content:**
1. **Select section/module** 
2. **Advanced → CSS ID & Classes → CSS Class**
3. **Add**: `davis-content`

#### **For Dixon-only content:**
1. **Select section/module**
2. **Advanced → CSS ID & Classes → CSS Class** 
3. **Add**: `dixon-content`

#### **For store-specific menu items:**
- **Davis menu items**: Add class `davis-menu`
- **Dixon menu items**: Add class `dixon-menu`

### **Content Organization Strategy:**
```
Page Layout:
├── Shared Header (both stores see)
├── Davis Content Section (.davis-content)
│   ├── Davis-specific text
│   ├── Davis carousel: [club420_deals_carousel]
│   └── Davis store dropdown
├── Dixon Content Section (.dixon-content)  
│   ├── Dixon-specific text
│   ├── Dixon carousel: [club420_deals_carousel]
│   └── Dixon store dropdown
└── Shared Footer (both stores see)
```

## 🛒 **Step 8: Configure WooCommerce Products**

### **Set Up Individual Products:**

**Go to**: WooCommerce → **Products → Edit Product**

**For each product, configure:**

#### **Davis Store Settings:**
- **Product visibility**: Always visible / Scheduled / Not available
- **Davis Store URL**: `https://club420.com/menu/f-street/categories/[category]/?brand=[brand]`
- **Schedule** (if using scheduled visibility)

#### **Dixon Store Settings:**
- **Product visibility**: Always visible / Scheduled / Not available  
- **Dixon Store URL**: `https://club420.com/menu/highway-80/categories/[category]/?brand=[brand]`
- **Schedule** (if using scheduled visibility)

### **Example Product URLs:**
```
Davis Flower: https://club420.com/menu/f-street/categories/flower/?brand=Smokiez&order=-price
Dixon Edibles: https://club420.com/menu/highway-80/categories/edible/?brand=Smokiez&order=-price
```

## 🎛️ **Step 9: Add Store Dropdowns to Pages**

### **Add Synchronized Store Dropdowns:**

**In Divi Code Modules**, add:
```html
<!-- Copy from divi-integration/store-dropdown-examples.html -->

<div style="max-width: 350px;">
 <select id="styled-store-select-page" style="[styling]">
   <option value="">Choose Store Location</option>
   <option value="davis">Davis Store</option>
   <option value="dixon">Dixon Store</option>
 </select>
</div>
```

### **Auto-Detection Pattern:**
- **Any dropdown with ID starting `styled-store-select-`** will be automatically detected
- **All dropdowns sync** when one changes
- **State persists** across page loads

## 🎠 **Step 10: Add Product Carousels**

### **Add Carousels to Store Sections:**

**In Davis content sections:**
```
[club420_deals_carousel]
```

**In Dixon content sections:**
```
[club420_deals_carousel]
```

**Carousel automatically filters** products based on:
- Current store selection (`?store_filter=davis` or `?store_filter=dixon`)
- Product availability settings
- Real-time scheduling status

## 📊 **Step 11: Add Admin Dashboard**

### **Create Admin Monitoring Page:**

**Create new WordPress page** or add to existing admin area:
```
[club420_scheduler_dashboard]
```

**This dashboard shows:**
- ✅ Current system status
- ✅ All scheduled products with live status
- ✅ Real-time scheduling monitoring
- ✅ Product availability by store

## 🔧 **Step 12: Configure Store IDs**

### **Update Store Configuration:**

**In your JavaScript files**, verify these Store IDs match your Tymber setup:

```javascript
// In body-javascript-complete.html
const davisID = '79043044-f024-4b70-8714-4fcad409f978';
const dixonID = '7029749f-9c6d-419e-b037-5c1b566f3df9';

// Store mapping for Tymber URLs
storeMapping: {
  '79043044-f024-4b70-8714-4fcad409f978': 'f-street',
  '7029749f-9c6d-419e-b037-5c1b566f3df9': 'highway-80'
}
```

**If your Store IDs are different:**
1. **Get correct IDs** from Tymber dashboard
2. **Update all JavaScript** files with correct IDs
3. **Update proxy pass** configuration to match

## ✅ **Step 13: Testing & Verification**

### **Test Complete User Flow:**

#### **Age Gate Testing:**
1. **Open site in incognito** browser
2. **Age gate should appear** 
3. **Click "YES, I AM 21"**
4. **Age gate should disappear**
5. **Refresh page** - age gate should NOT reappear

#### **Store Selection Testing:**
1. **Store picker should appear** after age verification
2. **Select Davis** - should store in localStorage
3. **Select Dixon** - should update localStorage and content

#### **Desktop Location Picker Testing:**
1. **Black button should show** in header
2. **Shows current store** (Davis or Dixon)
3. **Dropdown opens** on click  
4. **Store switching works** (page reloads with store filter)

#### **Mobile Location Picker Testing:**
1. **LOCATIONS should appear as black button** (not regular menu item)
2. **Davis/Dixon hidden by default**
3. **Click LOCATIONS** - dropdown should open
4. **Select store** - dropdown should close and switch

#### **Content Visibility Testing:**
1. **Davis content visible** when Davis selected
2. **Dixon content visible** when Dixon selected  
3. **Content switches** when store changes

#### **Product Carousel Testing:**
1. **Carousels show products** for selected store only
2. **No products from other store** appear
3. **Real-time scheduling** works if configured

#### **Menu Navigation Testing:**
1. **Click "Flower"** in menu
2. **Should route to**: `/menu/f-street/categories/flower/` (Davis) or `/menu/highway-80/categories/flower/` (Dixon)
3. **No re-verification** required on Tymber pages

### **Backend Testing:**
1. **WooCommerce Products page** should show store filter dropdown
2. **Davis/Dixon columns** should show checkmarks
3. **Product editing** should show CLUB420 settings section
4. **Scheduler dashboard** should show live product status

## ⚠️ **Common Installation Issues**

### **Age Gate Not Appearing:**
- Check Divi Body JavaScript is saved
- Verify no JavaScript errors in console
- Confirm localStorage is enabled in browser

### **Mobile LOCATIONS Not Working:**
- Verify WordPress menu structure exactly matches requirements
- Check CSS class `dt-hide-on-desktop` is applied
- Confirm mobile CSS is installed in Divi Custom CSS

### **Store Switching Not Working:**
- Check Store IDs match your Tymber configuration
- Verify localStorage values in browser console
- Confirm proxy pass is working for /menu/ URLs

### **Content Not Showing/Hiding:**
- Verify CSS classes `.davis-content` and `.dixon-content` are applied
- Check JavaScript console for errors
- Confirm Body JavaScript is installed correctly

### **Carousels Not Filtering:**
- Verify all 6 PHP snippets are active
- Check WooCommerce products have store URLs configured
- Confirm `?store_filter=` parameter appears in URL

## 🎯 **Installation Complete!**

**If all tests pass, your Club420 system is fully installed and ready for production use.**

### **Next Steps:**
1. **Configure individual products** with store URLs and scheduling
2. **Create store-specific content** using CSS class system
3. **Monitor system** using admin dashboard
4. **Train staff** on product scheduling interface

### **For Ongoing Maintenance:**
- **Monitor scheduler dashboard** regularly
- **Update product URLs** as needed in Tymber
- **Test user flow** periodically to ensure everything works
- **Keep backups** of working configuration

---

**🍃 Club420 Installation Complete - Ready for Cannabis Dispensary Operations!**
