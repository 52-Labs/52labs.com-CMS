// Sample product data matching the mockup style
export const categories = [
  { id: 'ticketing', name: 'Ticketing', color: '#4F7DF3' },
  { id: 'marketing', name: 'Marketing', color: '#FF6B6B' },
  { id: 'analytics', name: 'Analytics', color: '#4ECDC4' },
  { id: 'booking', name: 'Booking', color: '#A855F7' },
  { id: 'admin', name: 'Admin', color: '#F59E0B' },
  { id: 'websites', name: 'Websites', color: '#10B981' },
];

export const platforms = [
  { id: 'web', name: 'Web', icon: 'desktop' },
  { id: 'ios', name: 'iOS', icon: 'apple' },
  { id: 'android', name: 'Android', icon: 'android' },
];

export const features = [
  { id: 'ai-powered', name: 'AI-Powered' },
  { id: 'automation', name: 'Automation' },
  { id: 'reporting', name: 'Reporting' },
  { id: 'integrations', name: 'Integrations' },
  { id: 'realtime', name: 'Real-time' },
  { id: 'collaboration', name: 'Collaboration' },
];

export const products = [
  {
    id: 1,
    name: 'Prekindle',
    slug: 'prekindle',
    category: 'ticketing',
    description: 'All-in-one ticketing platform for casual event creators and professional planners.',
    shortDescription: 'Prekindle\'s ticket ing with pestaing and evanansis first concert onvay.',
    platforms: ['web', 'ios', 'android'],
    features: ['automation', 'reporting', 'integrations'],
    icon: '🎫',
    iconBg: 'linear-gradient(135deg, #4F7DF3 0%, #6B93F5 100%)',
    downloadUrl: 'https://prekindle.com/download',
    learnMoreUrl: 'https://prekindle.com',
    usageInstructions: [
      'Create Account & Set Up',
      'Build Events & Ticket Types',
      'Manage Sales & Attendees',
      'Utilize Marketing Tools'
    ],
    screenshots: [
      '/screenshots/prekindle-1.png',
      '/screenshots/prekindle-2.png',
      '/screenshots/prekindle-3.png'
    ]
  },
  {
    id: 2,
    name: 'Sparrow',
    slug: 'sparrow',
    category: 'marketing',
    description: 'Powerful marketing automation platform designed for concert promoters and event organizers.',
    shortDescription: 'Bow awm a megaphone for concentrating wra ponnet pomers.',
    platforms: ['web', 'ios'],
    features: ['ai-powered', 'automation', 'integrations'],
    icon: '📢',
    iconBg: 'linear-gradient(135deg, #FF6B6B 0%, #FF8E8E 100%)',
    downloadUrl: 'https://sparrow.io/download',
    learnMoreUrl: 'https://sparrow.io',
    usageInstructions: [
      'Connect your event platforms',
      'Set up automated campaigns',
      'Track engagement metrics',
      'Optimize with AI insights'
    ],
    screenshots: []
  },
  {
    id: 3,
    name: 'Booking',
    slug: 'booking',
    category: 'ticketing',
    description: 'Streamlined booking management for venues and talent agencies.',
    shortDescription: 'Flammevenr ticketing, marketing, analytics rap edit and ocaling patrnts.',
    platforms: ['web', 'ios', 'android'],
    features: ['realtime', 'collaboration', 'integrations'],
    icon: '📅',
    iconBg: 'linear-gradient(135deg, #4ECDC4 0%, #7BE0D8 100%)',
    downloadUrl: 'https://booking.app/download',
    learnMoreUrl: 'https://booking.app',
    usageInstructions: [
      'Add venues and artists',
      'Create booking requests',
      'Manage confirmations',
      'Track schedules'
    ],
    screenshots: []
  },
  {
    id: 4,
    name: 'Flnment',
    slug: 'flnment',
    category: 'marketing',
    description: 'Strategic marketing intelligence platform for music industry professionals.',
    shortDescription: 'Easy to morsculatis and comilucs qulte riek te to your concert.',
    platforms: ['web'],
    features: ['ai-powered', 'reporting', 'realtime'],
    icon: '📊',
    iconBg: 'linear-gradient(135deg, #A855F7 0%, #C084FC 100%)',
    downloadUrl: 'https://flnment.io/download',
    learnMoreUrl: 'https://flnment.io',
    usageInstructions: [
      'Connect data sources',
      'Configure dashboards',
      'Set up alerts',
      'Generate reports'
    ],
    screenshots: []
  },
  {
    id: 5,
    name: 'Booktine',
    slug: 'booktine',
    category: 'ticketing',
    description: 'Booking tickets and manage prices and iterative apps for live events.',
    shortDescription: 'Booking tickets and manage pricets and iteneritve apps.',
    platforms: ['web', 'ios'],
    features: ['automation', 'integrations'],
    icon: '🎟️',
    iconBg: 'linear-gradient(135deg, #4F7DF3 0%, #818CF8 100%)',
    downloadUrl: 'https://booktine.com/download',
    learnMoreUrl: 'https://booktine.com',
    usageInstructions: [
      'Create event listings',
      'Set dynamic pricing',
      'Process ticket sales',
      'Manage attendees'
    ],
    screenshots: []
  },
  {
    id: 6,
    name: 'Flankist',
    slug: 'flankist',
    category: 'marketing',
    description: 'Concert document to share baronela nirernorfor apps.',
    shortDescription: 'Concert desment to share baronela nir\'ernoritor apps.',
    platforms: ['web', 'android'],
    features: ['collaboration', 'realtime'],
    icon: '📈',
    iconBg: 'linear-gradient(135deg, #4ECDC4 0%, #A7F3D0 100%)',
    downloadUrl: 'https://flankist.io/download',
    learnMoreUrl: 'https://flankist.io',
    usageInstructions: [
      'Create marketing plans',
      'Share with team',
      'Track progress',
      'Analyze results'
    ],
    screenshots: []
  },
  {
    id: 7,
    name: 'Charier',
    slug: 'charier',
    category: 'ticketing',
    description: 'finanx active marketing and oommanle analytea ermin.',
    shortDescription: 'finanx active marketing and oommanle analytea ermin.',
    platforms: ['web', 'ios'],
    features: ['ai-powered', 'automation', 'reporting'],
    icon: '💼',
    iconBg: 'linear-gradient(135deg, #FF6B6B 0%, #FCA5A5 100%)',
    downloadUrl: 'https://charier.com/download',
    learnMoreUrl: 'https://charier.com',
    usageInstructions: [
      'Set up campaigns',
      'Configure automation',
      'Monitor analytics',
      'Optimize performance'
    ],
    screenshots: []
  },
  {
    id: 8,
    name: 'Websites',
    slug: 'websites',
    category: 'marketing',
    description: 'Build stunning websites for your concerts and events with our drag-and-drop builder.',
    shortDescription: 'Websites condmarers in your webstriop, and analystes.',
    platforms: ['web'],
    features: ['automation', 'integrations', 'collaboration'],
    icon: '🌐',
    iconBg: 'linear-gradient(135deg, #10B981 0%, #6EE7B7 100%)',
    downloadUrl: 'https://websites.app/download',
    learnMoreUrl: 'https://websites.app',
    usageInstructions: [
      'Choose a template',
      'Customize design',
      'Add content',
      'Publish site'
    ],
    screenshots: []
  },
  {
    id: 9,
    name: 'Analytix',
    slug: 'analytix',
    category: 'analytics',
    description: 'Comprehensive analytics dashboard for tracking event performance and audience insights.',
    shortDescription: 'Deep analytics and insights for your concert performance.',
    platforms: ['web', 'ios', 'android'],
    features: ['ai-powered', 'reporting', 'realtime'],
    icon: '📉',
    iconBg: 'linear-gradient(135deg, #4ECDC4 0%, #0EA5E9 100%)',
    downloadUrl: 'https://analytix.io/download',
    learnMoreUrl: 'https://analytix.io',
    usageInstructions: [
      'Connect data sources',
      'Build custom dashboards',
      'Set up tracking',
      'Generate insights'
    ],
    screenshots: []
  },
  {
    id: 10,
    name: 'VenueHub',
    slug: 'venuehub',
    category: 'booking',
    description: 'Venue management and booking coordination for concert halls and event spaces.',
    shortDescription: 'Manage your venues and coordinate bookings effortlessly.',
    platforms: ['web', 'ios'],
    features: ['realtime', 'collaboration', 'integrations'],
    icon: '🏟️',
    iconBg: 'linear-gradient(135deg, #A855F7 0%, #E879F9 100%)',
    downloadUrl: 'https://venuehub.com/download',
    learnMoreUrl: 'https://venuehub.com',
    usageInstructions: [
      'Add venue details',
      'Set availability',
      'Accept bookings',
      'Manage calendar'
    ],
    screenshots: []
  },
  {
    id: 11,
    name: 'AdminPanel',
    slug: 'adminpanel',
    category: 'admin',
    description: 'Centralized administration dashboard for managing all your concert tech stack.',
    shortDescription: 'Central hub for managing your entire tech ecosystem.',
    platforms: ['web'],
    features: ['automation', 'reporting', 'collaboration'],
    icon: '⚙️',
    iconBg: 'linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%)',
    downloadUrl: 'https://adminpanel.io/download',
    learnMoreUrl: 'https://adminpanel.io',
    usageInstructions: [
      'Configure settings',
      'Manage users',
      'Set permissions',
      'Monitor system'
    ],
    screenshots: []
  },
  {
    id: 12,
    name: 'SiteBuilder',
    slug: 'sitebuilder',
    category: 'websites',
    description: 'Modern website builder specifically designed for music events and festivals.',
    shortDescription: 'Build beautiful event websites without code.',
    platforms: ['web'],
    features: ['ai-powered', 'automation', 'integrations'],
    icon: '🎨',
    iconBg: 'linear-gradient(135deg, #10B981 0%, #34D399 100%)',
    downloadUrl: 'https://sitebuilder.app/download',
    learnMoreUrl: 'https://sitebuilder.app',
    usageInstructions: [
      'Select theme',
      'Drag and drop elements',
      'Connect ticketing',
      'Launch site'
    ],
    screenshots: []
  }
];

