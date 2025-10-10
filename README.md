# FoodManagementSystem

FoodManagementSystem is a **PHP-based web application** designed for efficient management of food-related operations. It uses **MongoDB** as its database, allowing for streamlined **CRUD (Create, Read, Update, Delete) operations** within a structured environment.

## 🌟 Technologies Used

| Category | Technology | Version / Notes |
| :--- | :--- | :--- |
| **Backend** | PHP | 8+ |
| **Database** | MongoDB | Atlas (Cloud or Local) |
| **Server** | Apache2 | Required for deployment |
| **Dependency Management** | Composer | Used for PHP package management |
| **Version Control** | Git & GitHub | For source code management |

## 📁 Project Structure

├── foodmanagementsystem/
│ ├── public/     
│ ├── api/
│ ├── patterns/         # Web entry point (index.php) and assets
│ ├── src/              # Controllers, models, and business logic
│ ├── vendor/           # Composer dependencies
│ ├── config.php        # Application configuration & MongoDB connection settings
│ └── hosttype.php      # BASE_URL and BASE_PATH definitions for environment detection
│ ├── README.md          # This file
│ └── .gitignore

Here is a complete, working `README.md` file based on the provided project documentation.

```markdown
# FoodManagementSystem

FoodManagementSystem is a **PHP-based web application** designed for efficient management of food-related operations. It uses **MongoDB** as its database, allowing for streamlined **CRUD (Create, Read, Update, Delete) operations** within a structured environment.

## 🌟 Technologies Used

| Category | Technology | Version / Notes |
| :--- | :--- | :--- |
| **Backend** | PHP | 8+ |
| **Database** | MongoDB | Atlas (Cloud or Local) |
| **Server** | Apache2 | Required for deployment |
| **Dependency Management** | Composer | Used for PHP package management |
| **Version Control** | Git & GitHub | For source code management |

## 📁 Project Structure

```

foodmanagementsystem-v2/
├── foodmanagementsystem/
│ ├── public/           \# Web entry point (index.php) and assets
│ ├── src/              \# Controllers, models, and business logic
│ ├── vendor/           \# Composer dependencies
│ ├── config.php        \# Application configuration & MongoDB connection settings
│ └── hosttype.php      \# BASE\_URL and BASE\_PATH definitions for environment detection
├── README.md           \# This file
└── .gitignore

````

---

## ⚙️ Prerequisites

### Server Deployment

* **Operating System:** Ubuntu 20.04+
* **Web Server:** Apache2
* **PHP:** Version 8+ with the following extensions:
    * `mongodb`
    * `curl`
    * `json`
    * `mbstring`
* **Tools:** Composer and Git installed.
* **Database:** MongoDB Atlas account and credentials.

### Local Development (Windows / Mac)

* **Software Stack:** XAMPP (Windows) or MAMP (Mac) installed.
* **PHP:** Version 8+ included in XAMPP/MAMP.
* **Database:** MongoDB PHP extension enabled/installed in the local PHP environment.

---

## 🚀 Installation & Setup

### A. Server Deployment (Ubuntu / Apache2)

1.  **Clone the Repository:**
    Clone the project into your web root directory, typically `/var/www/html/`.

    ```bash
    cd /var/www/html/
    git clone <repository-url> foodmanagementsystem-v2
    cd foodmanagementsystem-v2/foodmanagementsystem
    ```

2.  **Install Dependencies:**
    Navigate to the `foodmanagementsystem` directory and install the required dependencies.

    ```bash
    composer install
    # NOTE: Do NOT run 'composer update' on the server unless strictly necessary.
    ```

3.  **Set File Permissions:**
    Set the correct permissions and ownership for the Apache user (`www-data`).

    ```bash
    # Directories
    find . -type d -exec chmod 755 {} \;
    # Files
    find . -type f -exec chmod 644 {} \;
    # Owner
    sudo chown -R www-data:www-data /var/www/html/foodmanagementsystem-v2
    ```

4.  **Apache Configuration:**
    * Ensure your **DocumentRoot** is configured to point to the `public/` folder within the project.
    * Enable the Apache `rewrite` module for `.htaccess` support and restart the service:

        ```bash
        sudo a2enmod rewrite
        sudo systemctl restart apache2
        ```

5.  **MongoDB Configuration:**
    Edit the **`config.php`** file and replace the placeholder credentials with your actual MongoDB Atlas connection details:

    ```php
    // Example inside config.php
    $mongo_uri = "mongodb+srv://<username>:<password>@<cluster>.mongodb.net/...";
    // Replace <username>, <password>, and <cluster> with your details.
    ```

6.  **Running the Application:**
    Open your server's IP address or domain name in a web browser. If you encounter an **HTTP 500 error**, check the Apache logs located in `/var/log/apache2/`.

---

### B. Local Development (Windows / Mac)

1.  **Installation:**
    Install and launch **XAMPP** (Windows) or **MAMP** (Mac).

2.  **Move Project Folder:**
    Copy the `foodmanagementsystem` folder to your local server's web root directory (e.g., `htdocs` for XAMPP).

3.  **Start Services:**
    Start **Apache** (and **MySQL**, though it's not the primary database, it's often started with the stack).

4.  **MongoDB Extension:**
    Ensure the **MongoDB PHP extension** is properly enabled in your local PHP configuration (`php.ini`).

5.  **Configure Local URL:**
    Update the **`hosttype.php`** file to correctly detect and use the local URL structure.

6.  **Access:**
    Open the application in your browser:
    `http://localhost/foodmanagementsystem/public/`

---

## 🔄 Updating the Project

When updating the project on the server:

1.  **Pull Changes:**
    ```bash
    cd /path/to/foodmanagementsystem-v2/foodmanagementsystem
    git pull origin main
    ```

2.  **Update Dependencies (if needed):**
    Only run `composer install` if the `composer.json` file has new dependencies. **Avoid running `composer update` on the production server.**

3.  **Restart Apache:**
    If changes involve core configurations or business logic, a restart is often a good practice.
    ```bash
    sudo systemctl restart apache2
    ```

---

## 💡 Development Workflow

1.  Edit files locally using your preferred IDE (e.g., VSCode).
2.  Commit changes and push to your GitHub repository.
3.  Pull the changes onto the server using `git pull`.
4.  Restart Apache if necessary.

---

## 📝 Notes & Troubleshooting

* **Vendor Folder:** Keep the `vendor/` folder intact on the server. Do not delete it unless you are explicitly managing dependency changes via Composer.
* **Logging:** Server logs for Apache are located in `/var/log/apache2/`.
* **Configuration:** Ensure all paths defined in `hosttype.php` and `config.php` accurately reflect the project structure and server environment.
* **Credentials:** Avoid editing `config.php` directly on the server unless you are specifically updating database credentials or core application paths.
````