-- CityNews360 Database Schema & Sample Data for MySQL / XAMPP
-- Database: citynews360_db

CREATE DATABASE IF NOT EXISTS `citynews360_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `citynews360_db`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('ADMIN', 'USER') DEFAULT 'USER',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `categories`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `articles`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `articles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `excerpt` TEXT NOT NULL,
  `content` TEXT NOT NULL,
  `image_url` VARCHAR(500) DEFAULT '',
  `category_id` INT NOT NULL,
  `author_id` INT DEFAULT NULL,
  `author_name` VARCHAR(100) DEFAULT 'Admin',
  `views` INT DEFAULT 0,
  `likes` INT DEFAULT 0,
  `tags` VARCHAR(255) DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_articles_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_articles_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `contact_messages`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(255),
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Sample Data Insertion
-- Default Admin Account: admin / admin123
-- --------------------------------------------------------

INSERT INTO `users` (`id`, `full_name`, `email`, `username`, `password`, `role`) VALUES
(1, 'System Administrator', 'admin@citynews360.com', 'admin', '$2y$10$j47D3VMy8qCWESsfpU1BdunDzBo/heNvPb4GJlWSt/X5WlA5cIJW2', 'ADMIN'),
(2, 'John Doe', 'john@example.com', 'johndoe', '$2y$10$23mX6W1Q4WVIu3m0P6Mzpu7ptUVXKoIJY3P3Xw2wQ7KQHjvOSpc8u', 'USER')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Note: The admin hash above matches 'admin123'; the sample user hash above matches 'john123'

INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Tech', 'tech', 'Latest in technology and innovation'),
(2, 'Sports', 'sports', 'Sports news and updates'),
(3, 'Lifestyle', 'lifestyle', 'Health, fashion, and lifestyle trends')
ON DUPLICATE KEY UPDATE `id`=`id`;

INSERT INTO `articles` (`id`, `title`, `excerpt`, `content`, `image_url`, `category_id`, `author_id`, `author_name`, `views`, `likes`, `tags`) VALUES
(1, 'The Future of Artificial Intelligence in 2024', 'Discover the latest breakthroughs in AI technology and how they\'re shaping our world.', '<h2>The Rise of AI in Modern Technology</h2><p>Artificial Intelligence has become one of the most transformative technologies of our time. From autonomous vehicles to smart home systems, AI is revolutionizing how we live and work.</p><h2>Key Developments in 2024</h2><p>This year has seen remarkable progress in machine learning algorithms, natural language processing, and computer vision. Companies are investing billions in AI research and development.</p><h2>Impact on Various Industries</h2><p>Healthcare, finance, education, and manufacturing are just a few sectors experiencing the AI revolution. The technology is creating new opportunities while also raising important questions about ethics and job displacement.</p><h2>Looking Ahead</h2><p>As we move forward, the integration of AI into everyday life will continue to accelerate. It\'s crucial for individuals and organizations to understand and adapt to these changes.</p>', 'https://images.unsplash.com/photo-1677442136019-21780efad99a?auto=format&fit=crop&w=800&q=80', 1, 1, 'Sarah Johnson', 1250, 89, '#Tech,#AI,#Innovation'),
(2, 'Championship Finals: A Historic Victory', 'Relive the thrilling moments of this year\'s championship game that will be remembered for generations.', '<h2>The Road to Victory</h2><p>After months of intense competition, the championship finals delivered one of the most memorable games in recent history. The underdog team\'s journey to the top captured the hearts of millions.</p><h2>Key Moments of the Game</h2><p>From the opening whistle to the final buzzer, every moment was filled with excitement. The game went into overtime, keeping fans on the edge of their seats until the very end.</p><h2>Player Performance</h2><p>Several players delivered career-defining performances, with the MVP award going to a player who scored the winning goal in the final seconds of overtime.</p><h2>Celebration and Aftermath</h2><p>The victory parade drew thousands of fans, celebrating not just a win, but the spirit of determination and teamwork that led to this historic moment.</p>', 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=800&q=80', 2, 1, 'Mike Thompson', 2100, 156, '#Sports,#Championship,#Victory'),
(3, 'Wellness Trends That Are Here to Stay', 'Explore the latest wellness and lifestyle trends that are transforming how we approach health and happiness.', '<h2>Mindfulness and Mental Health</h2><p>Mental wellness has taken center stage in 2024, with more people embracing mindfulness practices, meditation, and stress-reduction techniques.</p><h2>Plant-Based Living</h2><p>The shift toward plant-based diets continues to grow, driven by health benefits, environmental concerns, and ethical considerations.</p><h2>Digital Detox</h2><p>As technology becomes more pervasive, people are increasingly seeking ways to disconnect and find balance in their digital lives.</p><h2>Sustainable Living</h2><p>From eco-friendly products to sustainable fashion, consumers are making choices that align with their environmental values.</p>', 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&w=800&q=80', 3, 1, 'Emma Davis', 980, 67, '#Lifestyle,#Wellness,#Health')
ON DUPLICATE KEY UPDATE `id`=`id`;
rtical