export function getProductBySlug(slug) {
  return products.find(p => p.slug === slug);
}

export function getProductsByCategory(categoryId) {
  if (!categoryId) return products;
  return products.filter(p => p.category === categoryId);
}

export function filterProducts({ search, categories: selectedCategories, platforms: selectedPlatforms, features: selectedFeatures }) {
  return products.filter(product => {
    // Search filter
    if (search) {
      const searchLower = search.toLowerCase();
      const matchesSearch = 
        product.name.toLowerCase().includes(searchLower) ||
        product.description.toLowerCase().includes(searchLower) ||
        product.category.toLowerCase().includes(searchLower);
      if (!matchesSearch) return false;
    }
    
    // Category filter
    if (selectedCategories && selectedCategories.length > 0) {
      if (!selectedCategories.includes(product.category)) return false;
    }
    
    // Platform filter
    if (selectedPlatforms && selectedPlatforms.length > 0) {
      const hasMatchingPlatform = selectedPlatforms.some(p => product.platforms.includes(p));
      if (!hasMatchingPlatform) return false;
    }
    
    // Feature filter
    if (selectedFeatures && selectedFeatures.length > 0) {
      const hasMatchingFeature = selectedFeatures.some(f => product.features.includes(f));
      if (!hasMatchingFeature) return false;
    }
    
    return true;
  });
}
