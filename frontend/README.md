# 52 Labs App Library

A beautiful App Store-style library page for showcasing products/apps with filtering and search capabilities.

## Features

- 🎨 **Beautiful UI** - Modern, clean design inspired by the Apple App Store
- 🔍 **Search** - Real-time search across all products
- 🏷️ **Filtering** - Filter by category, platform, and key features
- 📱 **Responsive** - Works great on desktop, tablet, and mobile
- ⚡ **Fast** - Built with Vite for lightning-fast development and builds

## Pages

### Home (`/`)
Landing page with hero section and features overview.

### App Library (`/library`)
Main archive page featuring:
- Left sidebar with collapsible filter sections
- Category filters (Ticketing, Marketing, Analytics, etc.)
- Platform filters (Web, iOS, Android)
- Key features filters (AI-Powered, Automation, etc.)
- Search bar
- Product grid with animated cards

### Product Detail (`/product/:slug`)
Individual product page with:
- Product hero with icon, name, category, and description
- Download button and platform badges
- Screenshots carousel
- Usage instructions
- Download instructions with App Store/Play Store badges
- Key features pills

## Getting Started

### Prerequisites

- Node.js 18 or higher
- npm or yarn

### Installation

```bash
# Navigate to frontend directory
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev
```

### Building for Production

```bash
npm run build
```

The built files will be in the `dist` directory.

### Preview Production Build

```bash
npm run preview
```

## Connecting to Strapi

This frontend can connect to the Strapi backend API. To do so:

1. Make sure Strapi is running on `http://localhost:1337`
2. The Vite proxy is configured to forward `/api` requests to Strapi
3. Update `src/data/products.js` to fetch from the API instead of using static data

### Example API Integration

Replace the static data in `products.js` with:

```javascript
export async function fetchProducts() {
  const response = await fetch('/api/products?populate=*');
  const data = await response.json();
  return data.data;
}
```

## Customization

### Colors

Edit the CSS variables in `src/styles/global.css`:

```css
:root {
  --primary-blue: #4F7DF3;
  --cat-ticketing: #4F7DF3;
  --cat-marketing: #FF6B6B;
  /* ... */
}
```

### Adding New Categories

Edit `src/data/products.js` and add to the `categories` array.

### Adding New Products

Add a new product object to the `products` array in `src/data/products.js`.

## Tech Stack

- **React 18** - UI library
- **React Router 6** - Routing
- **Vite 5** - Build tool
- **CSS Modules** - Styling

## Project Structure

```
frontend/
├── public/
│   └── favicon.svg
├── src/
│   ├── components/
│   │   ├── Header.jsx
│   │   └── Header.css
│   ├── data/
│   │   └── products.js
│   ├── pages/
│   │   ├── Home.jsx
│   │   ├── Home.css
│   │   ├── Library.jsx
│   │   ├── Library.css
│   │   ├── ProductDetail.jsx
│   │   └── ProductDetail.css
│   ├── styles/
│   │   └── global.css
│   ├── App.jsx
│   └── main.jsx
├── index.html
├── package.json
└── vite.config.js
```

## License

MIT
