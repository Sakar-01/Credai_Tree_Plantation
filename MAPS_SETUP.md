# Google Maps Integration Setup

## API Key Configuration

To enable Google Maps functionality, you need to:

1. **Get a Google Maps API Key:**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select existing one
   - Enable the following APIs:
     - Maps JavaScript API
     - Places API (optional, for location search)
     - Geocoding API (optional, for address lookup)

2. **Replace API Key in Views:**
   
   **For Admin Map View (`resources/views/admin/map.blade.php`):**
   ```html
   <!-- Replace YOUR_API_KEY with your actual API key -->
   <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_ACTUAL_API_KEY&libraries=visualization&callback=initMap"></script>
   ```

   **For Individual Tree View (`resources/views/trees/show.blade.php`):**
   ```html
   <!-- Replace YOUR_API_KEY with your actual API key -->
   <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_ACTUAL_API_KEY&callback=initTreeMap"></script>
   ```

3. **Optional: Add to Environment Variables**
   
   Add to `.env` file:
   ```
   GOOGLE_MAPS_API_KEY=your_actual_api_key_here
   ```
   
   Then update the views to use:
   ```html
   <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=visualization&callback=initMap"></script>
   ```

## Map Features Implemented

### Admin Map View (`/admin/map`)
- **Interactive Map** centered on Jalgaon, India
- **Color-coded Markers** based on tree health:
  - 🟢 Green: Healthy trees
  - 🟡 Yellow: Under inspection
  - 🔴 Red: Needs attention
  - ⚫ Gray: Planted (not yet inspected)
- **Heatmap Layer** showing tree concentration
- **Filter Controls** to show/hide different tree statuses
- **Info Windows** with tree details and action buttons
- **Map Controls**: Center on Jalgaon, Fit all markers

### Individual Tree View
- **Detailed Location Map** for each tree
- **Status-based Marker Colors**
- **Info Window** with tree information
- **Hybrid Map View** (satellite + roads)

## Sample Data
- 10 sample trees created around Jalgaon, Maharashtra, India
- Coordinates: 21.0077°N, 75.5626°E (Jalgaon city center)
- Various tree species native to the region
- Different health statuses for demonstration

## Map Controls
- **Zoom**: Mouse wheel or +/- buttons
- **Pan**: Click and drag
- **Map Types**: Roadmap, Satellite, Hybrid, Terrain
- **Markers**: Click for tree information
- **Filters**: Toggle different tree status visibility
- **Heatmap**: Toggle density visualization

## Browser Requirements
- Modern browsers with JavaScript enabled
- Internet connection for Google Maps tiles
- Geolocation API support (for GPS features)