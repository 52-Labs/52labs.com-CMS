import { useState, useMemo } from 'react';
import { Link } from 'react-router-dom';
import { products, categories, platforms, features, filterProducts } from '../data/products';
import './Library.css';

function Library() {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategories, setSelectedCategories] = useState([]);
  const [selectedPlatforms, setSelectedPlatforms] = useState([]);
  const [selectedFeatures, setSelectedFeatures] = useState([]);
  const [expandedSections, setExpandedSections] = useState({
    category: true,
    platform: true,
    features: true,
  });

  const filteredProducts = useMemo(() => {
    return filterProducts({
      search: searchQuery,
      categories: selectedCategories,
      platforms: selectedPlatforms,
      features: selectedFeatures,
    });
  }, [searchQuery, selectedCategories, selectedPlatforms, selectedFeatures]);

  const toggleSection = (section) => {
    setExpandedSections(prev => ({
      ...prev,
      [section]: !prev[section]
    }));
  };

  const toggleFilter = (type, value) => {
    const setterMap = {
      category: setSelectedCategories,
      platform: setSelectedPlatforms,
      features: setSelectedFeatures,
    };
    const currentValues = {
      category: selectedCategories,
      platform: selectedPlatforms,
      features: selectedFeatures,
    };

    const setter = setterMap[type];
    const current = currentValues[type];

    if (current.includes(value)) {
      setter(current.filter(v => v !== value));
    } else {
      setter([...current, value]);
    }
  };

  const clearAllFilters = () => {
    setSearchQuery('');
    setSelectedCategories([]);
    setSelectedPlatforms([]);
    setSelectedFeatures([]);
  };

  const hasActiveFilters = searchQuery || selectedCategories.length > 0 || 
    selectedPlatforms.length > 0 || selectedFeatures.length > 0;

  return (
    <div className="library">
      <div className="library-header">
        <h1 className="library-title">Our Concert Promotion Tech Stack</h1>
        <p className="library-subtitle">
          Discover tools built specifically for the music industry
        </p>
      </div>

      <div className="library-container">
        {/* Sidebar Filters */}
        <aside className="library-sidebar">
          <div className="sidebar-header">
            <h3>Filters</h3>
            {hasActiveFilters && (
              <button className="clear-filters" onClick={clearAllFilters}>
                Clear all
              </button>
            )}
          </div>

          {/* Search */}
          <div className="sidebar-search">
            <svg className="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            <input
              type="text"
              placeholder="Search apps..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
            />
          </div>

          {/* Category Filter */}
          <div className="filter-section">
            <button 
              className="filter-section-header"
              onClick={() => toggleSection('category')}
            >
              <span>Category</span>
              <svg 
                className={`chevron ${expandedSections.category ? 'expanded' : ''}`} 
                width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
              >
                <polyline points="6 9 12 15 18 9"/>
              </svg>
            </button>
            {expandedSections.category && (
              <div className="filter-options">
                {categories.map(category => (
                  <label key={category.id} className="checkbox-wrapper">
                    <input
                      type="checkbox"
                      checked={selectedCategories.includes(category.id)}
                      onChange={() => toggleFilter('category', category.id)}
                    />
                    <span className="checkbox-custom">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3">
                        <polyline points="20 6 9 17 4 12"/>
                      </svg>
                    </span>
                    <span className="checkbox-label">{category.name}</span>
                  </label>
                ))}
              </div>
            )}
          </div>

          {/* Platform Filter */}
          <div className="filter-section">
            <button 
              className="filter-section-header"
              onClick={() => toggleSection('platform')}
            >
              <span>Platform</span>
              <svg 
                className={`chevron ${expandedSections.platform ? 'expanded' : ''}`}
                width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
              >
                <polyline points="6 9 12 15 18 9"/>
              </svg>
            </button>
            {expandedSections.platform && (
              <div className="filter-options">
                {platforms.map(platform => (
                  <label key={platform.id} className="checkbox-wrapper">
                    <input
                      type="checkbox"
                      checked={selectedPlatforms.includes(platform.id)}
                      onChange={() => toggleFilter('platform', platform.id)}
                    />
                    <span className="checkbox-custom">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3">
                        <polyline points="20 6 9 17 4 12"/>
                      </svg>
                    </span>
                    <span className="checkbox-label">{platform.name}</span>
                  </label>
                ))}
              </div>
            )}
          </div>

          {/* Key Features Filter */}
          <div className="filter-section">
            <button 
              className="filter-section-header"
              onClick={() => toggleSection('features')}
            >
              <span>Key Features</span>
              <svg 
                className={`chevron ${expandedSections.features ? 'expanded' : ''}`}
                width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
              >
                <polyline points="6 9 12 15 18 9"/>
              </svg>
            </button>
            {expandedSections.features && (
              <div className="filter-options">
                {features.map(feature => (
                  <label key={feature.id} className="checkbox-wrapper">
                    <input
                      type="checkbox"
                      checked={selectedFeatures.includes(feature.id)}
                      onChange={() => toggleFilter('features', feature.id)}
                    />
                    <span className="checkbox-custom">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3">
                        <polyline points="20 6 9 17 4 12"/>
                      </svg>
                    </span>
                    <span className="checkbox-label">{feature.name}</span>
                  </label>
                ))}
              </div>
            )}
          </div>
        </aside>

        {/* Product Grid */}
        <div className="library-main">
          <div className="library-results-header">
            <span className="results-count">
              {filteredProducts.length} {filteredProducts.length === 1 ? 'app' : 'apps'}
            </span>
          </div>

          <div className="products-grid">
            {filteredProducts.map((product, index) => (
              <ProductCard key={product.id} product={product} index={index} />
            ))}
          </div>

          {filteredProducts.length === 0 && (
            <div className="no-results">
              <div className="no-results-icon">🔍</div>
              <h3>No apps found</h3>
              <p>Try adjusting your filters or search query</p>
              <button className="btn btn-secondary" onClick={clearAllFilters}>
                Clear filters
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

function ProductCard({ product, index }) {
  const getCategoryClass = (category) => {
    return `tag tag-${category}`;
  };

  const PlatformIcon = ({ type, active }) => {
    const icons = {
      web: (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
          <line x1="8" y1="21" x2="16" y2="21"/>
          <line x1="12" y1="17" x2="12" y2="21"/>
        </svg>
      ),
      ios: (
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
        </svg>
      ),
      android: (
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M17.523 15.341c-.5 0-.908-.407-.908-.908s.407-.908.908-.908.908.407.908.908-.407.908-.908.908m-11.046 0c-.5 0-.908-.407-.908-.908s.407-.908.908-.908.908.407.908.908-.407.908-.908.908m11.399-5.814l1.976-3.422a.41.41 0 0 0-.149-.559.411.411 0 0 0-.559.149l-2.001 3.466C15.528 8.392 13.838 7.94 12 7.94c-1.838 0-3.528.452-5.143 1.221L4.856 5.695a.411.411 0 0 0-.559-.149.41.41 0 0 0-.149.559l1.976 3.422C2.9 11.553.95 15.08.95 19.093h22.1c0-4.013-1.95-7.54-5.174-9.566"/>
        </svg>
      ),
    };

    return (
      <span className={`platform-icon ${active ? 'active' : ''}`}>
        {icons[type]}
      </span>
    );
  };

  return (
    <Link 
      to={`/product/${product.slug}`} 
      className="product-card card"
      style={{ animationDelay: `${index * 50}ms` }}
    >
      <div className="product-card-header">
        <div className="product-icon" style={{ background: product.iconBg }}>
          <span>{product.icon}</span>
        </div>
        <div className="product-info">
          <h3 className="product-name">{product.name}</h3>
          <span className={getCategoryClass(product.category)}>
            {product.category}
          </span>
        </div>
      </div>
      
      <p className="product-description">{product.shortDescription}</p>
      
      <div className="product-card-footer">
        <div className="platform-icons">
          <PlatformIcon type="web" active={product.platforms.includes('web')} />
          <PlatformIcon type="ios" active={product.platforms.includes('ios')} />
          <PlatformIcon type="android" active={product.platforms.includes('android')} />
        </div>
        <span className="learn-more-link">
          Learn More
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"/>
            <polyline points="12 5 19 12 12 19"/>
          </svg>
        </span>
      </div>
    </Link>
  );
}

export default Library;
