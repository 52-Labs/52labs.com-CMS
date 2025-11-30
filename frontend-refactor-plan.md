# SvelteKit Refactor Plan: Strapi -> Headless WordPress

We are migrating the frontend from using Strapi (REST/GraphQL) to Headless WordPress (GraphQL). This refactor will ensure type safety, environment-based configuration (Local vs Prod), and a clean separation of data fetching logic.

## 1. Environment Configuration
We will update the `.env` strategy to support both Local (WP Local) and Production (WP Engine) environments without manual code changes.

**Action Items:**
-   Create/Update `.env` and `.env.example` files.
-   Define `PUBLIC_WORDPRESS_API_URL` for the GraphQL endpoint.
-   (Optional) Define `WORDPRESS_AUTH_REFRESH_TOKEN` if you need authenticated queries later.

**Example `.env` structure:**
```bash
# Local Development
PUBLIC_WORDPRESS_API_URL="http://52-labs.local/graphql"

# Production (WP Engine)
# PUBLIC_WORDPRESS_API_URL="https://your-install.wpengine.com/graphql"
```

## 2. GraphQL Client Setup
We need a robust GraphQL client. If you aren't already using one, `graphql-request` is lightweight and works great with SvelteKit's `load` functions.

**Action Items:**
-   Install dependencies: `npm install graphql-request graphql`
-   Create a dedicated API client file (`src/lib/api.js` or `src/lib/graphql.js`) to handle requests.
-   Configure it to use the environment variable `PUBLIC_WORDPRESS_API_URL`.

**Sample Client (`src/lib/api.js`):**
```javascript
import { GraphQLClient } from 'graphql-request';
import { PUBLIC_WORDPRESS_API_URL } from '$env/static/public';

export const client = new GraphQLClient(PUBLIC_WORDPRESS_API_URL);
```

## 3. Query Migration & Type Generation
We need to replace Strapi queries with WordPress GraphQL queries.

**Action Items:**
-   **Products**: Fetch `products` -> `nodes`. Map ACF fields (`productFields`) to your component props.
-   **Settings**: Fetch `siteSettings` -> `settingsFields` for global data (Nav, Footer, SEO).
-   **Images**: Update image rendering to use WP's `sourceUrl` and `altText` instead of Strapi's nested `attributes.url`.

**Key Mapping Changes:**
| Strapi Concept | WordPress GraphQL Concept |
| :--- | :--- |
| `attributes.name` | `title` |
| `attributes.slug` | `slug` |
| `attributes.image.data.attributes.url` | `featuredImage.node.sourceUrl` or ACF `logo.node.sourceUrl` |
| `attributes.productFeatures` | `productFields.productFeatures` (Repeater) |

## 4. Component Updates
Update Svelte components to handle the new data structure.

**Action Items:**
-   **`+layout.server.js`**: Fetch Global Settings (Menu, Footer, SEO) here so they are available on every page.
-   **`+page.server.js`** (Home/Products): Fetch the list of products.
-   **`[slug]/+page.server.js`**: Fetch single product by `slug` using `idType: SLUG`.
-   **Image Component**: Create or update a `<Image />` component to handle WP's image object structure seamlessly.

## 5. SEO & Metadata
Migrate SEO handling to use the data from the "Site Settings" options page we created.

**Action Items:**
-   Update `src/routes/+layout.svelte` or `<svelte:head>` sections to read from `data.siteSettings.SEO`.

## 6. Prompt for the Developer (or AI)
*Copy and paste this into your chat with your developer or AI assistant:*

> "I need to refactor my SvelteKit app to switch from Strapi to Headless WordPress.
>
> **Context:**
> - We are using `WPGraphQL` with ACF.
> - The WordPress backend is ready with `Products` (CPT) and `Site Settings` (Options Page).
> - I have a `.env` variable `PUBLIC_WORDPRESS_API_URL`.
>
> **Tasks:**
> 1.  **Setup**: Install `graphql-request` and create a client in `src/lib/api.js` using the env var.
> 2.  **Queries**: Create a `src/lib/queries.js` file containing GraphQL queries for:
>     - `GetSiteSettings` (Site Title, Description, Logo, SEO, Carousel).
>     - `GetAllProducts` (Title, Slug, Categories, and all ACF fields like logo, description, features).
>     - `GetProductBySlug` (For individual product pages).
> 3.  **Data Fetching**: Update `+layout.server.js` to fetch Site Settings and pass them to the layout. Update `+page.server.js` to fetch Products.
> 4.  **Components**: Refactor my `ProductCard.svelte` and `PageHeader.svelte` to consume the flattened WPGraphQL response structure (e.g., `node.sourceUrl` instead of `data.attributes.url`)."



