import type { Schema, Struct } from '@strapi/strapi';

export interface ProductsFeatures extends Struct.ComponentSchema {
  collectionName: 'components_products_features';
  info: {
    displayName: 'features';
  };
  attributes: {
    feature: Schema.Attribute.String;
  };
}

export interface ProductsInternalOptions extends Struct.ComponentSchema {
  collectionName: 'components_products_internal_options';
  info: {
    displayName: 'internalOptions';
  };
  attributes: {
    downloadUrl: Schema.Attribute.String;
    installationInstructions: Schema.Attribute.Blocks;
    media: Schema.Attribute.Media<
      'images' | 'files' | 'videos' | 'audios',
      true
    >;
    usageInstructions: Schema.Attribute.Blocks;
  };
}

export interface ProductsPageSettings extends Struct.ComponentSchema {
  collectionName: 'components_products_page_settings';
  info: {
    displayName: 'pageSettings';
    icon: 'cog';
  };
  attributes: {
    isActive: Schema.Attribute.Boolean &
      Schema.Attribute.Required &
      Schema.Attribute.DefaultTo<true>;
    order: Schema.Attribute.Integer;
  };
}

export interface SharedMedia extends Struct.ComponentSchema {
  collectionName: 'components_shared_media';
  info: {
    displayName: 'Media';
    icon: 'file-video';
  };
  attributes: {
    file: Schema.Attribute.Media<'images' | 'files' | 'videos'>;
  };
}

export interface SharedQuote extends Struct.ComponentSchema {
  collectionName: 'components_shared_quotes';
  info: {
    displayName: 'Quote';
    icon: 'indent';
  };
  attributes: {
    body: Schema.Attribute.Text;
    title: Schema.Attribute.String;
  };
}

export interface SharedRichText extends Struct.ComponentSchema {
  collectionName: 'components_shared_rich_texts';
  info: {
    description: '';
    displayName: 'Rich text';
    icon: 'align-justify';
  };
  attributes: {
    body: Schema.Attribute.RichText;
  };
}

export interface SharedSeo extends Struct.ComponentSchema {
  collectionName: 'components_shared_seos';
  info: {
    description: '';
    displayName: 'Seo';
    icon: 'allergies';
    name: 'Seo';
  };
  attributes: {
    metaDescription: Schema.Attribute.Text & Schema.Attribute.Required;
    metaTitle: Schema.Attribute.String & Schema.Attribute.Required;
    shareImage: Schema.Attribute.Media<'images'>;
  };
}

export interface SharedSlider extends Struct.ComponentSchema {
  collectionName: 'components_shared_sliders';
  info: {
    description: '';
    displayName: 'Slider';
    icon: 'address-book';
  };
  attributes: {
    files: Schema.Attribute.Media<'images', true>;
  };
}

declare module '@strapi/strapi' {
  export module Public {
    export interface ComponentSchemas {
      'products.features': ProductsFeatures;
      'products.internal-options': ProductsInternalOptions;
      'products.page-settings': ProductsPageSettings;
      'shared.media': SharedMedia;
      'shared.quote': SharedQuote;
      'shared.rich-text': SharedRichText;
      'shared.seo': SharedSeo;
      'shared.slider': SharedSlider;
    }
  }
}
