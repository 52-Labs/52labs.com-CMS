import { Link } from 'react-router-dom';
import './Home.css';

function Home() {
  return (
    <div className="home">
      <section className="hero">
        <div className="hero-content">
          <h1 className="hero-title">
            Your Concert Promotion
            <span className="hero-gradient"> Tech Stack</span>
          </h1>
          <p className="hero-subtitle">
            Discover our curated collection of tools designed for concert promoters,
            event creators, and music industry professionals.
          </p>
          <div className="hero-actions">
            <Link to="/library" className="btn btn-primary btn-lg">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
              </svg>
              Browse App Library
            </Link>
            <a href="#features" className="btn btn-secondary btn-lg">
              Learn More
            </a>
          </div>
        </div>
        
        <div className="hero-visual">
          <div className="hero-cards">
            <div className="floating-card card-1">
              <div className="floating-card-icon" style={{ background: 'linear-gradient(135deg, #4F7DF3, #6B93F5)' }}>🎫</div>
              <span>Ticketing</span>
            </div>
            <div className="floating-card card-2">
              <div className="floating-card-icon" style={{ background: 'linear-gradient(135deg, #FF6B6B, #FF8E8E)' }}>📢</div>
              <span>Marketing</span>
            </div>
            <div className="floating-card card-3">
              <div className="floating-card-icon" style={{ background: 'linear-gradient(135deg, #4ECDC4, #7BE0D8)' }}>📊</div>
              <span>Analytics</span>
            </div>
            <div className="floating-card card-4">
              <div className="floating-card-icon" style={{ background: 'linear-gradient(135deg, #A855F7, #C084FC)' }}>📅</div>
              <span>Booking</span>
            </div>
          </div>
        </div>
      </section>

      <section id="features" className="features-section">
        <div className="container">
          <h2 className="section-title">Everything You Need</h2>
          <p className="section-subtitle">
            From ticket sales to analytics, we've got you covered with our comprehensive suite of tools.
          </p>
          
          <div className="features-grid">
            <div className="feature-card">
              <div className="feature-icon">🚀</div>
              <h3>Quick Setup</h3>
              <p>Get started in minutes with our intuitive interfaces and streamlined onboarding.</p>
            </div>
            <div className="feature-card">
              <div className="feature-icon">🔗</div>
              <h3>Seamless Integration</h3>
              <p>All our apps work together, sharing data and insights across your entire workflow.</p>
            </div>
            <div className="feature-card">
              <div className="feature-icon">📱</div>
              <h3>Cross-Platform</h3>
              <p>Access your tools on web, iOS, and Android—work from anywhere, anytime.</p>
            </div>
            <div className="feature-card">
              <div className="feature-icon">🔒</div>
              <h3>Enterprise Security</h3>
              <p>Bank-level encryption and security protocols to keep your data safe.</p>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}

export default Home;
