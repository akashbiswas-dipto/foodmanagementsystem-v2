FoodManagementSystem

FoodManagementSystem is a PHP-based web application for managing food-related operations, using MongoDB as the database. The system is designed to run on Ubuntu servers with Apache2 and PHP 8+, but can also run locally on Windows or Mac using XAMPP or MAMP.

Table of Contents

Project Overview

Technologies Used

Project Structure

Prerequisites

Installation & Setup (Server)

Apache Configuration

MongoDB Configuration

Running the Application

Running Locally (Windows / Mac)

Updating the Project

Development Workflow

Notes

Project Overview

The FoodManagementSystem is a web platform for managing food operations. It allows CRUD operations with MongoDB and provides a structured environment for managing food data efficiently.

Technologies Used

Backend: PHP 8+

Database: MongoDB (Atlas)

Server: Apache2

Dependency Management: Composer

Version Control: Git & GitHub

Project Structure

foodmanagementsystem-v2/
├── foodmanagementsystem/
│ ├── public/ - Web entry point (index.php)
│ ├── src/ - Controllers, models, and business logic
│ ├── vendor/ - Composer dependencies
│ ├── config.php - App config & DB connection
│ └── hosttype.php - BASE_URL and BASE_PATH definitions
├── README.txt
└── .gitignore

Prerequisites

Server:

Ubuntu 20.04+ with Apache2

PHP 8+ with mongodb, curl, json, mbstring extensions

Composer installed

Database: MongoDB Atlas account and credentials

Local Development (optional):

XAMPP (Windows) or MAMP (Mac)

PHP 8+ included in XAMPP/MAMP

MongoDB PHP extension installed

Installation & Setup (Server)

Clone the repository to /var/www/html/

Navigate to project folder: foodmanagementsystem-v2/foodmanagementsystem

Install dependencies using composer install (do not run composer update on server unless necessary)

Set file permissions:

Directories: 755

Files: 644

Owner: www-data

Apache Configuration

Ensure DocumentRoot points to the public folder.

Enable .htaccess support with sudo a2enmod rewrite and restart Apache.

MongoDB Configuration

The connection is set in config.php. Replace <username>, <password>, and <cluster> with your MongoDB Atlas credentials.

Running the Application

Open in a browser using your server IP or domain. If you encounter HTTP 500 errors, check Apache logs.

Running Locally (Windows / Mac)

Install XAMPP or MAMP.

Copy the foodmanagementsystem folder to htdocs.

Start Apache and MySQL.

Update hosttype.php for local URL detection.

Ensure MongoDB PHP extension is enabled.

Open in browser: http://localhost/foodmanagementsystem/public/

Updating the Project

Pull changes from GitHub using git pull origin main. Only run composer install if composer.json has new dependencies. Avoid composer update on the server.

Development Workflow

Edit files locally using VSCode.

Commit and push to GitHub.

Pull changes on the server using Git.

Restart Apache if necessary.

Notes

Keep the vendor/ folder intact on the server; do not delete unless dependencies change.

Logs are located in /var/log/apache2/.

Ensure paths in hosttype.php and config.php match the project structure.

Do not edit config.php on the server unless updating credentials or paths.