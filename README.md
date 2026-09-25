# CityNews360 - Dynamic PHP & MySQL News Portal

Welcome to **CityNews360**, a news website integrated with a **MySQL database** running locally through **XAMPP (Apache + MySQL + phpMyAdmin)**.

---

## 📋 Features & Functionality

- **Database Connection**: Configured for XAMPP MySQL at `localhost:3306` (`citynews360_db`).
- **Auto-Initialization**: Automatically creates the database `citynews360_db` and all required tables (`users`, `categories`, `articles`, `contact_messages`) along with seed data if they do not exist.
- **Manual Import Option**: Includes `database/citynews360_db.sql` for manual import via phpMyAdmin.
- **User Registration System**: Secure form validation, duplicate username/email checks, password hashing using `password_hash()`, and MySQL `users` record insertion.
- **Authentication & Login**: Database-driven login verifying credentials via `password_verify()`.
- **Role-Based Access Control**:
  - `ADMIN`: Access to Admin Dashboard (`admin.php`) for full article/category CRUD.
  - `USER`: Regular browsing, reading, liking, and commenting capabilities.
  - **Access Restriction**: Unauthenticated users or regular `USER` accounts attempting to access `admin.php` are immediately redirected with an unauthorized access error.
- **Full Database DML / CRUD Operations**:
  - **INSERT**: User Registration, Add Article, Add Category, Send Contact Message.
  - **SELECT**: View Articles, Filter by Categories, Article Detail View, Admin Overview Stats.
  - **UPDATE**: Edit Article, Edit Category, Increment Article Views, Increment Article Likes.
  - **DELETE**: Delete Article, Delete Category.
- **Preserved UI**: Original clean design, typography, Inter font, color palette, and layout.

---

## 🛠️ Requirements & Environment

- **XAMPP** (with Apache and MySQL enabled)
- **PHP** 8.x (Included with XAMPP)
- **MySQL Server** (Included with XAMPP)
- **phpMyAdmin** (Included with XAMPP)

---

## 🚀 Step-by-Step XAMPP Setup & Installation

### Step 1: Install & Start XAMPP
1. Download and install **XAMPP** on Windows (default path: `C:\xampp`).
2. Open the **XAMPP Control Panel**.
3. Click **Start** next to **Apache**.
4. Click **Start** next to **MySQL**.

### Step 2: Deploy Project to XAMPP htdocs
1. Copy the entire `CityNews360` folder to your XAMPP web root directory:
   ```
   C:\xampp\htdocs\CityNews360
   ```

### Step 3: Database Setup (Automatic or Manual)

#### Option A: Automatic Creation (Recommended)
Simply open the website in your browser (`http://localhost/CityNews360/index.php`). The application will automatically detect if `citynews360_db` exists; if not, it will:
1. Create the database `citynews360_db`.
2. Create all required tables (`users`, `categories`, `articles`, `contact_messages`).
3. Seed the default admin account and sample news articles.

#### Option B: Manual SQL Import via phpMyAdmin
1. Open your browser and navigate to:
   ```
   http://localhost/phpmyadmin
   ```
2. Click on **Import** in the top navigation menu.
3. Click **Choose File** and select:
   ```
   C:\xampp\htdocs\CityNews360\database\citynews360_db.sql
   ```
4. Click **Import** at the bottom of the page.

---

## 🔑 Database Credentials & Configuration

Database settings use standard XAMPP defaults and can be overridden with environment variables:
📄 `config/db.php`

```php
CITYNEWS360_DB_HOST=127.0.0.1
CITYNEWS360_DB_PORT=3306
CITYNEWS360_DB_NAME=citynews360_db
CITYNEWS360_DB_USER=root
CITYNEWS360_DB_PASS=
```

Set these variables in the environment used by PHP. Alternatively, change the fallback values in `config/db.php` for a local setup.

---

## 👤 Default Admin Account

| Role | Username | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` |

> 🔒 *Password is stored in MySQL using secure PHP bcrypt hashing.*

---

## 🌐 Website URLs

- **Main Homepage**: `http://localhost/CityNews360/index.php`
- **Browse Categories**: `http://localhost/CityNews360/categories.php`
- **About Us**: `http://localhost/CityNews360/about.php`
- **Contact Us**: `http://localhost/CityNews360/contact.php`
- **User Registration**: `http://localhost/CityNews360/register.php`
- **Login Page**: `http://localhost/CityNews360/login.php`
- **Admin Dashboard**: `http://localhost/CityNews360/admin.php`

---

## 📊 How to Verify Database Records in phpMyAdmin

1. Open `http://localhost/phpmyadmin`
2. Select database `citynews360_db` from the left sidebar.
3. Inspect tables:
   - `users`: Register a new user at `http://localhost/CityNews360/register.php` and refresh `users` to see their record inserted.
   - `articles`: Add, update, or delete an article from `admin.php` and observe live DML changes in MySQL.
   - `categories`: Add or delete categories from `admin.php` and observe updates in MySQL.
   - `contact_messages`: Send a message on `contact.php` and view submitted feedback in MySQL.

---

## 🧪 Testing DML Operations

- **INSERT**:
  - Go to `register.php` -> Submit new account -> Check `users` table in phpMyAdmin.
  - Go to `admin.php` -> Click **Add New Article** -> Submit form -> Check `articles` table in phpMyAdmin.
  - Go to `admin.php` -> Click **Add New Category** -> Submit form -> Check `categories` table in phpMyAdmin.
- **SELECT**:
  - Open `index.php` or `categories.php` -> Articles loaded directly from MySQL.
- **UPDATE**:
  - Open an article page (`article.php?id=1`) -> Click the **Like** button -> Views & Likes updated in MySQL `articles` table.
  - Go to `admin.php` -> Edit an article -> Check updated fields in MySQL.
- **DELETE**:
  - Go to `admin.php` -> Delete an article or category -> Record removed from MySQL.

---

## 📁 Project Structure

```
CityNews360/
├── config/
│   └── db.php                # Database connection & auto-schema creation
├── database/
│   └── citynews360_db.sql    # Complete SQL database dump for manual import
├── includes/
│   ├── auth.php              # Session management & authorization checks
│   ├── header.php            # Dynamic navigation header
│   └── footer.php            # Shared site footer
├── api/
│   ├── login.php             # Login API endpoint
│   ├── register.php          # User registration API endpoint
│   ├── articles.php          # Articles CRUD API endpoint
│   ├── categories.php        # Categories CRUD API endpoint
│   └── contact.php           # Contact submission API endpoint
├── index.php                 # Homepage (Top Stories & Trending Categories)
├── categories.php            # Categories page with live filtering & sidebar
├── article.php               # Article detail page (Views, Likes, Related Articles)
├── about.php                 # About Us page
├── contact.php               # Contact Us page
├── login.php                 # Login page
├── register.php              # Registration page
├── logout.php                # Logout handler
├── admin.php                 # Admin Dashboard (Protected access control)
├── styles.css                # CSS Stylesheet
├── script.js                 # Frontend JavaScript & AJAX integration
└── README.md                 # Setup & Usage Guide
```
