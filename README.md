# InfernoFramework - The Ultimate Lightweight PHP Framework

InfernoFramework is a blazing-fast, lightweight, and highly extensible PHP framework designed for modern web development. Built with efficiency and flexibility in mind, InfernoFramework simplifies application development with structured architecture, automatic class loading, and seamless database integration. Whether you're developing a small web app or a large-scale system, InfernoFramework provides the perfect foundation.

<p align="center">
  <a href="https://infernovm.net">
    <img src="https://i.imgur.com/l5cRB9s.gif" alt="InfernoVM">
  </a>
</p>


## 🔥 Key Features
- **Lightweight & Fast** – Optimized for speed and performance
- **Automatic Class Loading** – No manual includes, just create and use
- **Secure & Scalable** – Built with security best practices in mind
- **Database Ready** – Easy-to-use database connection management
- **Extensible** – Easily add new functionalities with minimal effort

## 🚀 Installation & Setup
1. **Install Dependencies with Composer:**
   ```sh
   composer install
   ```
2. **Configure Environment Variables:**
   Edit the `/core/.env` file to set up your database and environment settings:
   ```env
   DB_HOST_LOCAL=your_localhost
   DB_USER_LOCAL=your_local_user
   DB_PASS_LOCAL=your_local_password
   DB_NAME_LOCAL=your_local_db

   DB_HOST_REMOTE=your_remote_host
   DB_USER_REMOTE=your_remote_user
   DB_PASS_REMOTE=your_remote_password
   DB_NAME_REMOTE=your_remote_db
   ```

## 🔧 Framework Initialization
InfernoFramework initializes automatically via `/core/init.php`, which:
- Loads environment variables from `.env`
- Sets up database configurations (local or remote)
- Automatically loads classes from `/core/classes/`

## 🛠 Adding New Classes
To extend the framework, follow these simple steps:
1. **Create a New Class File:** Place it inside `/core/classes/`, following the naming convention `ClassName.class.php`.
2. **Define Your Class:**
   ```php
   <?php
   if (!defined('rootsec')) {
       die('Direct access not permitted');
   }

   class ClassName {
       public function exampleFunction() {
           return "Hello, Inferno!";
       }
   }
   ?>
   ```
3. **Start Using Your Class Immediately** – The framework’s autoloader will handle it.

## 📌 Usage Guide
### 🔹 Helper Class
The `Helper` class provides essential utility functions to streamline development.
#### Example Usage:
```php
$Helper = new Helper($DataBase);

// Sanitize user input
$cleanInput = $Helper->sanitizeInput($dirtyInput);

// Get the current domain
$domain = $Helper->currentDomain();

// Validate email format
$isValid = $Helper->validateEmail("test@example.com");
```

### 🔹 SiteConfig Class
The `SiteConfig` class manages site configuration settings stored in the database.
#### Example Usage:
```php
$SiteConfig = new SiteConfig($DataBase);

// Retrieve site name
$siteName = $SiteConfig->getSiteName();

// Retrieve domain information
$domain = $SiteConfig->getDomain();
```

### 🔹 UserManager Class
The `UserManager` class handles user authentication and account management, including login, registration, and password recovery.
#### Example Usage:
```php
// Register a new user
$response = $UserManager->register("username", "email@example.com", "password123", "recaptcha_token");

// Log in a user
$response = $UserManager->login("email@example.com", "password123", "recaptcha_token");

// Request password reset
$response = $UserManager->forgotPassword("email@example.com", "recaptcha_token");
```

## 🌐 API Handling with `api.php`
InfernoFramework includes an `api.php` file that handles API requests through structured classes. The API allows secure interaction with user management features such as login, registration, password reset, and account updates.

## 🌍 URL Routing
InfernoFramework uses an `.htaccess` file for clean URL routing and API endpoint handling:
This ensures:

- Clean URLs for API requests
- Pretty URLs for web pages
- Automatic redirection to HTTPS and non-www version
- Route for API is yourdomain.com/api/action - api.php
- Route for Views / Pages is yourdomain.com/page - index.php



## 🔗 Get Started Today!
Start building with InfernoFramework and experience the power of a fast, lightweight, and highly extensible PHP framework. If you need help, feel free to open an issue on GitHub. Join the growing community of developers who trust InfernoFramework for their projects! 🔥

