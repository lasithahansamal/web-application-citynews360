<?php
$pageTitle = "About Us - CityNews360";
$activePage = "about";
require_once __DIR__ . '/includes/header.php';
?>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h1>About CityNews360</h1>
                    <p class="mission-statement">
                        We are dedicated to bringing you the most relevant and up-to-date news from around the world. 
                        Our mission is to provide accurate, timely, and engaging content that keeps you informed about 
                        what matters most in technology, sports, lifestyle, and beyond.
                    </p>
                    <p>
                        Founded with the vision of creating a trusted news platform, CityNews360 has grown to become 
                        a reliable source for millions of readers worldwide. Our team of experienced journalists and 
                        editors work tirelessly to deliver high-quality content that you can count on.
                    </p>
                    <div class="stats">
                        <div class="stat-item">
                            <h3>1M+</h3>
                            <p>Monthly Readers</p>
                        </div>
                        <div class="stat-item">
                            <h3>500+</h3>
                            <p>Articles Published</p>
                        </div>
                        <div class="stat-item">
                            <h3>24/7</h3>
                            <p>News Coverage</p>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1585829365295-ab7cd400c167?auto=format&fit=crop&w=600&q=80" alt="CityNews360 Team" class="team-image">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-content">
                <div class="contact-info">
                    <h2>Get in Touch</h2>
                    <p>Have a question, suggestion, or want to contribute? We'd love to hear from you!</p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="contact-icon">📧</div>
                            <div>
                                <h4>Email</h4>
                                <p>info@citynews360.com</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">📞</div>
                            <div>
                                <h4>Phone</h4>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">📍</div>
                            <div>
                                <h4>Address</h4>
                                <p>123 News Street, Media City, MC 12345</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-form-container">
                    <form class="contact-form" id="contactForm">
                        <h3>Send us a Message</h3>
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject">
                        </div>
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <h2>Find Us</h2>
            <div class="map-container">
                <img src="https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=1200&q=80" alt="Location Map" class="map-image">
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
