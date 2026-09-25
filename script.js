// CityNews360 - Main JavaScript File connected to PHP / MySQL Backend

document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize the application based on current page
function initializeApp() {
    const currentPage = getCurrentPage();
    
    switch(currentPage) {
        case 'index':
            setupHomePage();
            break;
        case 'categories':
            setupCategoriesPage();
            setupAdminPage();
            break;
        case 'article':
            setupArticlePage();
            break;
        case 'about':
            setupAboutPage();
            break;
        case 'contact':
            setupContactPage();
            break;
        case 'login':
            setupLoginPage();
            break;
        case 'register':
            setupRegisterPage();
            break;
        case 'admin':
            setupAdminPage();
            break;
    }
    
    setupGlobalEventListeners();
    initializeAnimations();
}

// Get current page
function getCurrentPage() {
    const path = window.location.pathname;
    if (path.includes('categories.php') || path.includes('categories.html')) return 'categories';
    if (path.includes('article.php') || path.includes('article.html')) return 'article';
    if (path.includes('about.php') || path.includes('about.html')) return 'about';
    if (path.includes('contact.php') || path.includes('contact.html')) return 'contact';
    if (path.includes('login.php')) return 'login';
    if (path.includes('register.php')) return 'register';
    if (path.includes('admin.php') || path.includes('admin.html')) return 'admin';
    return 'index';
}

// Global Event Listeners
function setupGlobalEventListeners() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.category-card')) {
            const category = e.target.closest('.category-card').dataset.category;
            window.location.href = `categories.php?category=${category}`;
        }
    });
}

// Animations Observer
function initializeAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.story-card, .category-card, .stat-card').forEach(el => {
        observer.observe(el);
    });
}

// ----------------------------------------------------
// HOME PAGE
// ----------------------------------------------------
function setupHomePage() {
    loadTopStories();
    loadTrendingCategories();
}

function fetchCategories() {
    return fetch('api/categories.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.categories) && data.categories.length > 0) {
                return data.categories;
            }
            return [
                { id: 1, name: 'Tech', slug: 'tech', description: 'Latest in technology and innovation' },
                { id: 2, name: 'Sports', slug: 'sports', description: 'Sports news and updates' },
                { id: 3, name: 'Lifestyle', slug: 'lifestyle', description: 'Health, fashion, and lifestyle trends' }
            ];
        })
        .catch(() => [
            { id: 1, name: 'Tech', slug: 'tech', description: 'Latest in technology and innovation' },
            { id: 2, name: 'Sports', slug: 'sports', description: 'Sports news and updates' },
            { id: 3, name: 'Lifestyle', slug: 'lifestyle', description: 'Health, fashion, and lifestyle trends' }
        ]);
}

function loadTrendingCategories() {
    const categoriesGrid = document.querySelector('.categories-grid');
    if (!categoriesGrid) return;

    fetchCategories().then(categories => {
        categoriesGrid.innerHTML = categories.map((category, index) => `
            <div class="category-card" data-category="${escapeHtml(category.slug || category.name.toLowerCase())}" style="animation-delay: ${index * 0.1}s">
                <div class="category-icon">${escapeHtml(category.name.charAt(0) || '•')}</div>
                <h3>${escapeHtml(category.name)}</h3>
                <p>${escapeHtml(category.description || 'Latest updates in this category.')}</p>
            </div>
        `).join('');
    });
}

