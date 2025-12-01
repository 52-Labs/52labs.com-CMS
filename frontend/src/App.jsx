import { Routes, Route } from 'react-router-dom'
import Header from './components/Header'
import Library from './pages/Library'
import ProductDetail from './pages/ProductDetail'
import Home from './pages/Home'

function App() {
  return (
    <div className="app">
      <Header />
      <main className="main-content">
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/library" element={<Library />} />
          <Route path="/product/:slug" element={<ProductDetail />} />
        </Routes>
      </main>
    </div>
  )
}

export default App
