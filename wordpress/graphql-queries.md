# Sample GraphQL Queries for SvelteKit

Here are the GraphQL queries you can use in your SvelteKit application to fetch data from the WordPress Headless CMS.

## Endpoint
`http://localhost:8000/graphql`

## 1. Get All Products
Fetches all products with their ACF fields and categories.

```graphql
query GetProducts {
  products(first: 100) {
    nodes {
      id
      databaseId
      slug
      title
      # ACF Product Fields
      productFields {
        description
        isCoreProduct
        learnMoreUrl
        backgroundColor
        order
        logo {
          node {
            sourceUrl
            altText
          }
        }
        productFeatures {
          feature
        }
        internalOptions {
          media {
            nodes {
              sourceUrl
            }
          }
          installationInstructions
          usageInstructions
          downloadUrl
          internalProductOnly
        }
      }
      # Categories
      productCategories {
        nodes {
          name
          slug
          categoryFields {
            icon {
              node {
                sourceUrl
              }
            }
            order
          }
        }
      }
    }
  }
}
```

## 2. Get Single Product by Slug
Fetches a single product based on the slug parameter.

```graphql
query GetProductBySlug($slug: ID!) {
  product(id: $slug, idType: SLUG) {
    id
    title
    productFields {
      description
      isCoreProduct
      # ... other fields
    }
  }
}
```

## 3. Get Site Settings
Fetches the global site settings from the Options Page.

```graphql
query GetSiteSettings {
  siteSettings {
    settingsFields {
      siteName
      siteDescription
      heroHeadline
      heroSubHeadline
      maintenanceMode
      favicon {
        node {
          sourceUrl
        }
      }
      SEO {
        metaTitle
        metaDescription
        shareImage {
          node {
            sourceUrl
          }
        }
      }
      carousel {
        nodes {
          sourceUrl
        }
      }
    }
  }
}
```





