import { useParams, Link, useNavigate } from 'react-router-dom';
import { getProductBySlug, categories } from '../data/products';
import './ProductDetail.css';

function ProductDetail() {
  const { slug } = useParams();
  const navigate = useNavigate();
  const product = getProductBySlug(slug);

  if (!product) {
    return (
      <div className="product-not-found">
        <div className="not-found-content">
          <h1>404</h1>
          <p>Product not found</p>
          <Link to="/library" className="btn btn-primary">
            Back to Library
          </Link>
        </div>
      </div>
    );
  }

  const category = categories.find(c => c.id === product.category);

  const PlatformIcon = ({ type, active, label }) => {
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
      <div className={`platform-badge ${active ? 'active' : 'inactive'}`} title={label}>
        <span className="platform-badge-icon">{icons[type]}</span>
      </div>
    );
  };

  return (
    <div className="product-detail">
      {/* Breadcrumb */}
      <div className="breadcrumb">
        <Link to="/library" className="breadcrumb-link">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
          </svg>
          Back to Library
        </Link>
      </div>

      {/* Hero Section */}
      <section className="product-hero">
        <div className="product-hero-content">
          <div className="product-hero-icon" style={{ background: product.iconBg }}>
            <span>{product.icon}</span>
          </div>
          
          <div className="product-hero-info">
            <h1 className="product-hero-name">{product.name}</h1>
            <span className={`tag tag-${product.category}`}>{category?.name}</span>
            <p className="product-hero-description">{product.description}</p>
          </div>

          <div className="product-hero-actions">
            <a 
              href={product.downloadUrl} 
              target="_blank" 
              rel="noopener noreferrer" 
              className="btn btn-primary btn-lg download-btn"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              Download Now
            </a>
            <div className="platform-badges">
              <PlatformIcon type="web" active={product.platforms.includes('web')} label="Web" />
              <PlatformIcon type="ios" active={product.platforms.includes('ios')} label="iOS" />
              <PlatformIcon type="android" active={product.platforms.includes('android')} label="Android" />
            </div>
          </div>
        </div>
      </section>

      {/* Screenshots Section */}
      <section className="product-section screenshots-section">
        <h2 className="section-heading">Screenshots</h2>
        <div className="screenshots-carousel">
          <button className="carousel-btn carousel-prev" aria-label="Previous">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
              <polyline points="15 18 9 12 15 6"/>
            </svg>
          </button>
          <div className="screenshots-track">
            <div className="screenshot-placeholder">
              <div className="screenshot-mock">
                <div className="mock-header">
                  <span className="mock-dot"></span>
                  <span className="mock-dot"></span>
                  <span className="mock-dot"></span>
                </div>
                <div className="mock-content">
                  <div className="mock-sidebar"></div>
                  <div className="mock-main">
                    <div className="mock-bar"></div>
                    <div className="mock-cards">
                      <div className="mock-card"></div>
                      <div className="mock-card"></div>
                      <div className="mock-card"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="screenshot-placeholder">
              <div className="screenshot-mock alt">
                <div className="mock-header">
                  <span className="mock-dot"></span>
                  <span className="mock-dot"></span>
                  <span className="mock-dot"></span>
                </div>
                <div className="mock-content">
                  <div className="mock-table">
                    <div className="mock-row"></div>
                    <div className="mock-row"></div>
                    <div className="mock-row"></div>
                    <div className="mock-row"></div>
                  </div>
                </div>
              </div>
            </div>
            <div className="screenshot-placeholder">
              <div className="screenshot-mock dark">
                <div className="mock-header dark">
                  <span className="mock-dot"></span>
                  <span className="mock-dot"></span>
                  <span className="mock-dot"></span>
                </div>
                <div className="mock-content dark">
                  <div className="mock-chart"></div>
                  <div className="mock-stats">
                    <div className="mock-stat"></div>
                    <div className="mock-stat"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button className="carousel-btn carousel-next" aria-label="Next">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </button>
        </div>
      </section>

      {/* Instructions Section */}
      <section className="product-section instructions-section">
        <div className="instructions-grid">
          {/* Usage Instructions */}
          <div className="instructions-card">
            <h2 className="section-heading">Usage Instructions</h2>
            <ol className="instructions-list">
              {product.usageInstructions.map((instruction, index) => (
                <li key={index} className="instruction-item">
                  <span className="instruction-number">{index + 1}</span>
                  <span className="instruction-text">{instruction}</span>
                </li>
              ))}
            </ol>
          </div>

          {/* Download Instructions */}
          <div className="instructions-card download-card">
            <h2 className="section-heading">Download Instructions</h2>
            <div className="download-options">
              <div className="download-section">
                <h4>Desktop</h4>
                <div className="download-links">
                  <a href={product.downloadUrl} className="download-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                    Download for macOS
                  </a>
                  <a href={product.downloadUrl} className="download-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M0 3.449L9.75 2.1v9.451H0m10.949-9.602L24 0v11.4H10.949M0 12.6h9.75v9.451L0 20.699M10.949 12.6H24V24l-12.9-1.801"/>
                    </svg>
                    Download for Windows
                  </a>
                </div>
              </div>
              <div className="download-section">
                <h4>Mobile</h4>
                <div className="store-badges">
                  <a href="#" className="store-badge" aria-label="Download on App Store">
                    <div className="store-badge-content">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                      </svg>
                      <div className="store-text">
                        <span className="store-label">Download on the</span>
                        <span className="store-name">App Store</span>
                      </div>
                    </div>
                  </a>
                  <a href="#" className="store-badge" aria-label="Get it on Google Play">
                    <div className="store-badge-content">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 20.5V3.5C3 2.91 3.34 2.39 3.84 2.15L13.69 12L3.84 21.85C3.34 21.61 3 21.09 3 20.5M16.81 15.12L6.05 21.34L14.54 12.85L16.81 15.12M20.16 10.81C20.5 11.08 20.75 11.5 20.75 12C20.75 12.5 20.5 12.92 20.16 13.19L17.89 14.5L15.39 12L17.89 9.5L20.16 10.81M6.05 2.66L16.81 8.88L14.54 11.15L6.05 2.66Z"/>
                      </svg>
                      <div className="store-text">
                        <span className="store-label">GET IT ON</span>
                        <span className="store-name">Google Play</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      {product.features && product.features.length > 0 && (
        <section className="product-section features-section">
          <h2 className="section-heading">Key Features</h2>
          <div className="features-list">
            {product.features.map((feature, index) => (
              <div key={index} className="feature-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>{feature.replace('-', ' ')}</span>
              </div>
            ))}
          </div>
        </section>
      )}
    </div>
  );
}

export default ProductDetail;