function loadTopStories() {
    const storiesGrid = document.getElementById('storiesGrid');
    if (!storiesGrid) return;

    fetch('api/articles.php?top=4')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.articles) {
                if (data.articles.length === 0) {
                    storiesGrid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #64748B;">No top stories available.</p>';
                    return;
                }
                storiesGrid.innerHTML = data.articles.map((article, index) => `
                    <div class="story-card" style="animation-delay: ${index * 0.1}s">
                        <img src="${escapeHtml(article.image_url)}" alt="${escapeHtml(article.title)}" class="story-image">
                        <div class="story-content">
                            <h3 class="story-title">${escapeHtml(article.title)}</h3>
                            <p class="story-excerpt">${escapeHtml(article.excerpt)}</p>
                            <a href="article.php?id=${article.id}" class="read-more-btn">Read More</a>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(err => {
            console.error('Error loading top stories:', err);
        });
}

// ----------------------------------------------------
// CATEGORIES PAGE
// ----------------------------------------------------
function setupCategoriesPage() {
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('category') || 'all';

    loadCategoryTabs(categoryParam);
    loadArticlesByCategory(categoryParam);
    loadSidebarContent();
}

function loadCategoryTabs(initialCategory = 'all') {
    const categorySelector = document.getElementById('categorySelector');
    if (!categorySelector) return;

    fetchCategories().then(categories => {
        const tabs = [{ name: 'All', slug: 'all' }, ...categories];
        categorySelector.innerHTML = tabs.map((category) => {
            const slug = category.slug || category.name.toLowerCase();
            const isActive = (slug === initialCategory.toLowerCase()) || (category.name === 'All' && initialCategory === 'all');
            return `<button class="category-tab ${isActive ? 'active' : ''}" data-category="${escapeHtml(slug)}">${escapeHtml(category.name)}</button>`;
        }).join('');

        setupCategoryTabs(initialCategory);
    });
}

function loadArticlesByCategory(category) {
    const articlesGrid = document.getElementById('articlesGrid');
    if (!articlesGrid) return;

    articlesGrid.innerHTML = '<p style="text-align: center; grid-column: 1/-1;">Loading articles...</p>';

    fetch(`api/articles.php?category=${encodeURIComponent(category)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.articles) {
                if (data.articles.length === 0) {
                    articlesGrid.innerHTML = '<p style="text-align: center; grid-column: 1 / -1; color: #6B7280; padding: 2rem;">No articles found in this category.</p>';
                    return;
                }

                articlesGrid.innerHTML = data.articles.map(article => `
                    <div class="story-card">
                        <img src="${escapeHtml(article.image_url)}" alt="${escapeHtml(article.title)}" class="story-image">
                        <div class="story-content">
                            <h3 class="story-title">${escapeHtml(article.title)}</h3>
                            <p class="story-excerpt">${escapeHtml(article.excerpt)}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                                <span style="color: #2563EB; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">${escapeHtml(article.category_name)}</span>
                                <a href="article.php?id=${article.id}" class="read-more-btn">Read More</a>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(err => console.error('Error loading categories articles:', err));
}

function setupCategoryTabs(initialCategory) {
    const tabs = document.querySelectorAll('.category-tab');
    
    tabs.forEach(tab => {
        const tabValue = (tab.dataset.category || '').toLowerCase();
        const target = (initialCategory || 'all').toLowerCase();
        if (tabValue === target) {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
        }

        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const cat = this.dataset.category;
            const url = new URL(window.location.href);
            if (cat && cat !== 'all') {
                url.searchParams.set('category', cat);
            } else {
                url.searchParams.delete('category');
            }
            window.history.pushState({}, '', url);
            loadArticlesByCategory(cat || 'all');
        });
    });
}

function loadSidebarContent() {
    // Recent posts
    fetch('api/articles.php')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.articles) {
                const recentPosts = document.getElementById('recentPosts');
                const mostViewed = document.getElementById('mostViewed');

                if (recentPosts) {
                    const recent = data.articles.slice(0, 3);
                    recentPosts.innerHTML = recent.map(art => `
                        <div class="recent-post" onclick="window.location.href='article.php?id=${art.id}'" style="cursor: pointer;">
                            <img src="${escapeHtml(art.image_url)}" alt="${escapeHtml(art.title)}">
                            <div class="recent-post-content">
                                <h4>${escapeHtml(art.title)}</h4>
                                <p>${new Date(art.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    `).join('');
                }

                if (mostViewed) {
                    const popular = [...data.articles].sort((a, b) => b.views - a.views).slice(0, 3);
                    mostViewed.innerHTML = popular.map(art => `
                        <div class="most-viewed-post" onclick="window.location.href='article.php?id=${art.id}'" style="cursor: pointer;">
                            <img src="${escapeHtml(art.image_url)}" alt="${escapeHtml(art.title)}">
                            <div class="most-viewed-post-content">
                                <h4>${escapeHtml(art.title)}</h4>
                                <p>${art.views} views</p>
                            </div>
                        </div>
                    `).join('');
                }
            }
        });
}

// ----------------------------------------------------
// ARTICLE DETAIL PAGE
// ----------------------------------------------------
function setupArticlePage() {
    setupLikeButton();
    setupShareButtons();
}

function setupLikeButton() {
    const likeBtn = document.getElementById('likeBtn');
    if (!likeBtn) return;

    likeBtn.addEventListener('click', function() {
        const articleId = this.dataset.id;
        if (!articleId) return;

        fetch('api/articles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'like', id: articleId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('likeCount').textContent = data.likes;
                likeBtn.style.transform = 'scale(1.15)';
                setTimeout(() => { likeBtn.style.transform = 'scale(1)'; }, 200);
            }
        })
        .catch(err => console.error('Error liking article:', err));
    });
}

function setupShareButtons() {
    document.querySelectorAll('.share-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const platform = this.dataset.platform;
            const url = window.location.href;
            const title = document.title;
            
            let shareUrl = '';
            if (platform === 'twitter') shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
            if (platform === 'facebook') shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            if (platform === 'linkedin') shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
            
            if (shareUrl) window.open(shareUrl, '_blank', 'width=600,height=400');
        });
    });
}

// ----------------------------------------------------
// ABOUT & CONTACT PAGES
// ----------------------------------------------------
function setupAboutPage() {}

function setupContactPage() {
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch('api/contact.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resData => {
            alert(resData.message);
            if (resData.success) contactForm.reset();
        })
        .catch(err => alert('Failed to send message. Please try again.'));
    });
}

// ----------------------------------------------------
// LOGIN & REGISTER PAGES
// ----------------------------------------------------
function setupLoginPage() {
    const pageLoginForm = document.getElementById('pageLoginForm');
    const alertBox = document.getElementById('loginAlert');
    const closeAlertButton = alertBox ? alertBox.querySelector('.login-alert-close') : null;
    const okAlertButton = alertBox ? alertBox.querySelector('.login-alert-ok') : null;

    if (closeAlertButton) {
        closeAlertButton.addEventListener('click', function() {
            if (alertBox) alertBox.classList.remove('is-visible');
        });
    }

    if (okAlertButton) {
        okAlertButton.addEventListener('click', function() {
            if (alertBox) alertBox.classList.remove('is-visible');
        });
    }

    if (!pageLoginForm) return;

    pageLoginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resData => {
            if (resData.success) {
                if (alertBox) {
                    const message = alertBox.querySelector('.login-alert-message');
                    alertBox.classList.remove('is-error');
                    alertBox.classList.add('is-success', 'is-visible');
                    if (message) message.textContent = '✅ ' + resData.message;
                }
                setTimeout(() => {
                    window.location.href = resData.redirect;
                }, 800);
            } else {
                if (alertBox) {
                    const message = alertBox.querySelector('.login-alert-message');
                    alertBox.classList.remove('is-success');
                    alertBox.classList.add('is-error', 'is-visible');
                    if (message) message.textContent = '⚠️ ' + resData.message;
                } else {
                    alert(resData.message);
                }
            }
        })
        .catch(() => {
            if (alertBox) {
                const message = alertBox.querySelector('.login-alert-message');
                alertBox.classList.remove('is-success');
                alertBox.classList.add('is-error', 'is-visible');
                if (message) message.textContent = '⚠️ Login error occurred. Please try again.';
            } else {
                alert('Login error occurred. Please try again.');
            }
        });
    });
}

function setupRegisterPage() {
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;

    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const alertBox = document.getElementById('registerAlert');
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch('api/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resData => {
            if (resData.success) {
                if (alertBox) {
                    alertBox.style.display = 'block';
                    alertBox.style.background = '#D1FAE5';
                    alertBox.style.color = '#065F46';
                    alertBox.textContent = '✅ ' + resData.message;
                }
                setTimeout(() => {
                    window.location.href = 'login.php?msg=registered';
                }, 1000);
            } else {
                if (alertBox) {
                    alertBox.style.display = 'block';
                    alertBox.style.background = '#FEE2E2';
                    alertBox.style.color = '#991B1B';
                    alertBox.textContent = '⚠️ ' + resData.message;
                } else {
                    alert(resData.message);
                }
            }
        })
        .catch(err => alert('Registration failed. Please try again.'));
    });
}

// ----------------------------------------------------
// ADMIN DASHBOARD
// ----------------------------------------------------
function setupAdminPage() {
    // Navigation Tabs
    document.querySelectorAll('.admin-nav-btn[data-section]').forEach(btn => {
        btn.addEventListener('click', function() {
            const section = this.dataset.section;
            document.querySelectorAll('.admin-nav-btn[data-section]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
            const targetSection = document.getElementById(section);
            if (targetSection) targetSection.classList.add('active');
        });
    });

    // Article Modal Triggers
    const addArticleBtn = document.getElementById('addArticleBtn');
    if (addArticleBtn) {
        addArticleBtn.addEventListener('click', () => showArticleModal());
    }

    const closeModal = document.getElementById('closeModal');
    const cancelArticle = document.getElementById('cancelArticle');
    if (closeModal) closeModal.addEventListener('click', closeArticleModal);
    if (cancelArticle) cancelArticle.addEventListener('click', closeArticleModal);

    const categoriesAddArticleBtn = document.getElementById('categoriesAddArticleBtn');
    if (categoriesAddArticleBtn) {
        categoriesAddArticleBtn.addEventListener('click', () => {
            const activeCategory = document.querySelector('.category-tab.active');
            showArticleModal(null, activeCategory ? activeCategory.dataset.category : null);
        });
    }

    // Category Modal Triggers
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    if (addCategoryBtn) {
        addCategoryBtn.addEventListener('click', () => showCategoryModal());
    }

    const closeCategoryModalBtn = document.getElementById('closeCategoryModal');
    const cancelCategory = document.getElementById('cancelCategory');
    if (closeCategoryModalBtn) closeCategoryModalBtn.addEventListener('click', closeCategoryModal);
    if (cancelCategory) cancelCategory.addEventListener('click', closeCategoryModal);

    // Article Form Submit
    const articleForm = document.getElementById('articleForm');
    if (articleForm) {
        articleForm.addEventListener('submit', handleArticleSubmit);
    }

    // Category Form Submit
    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        categoryForm.addEventListener('submit', handleCategorySubmit);
    }
}

// Article Modals & Actions
function populateArticleCategorySelect(preferredCategoryId = null) {
    const categorySelect = document.getElementById('articleCategory');
    if (!categorySelect) return;

    fetch('api/categories.php')
        .then(res => res.json())
        .then(data => {
            const categories = data.success && Array.isArray(data.categories) ? data.categories : [];
            categorySelect.innerHTML = '<option value="">Select Category</option>' + categories.map(cat => `
                <option value="${cat.id}">${escapeHtml(cat.name)}</option>
            `).join('');

            if (preferredCategoryId) {
                const match = categories.find(cat =>
                    String(cat.id) === String(preferredCategoryId) ||
                    (cat.slug && cat.slug.toLowerCase() === String(preferredCategoryId).toLowerCase()) ||
                    (cat.name && cat.name.toLowerCase() === String(preferredCategoryId).toLowerCase())
                );
                categorySelect.value = match ? String(match.id) : '';
            } else {
                categorySelect.value = '';
            }
        })
        .catch(() => {
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            categorySelect.value = '';
        });
}

function showArticleModal(articleId = null, preferredCategoryId = null) {
    const modal = document.getElementById('articleModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('articleForm');
    const categorySelect = document.getElementById('articleCategory');
    if (!modal || !form) return;

    populateArticleCategorySelect(preferredCategoryId);

    if (articleId) {
        modalTitle.textContent = 'Edit Article';
        document.getElementById('articleId').value = articleId;

        fetch(`api/articles.php?id=${articleId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.article) {
                    const art = data.article;
                    document.getElementById('articleTitle').value = art.title;
                    categorySelect.value = String(art.category_id);
                    document.getElementById('articleImage').value = art.image_url;
                    document.getElementById('articleContent').value = art.content.replace(/<br\s*\/?>/gi, '\n');
                }
            });
    } else {
        modalTitle.textContent = 'Add New Article';
        form.reset();
        document.getElementById('articleId').value = '';
    }

    modal.classList.add('active');
}

window.openArticleForCategory = function(categoryId) {
    showArticleModal(null, categoryId);
    const modal = document.getElementById('articleModal');
    if (modal) {
        modal.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

function closeArticleModal() {
    const modal = document.getElementById('articleModal');
    if (modal) modal.classList.remove('active');
}

function handleArticleSubmit(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    data.action = data.id ? 'update' : 'create';

    fetch('api/articles.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(resData => {
        alert(resData.message);
        if (resData.success) {
            closeArticleModal();
            window.location.reload();
        }
    })
    .catch(err => alert('Failed to save article.'));
}

window.editArticle = function(id) {
    showArticleModal(id);
};

window.deleteArticle = function(id) {
    if (confirm('Are you sure you want to delete this article? This operation cannot be undone.')) {
        fetch('api/articles.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', id: id })
        })
        .then(res => res.json())
        .then(resData => {
            alert(resData.message);
            if (resData.success) {
                window.location.reload();
            }
        })
        .catch(err => alert('Failed to delete article.'));
    }
};

