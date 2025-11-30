/**
 * Library JavaScript
 * Handles filtering, search, and carousel functionality
 */

(function() {
    'use strict';

    // ==========================================
    // Utility Functions
    // ==========================================
    
    /**
     * Debounce function for search input
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ==========================================
    // Filter Accordion
    // ==========================================
    
    function initFilterAccordions() {
        const filterHeaders = document.querySelectorAll('.filter-header');
        
        filterHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
            });
        });
    }

    // ==========================================
    // Category & Tag Filtering
    // ==========================================
    
    function initCategoryFilters() {
        const categoryCheckboxes = document.querySelectorAll('input[name="category"]');
        const tagCheckboxes = document.querySelectorAll('input[name="tag"]');
        const productsGrid = document.getElementById('products-grid');
        const postsGrid = document.getElementById('posts-grid');
        
        // Products grid filtering
        if (productsGrid) {
            // Category filters (checkboxes)
            if (categoryCheckboxes.length) {
                categoryCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        filterProducts();
                    });
                });
            }
            
            // Tag filters
            if (tagCheckboxes.length) {
                tagCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        filterProducts();
                    });
                });
            }
        }
        
        // Posts grid filtering (blog archive)
        if (postsGrid) {
            // Category filters (radio buttons for blog)
            if (categoryCheckboxes.length) {
                categoryCheckboxes.forEach(radio => {
                    radio.addEventListener('change', function() {
                        filterPosts();
                    });
                });
            }
        }
    }

    // ==========================================
    // Blog Posts Filtering
    // ==========================================
    
    function filterPosts() {
        const postsGrid = document.getElementById('posts-grid');
        if (!postsGrid) return;
        
        // Get selected category (radio button)
        const selectedCategory = document.querySelector('input[name="category"]:checked')?.value || '';
        
        // Get search query
        const searchInput = document.getElementById('header-search-input');
        const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';
        
        // Filter posts
        const postCards = postsGrid.querySelectorAll('.post-card');
        let visibleCount = 0;
        
        postCards.forEach(card => {
            const cardCategory = card.dataset.category || '';
            const cardTitle = card.querySelector('.post-card-title')?.textContent.toLowerCase() || '';
            const cardExcerpt = card.querySelector('.post-card-excerpt')?.textContent.toLowerCase() || '';
            
            // Check category match
            const categoryMatch = selectedCategory === '' || cardCategory === selectedCategory;
            
            // Check search match
            const searchMatch = searchQuery === '' || 
                               cardTitle.includes(searchQuery) || 
                               cardExcerpt.includes(searchQuery);
            
            // Show or hide card
            const isVisible = categoryMatch && searchMatch;
            card.classList.toggle('hidden', !isVisible);
            
            if (isVisible) visibleCount++;
        });
        
        // Show "no posts" message if needed
        updateNoPostsMessage(postsGrid, visibleCount);
        
        // Update URL state
        updateBlogUrlState();
    }
    
    function updateNoPostsMessage(grid, visibleCount) {
        let noPostsEl = grid.querySelector('.no-posts');
        
        if (visibleCount === 0) {
            if (!noPostsEl) {
                noPostsEl = document.createElement('div');
                noPostsEl.className = 'no-posts';
                noPostsEl.innerHTML = `
                    <div class="no-posts-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="12" y1="18" x2="12" y2="12"></line>
                            <line x1="9" y1="15" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <h3>No posts found</h3>
                    <p>We couldn't find any articles matching your criteria.</p>
                `;
                grid.appendChild(noPostsEl);
            }
            noPostsEl.style.display = 'block';
        } else if (noPostsEl) {
            noPostsEl.style.display = 'none';
        }
    }
    
    function updateBlogUrlState() {
        const selectedCategory = document.querySelector('input[name="category"]:checked')?.value || '';
        
        const searchInput = document.getElementById('header-search-input');
        const searchQuery = searchInput ? searchInput.value.trim() : '';
        
        const params = new URLSearchParams();
        if (selectedCategory) params.append('category', selectedCategory);
        if (searchQuery) params.append('search', searchQuery);
        
        const newUrl = params.toString() 
            ? `${window.location.pathname}?${params.toString()}`
            : window.location.pathname;
        
        window.history.replaceState({}, '', newUrl);
    }

    function filterProducts() {
        const productsGrid = document.getElementById('products-grid');
        if (!productsGrid) return;
        
        // Get selected categories
        const selectedCategories = Array.from(
            document.querySelectorAll('input[name="category"]:checked')
        ).map(cb => cb.value);
        
        // Get selected tags
        const selectedTags = Array.from(
            document.querySelectorAll('input[name="tag"]:checked')
        ).map(cb => cb.value);
        
        // Get search query
        const searchInput = document.getElementById('header-search-input');
        const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';
        
        // Filter products
        const productCards = productsGrid.querySelectorAll('.product-card');
        let visibleCount = 0;
        
        productCards.forEach(card => {
            const cardCategory = card.dataset.category || '';
            const cardTags = (card.dataset.tags || '').split(' ').filter(t => t);
            const cardTitle = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
            const cardDescription = card.querySelector('.product-description')?.textContent.toLowerCase() || '';
            
            // Check category match
            const categoryMatch = selectedCategories.length === 0 || 
                                  selectedCategories.includes(cardCategory);
            
            // Check tag match (product must have at least one selected tag if tags are selected)
            const tagMatch = selectedTags.length === 0 || 
                            selectedTags.some(tag => cardTags.includes(tag));
            
            // Check search match
            const searchMatch = searchQuery === '' || 
                               cardTitle.includes(searchQuery) || 
                               cardDescription.includes(searchQuery);
            
            // Show or hide card (all conditions must match)
            const isVisible = categoryMatch && tagMatch && searchMatch;
            card.classList.toggle('hidden', !isVisible);
            
            if (isVisible) visibleCount++;
        });
        
        // Show "no products" message if needed
        updateNoProductsMessage(productsGrid, visibleCount);
        
        // Update URL state
        updateUrlState();
    }

    function updateNoProductsMessage(grid, visibleCount) {
        let noProductsEl = grid.querySelector('.no-products');
        
        if (visibleCount === 0) {
            if (!noProductsEl) {
                noProductsEl = document.createElement('div');
                noProductsEl.className = 'no-products';
                noProductsEl.innerHTML = '<p>No products found matching your criteria.</p>';
                grid.appendChild(noProductsEl);
            }
            noProductsEl.style.display = 'block';
        } else if (noProductsEl) {
            noProductsEl.style.display = 'none';
        }
    }

    // ==========================================
    // Search Functionality
    // ==========================================
    
    function initSearch() {
        const searchInput = document.getElementById('header-search-input');
        if (!searchInput) return;
        
        const productsGrid = document.getElementById('products-grid');
        const postsGrid = document.getElementById('posts-grid');
        
        // Use appropriate filter function based on page type
        const filterFn = productsGrid ? filterProducts : (postsGrid ? filterPosts : null);
        
        if (!filterFn) return;
        
        const debouncedFilter = debounce(filterFn, 300);
        
        searchInput.addEventListener('input', function() {
            debouncedFilter();
        });
        
        // Handle Enter key for search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterFn();
            }
        });
    }

    // ==========================================
    // Tab Navigation
    // ==========================================
    
    function initTabs() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');
        
        if (!tabButtons.length) return;
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.dataset.tab;
                
                // Update button states
                tabButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Update panel visibility
                tabPanels.forEach(panel => {
                    panel.classList.remove('active');
                    if (panel.id === `tab-${targetTab}`) {
                        panel.classList.add('active');
                    }
                });
            });
        });
    }

    // ==========================================
    // Screenshot Lightbox
    // ==========================================
    
    function initLightbox() {
        const lightbox = document.getElementById('screenshot-lightbox');
        const thumbnails = document.querySelectorAll('.screenshot-thumb');
        
        if (!lightbox || !thumbnails.length) return;
        
        const images = lightbox.querySelectorAll('.lightbox-image');
        const closeBtn = lightbox.querySelector('.lightbox-close');
        const prevBtn = lightbox.querySelector('.lightbox-prev');
        const nextBtn = lightbox.querySelector('.lightbox-next');
        const overlay = lightbox.querySelector('.lightbox-overlay');
        const currentCounter = lightbox.querySelector('.lightbox-current');
        const totalCounter = lightbox.querySelector('.lightbox-total');
        
        let currentIndex = 0;
        const totalImages = images.length;
        
        function openLightbox(index) {
            currentIndex = index;
            updateLightbox();
            lightbox.classList.add('active');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
        
        function closeLightbox() {
            lightbox.classList.remove('active');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
        
        function updateLightbox() {
            images.forEach((img, i) => {
                img.classList.toggle('active', i === currentIndex);
            });
            
            if (currentCounter) {
                currentCounter.textContent = currentIndex + 1;
            }
            
            // Update nav button states
            if (prevBtn) {
                prevBtn.disabled = currentIndex === 0;
            }
            if (nextBtn) {
                nextBtn.disabled = currentIndex === totalImages - 1;
            }
        }
        
        function goToNext() {
            if (currentIndex < totalImages - 1) {
                currentIndex++;
                updateLightbox();
            }
        }
        
        function goToPrev() {
            if (currentIndex > 0) {
                currentIndex--;
                updateLightbox();
            }
        }
        
        // Thumbnail click handlers
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                const index = parseInt(this.dataset.index, 10);
                openLightbox(index);
            });
        });
        
        // Close handlers
        if (closeBtn) {
            closeBtn.addEventListener('click', closeLightbox);
        }
        if (overlay) {
            overlay.addEventListener('click', closeLightbox);
        }
        
        // Navigation handlers
        if (prevBtn) {
            prevBtn.addEventListener('click', goToPrev);
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', goToNext);
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (!lightbox.classList.contains('active')) return;
            
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                goToPrev();
            } else if (e.key === 'ArrowRight') {
                goToNext();
            }
        });
    }


    // ==========================================
    // AJAX Filtering (Optional Enhancement)
    // ==========================================
    
    function initAjaxFiltering() {
        // This function sets up AJAX-based filtering for server-side filtering
        // Currently using client-side filtering for better performance
        // Enable this if you need server-side filtering for large datasets
        
        const ajaxEnabled = false; // Set to true to enable AJAX filtering
        
        if (!ajaxEnabled) return;
        
        const productsGrid = document.getElementById('products-grid');
        if (!productsGrid) return;
        
        // Get all filter checkboxes
        const allFilters = document.querySelectorAll(
            'input[name="category"], input[name="tag"], input[name="platform"], input[name="feature"]'
        );
        
        allFilters.forEach(filter => {
            filter.addEventListener('change', function() {
                fetchFilteredProducts();
            });
        });
        
        function fetchFilteredProducts() {
            // Get selected filters
            const categories = Array.from(
                document.querySelectorAll('input[name="category"]:checked')
            ).map(cb => cb.value);
            
            const tags = Array.from(
                document.querySelectorAll('input[name="tag"]:checked')
            ).map(cb => cb.value);
            
            const searchInput = document.getElementById('header-search-input');
            const search = searchInput ? searchInput.value : '';
            
            // Build query string
            const params = new URLSearchParams();
            categories.forEach(cat => params.append('categories[]', cat));
            tags.forEach(tag => params.append('tags[]', tag));
            if (search) params.append('search', search);
            params.append('action', 'filter_products');
            params.append('nonce', libraryData?.nonce || '');
            
            // Show loading state
            productsGrid.classList.add('loading');
            
            // Fetch filtered products
            fetch(libraryData?.ajaxUrl || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    productsGrid.innerHTML = data.data.html;
                }
            })
            .catch(error => {
                console.error('Filter error:', error);
            })
            .finally(() => {
                productsGrid.classList.remove('loading');
            });
        }
    }

    // ==========================================
    // URL State Management
    // ==========================================
    
    function initUrlState() {
        // Update URL when filters change (for shareable filtered states)
        const params = new URLSearchParams(window.location.search);
        const productsGrid = document.getElementById('products-grid');
        const postsGrid = document.getElementById('posts-grid');
        
        // Apply search from URL (common for both)
        const urlSearch = params.get('search');
        if (urlSearch) {
            const searchInput = document.getElementById('header-search-input');
            if (searchInput) searchInput.value = urlSearch;
        }
        
        // Products grid URL state
        if (productsGrid) {
            // Apply categories from URL (multiple categories for products)
            const urlCategories = params.getAll('categories');
            if (urlCategories.length > 0) {
                urlCategories.forEach(cat => {
                    const checkbox = document.querySelector(`input[name="category"][value="${cat}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
            
            // Apply tags from URL
            const urlTags = params.getAll('tags');
            if (urlTags.length > 0) {
                urlTags.forEach(tag => {
                    const checkbox = document.querySelector(`input[name="tag"][value="${tag}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
            
            // Initial filter application
            if (urlCategories.length > 0 || urlTags.length > 0 || urlSearch) {
                filterProducts();
            }
        }
        
        // Posts grid URL state
        if (postsGrid) {
            // Apply category from URL (single category for blog)
            const urlCategory = params.get('category');
            if (urlCategory) {
                const radio = document.querySelector(`input[name="category"][value="${urlCategory}"]`);
                if (radio) radio.checked = true;
            }
            
            // Initial filter application
            if (urlCategory || urlSearch) {
                filterPosts();
            }
        }
    }
    
    function updateUrlState() {
        const selectedCategories = Array.from(
            document.querySelectorAll('input[name="category"]:checked')
        ).map(cb => cb.value);
        
        const selectedTags = Array.from(
            document.querySelectorAll('input[name="tag"]:checked')
        ).map(cb => cb.value);
        
        const searchInput = document.getElementById('header-search-input');
        const searchQuery = searchInput ? searchInput.value.trim() : '';
        
        const params = new URLSearchParams();
        selectedCategories.forEach(cat => params.append('categories', cat));
        selectedTags.forEach(tag => params.append('tags', tag));
        if (searchQuery) params.append('search', searchQuery);
        
        const newUrl = params.toString() 
            ? `${window.location.pathname}?${params.toString()}`
            : window.location.pathname;
        
        window.history.replaceState({}, '', newUrl);
    }

    // ==========================================
    // Mobile Menu Toggle (Future Enhancement)
    // ==========================================
    
    function initMobileMenu() {
        // Placeholder for mobile menu toggle functionality
        // Can be implemented when needed
    }

    // ==========================================
    // Initialize All
    // ==========================================
    
    function init() {
        initFilterAccordions();
        initCategoryFilters();
        initSearch();
        initLightbox();
        initTabs();
        initUrlState();
        initAjaxFiltering();
        initMobileMenu();
    }
    
    // Run initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();