// Category Modals & Actions
function showCategoryModal(id = null, name = '', description = '') {
    const modal = document.getElementById('categoryModal');
    const modalTitle = document.getElementById('categoryModalTitle');
    const form = document.getElementById('categoryForm');
    if (!modal || !form) return;

    if (id) {
        modalTitle.textContent = 'Edit Category';
        document.getElementById('categoryId').value = id;
        document.getElementById('categoryName').value = name;
        document.getElementById('categoryDescription').value = description;
    } else {
        modalTitle.textContent = 'Add New Category';
        form.reset();
        document.getElementById('categoryId').value = '';
    }

    modal.classList.add('active');
}

function closeCategoryModal() {
    const modal = document.getElementById('categoryModal');
    if (modal) modal.classList.remove('active');
}

function handleCategorySubmit(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    data.action = data.id ? 'update' : 'create';

    fetch('api/categories.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(resData => {
        alert(resData.message);
        if (resData.success) {
            closeCategoryModal();
            window.location.reload();
        }
    })
    .catch(err => alert('Failed to save category.'));
}

window.editCategory = function(id, name, description) {
    showCategoryModal(id, name, description);
};

window.deleteCategory = function(id) {
    if (confirm('Are you sure you want to delete this category? All related articles will also be updated or removed.')) {
        fetch('api/categories.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', id: id })
        })
        .then(res => res.json())
        .then(resData => {
            alert(resData.message);
            if (resData.success) {
                window.location.reload();
            }
        })
        .catch(err => alert('Failed to delete category.'));
    }
};

// Helper function to escape HTML
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}