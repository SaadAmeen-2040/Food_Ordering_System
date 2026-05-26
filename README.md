# 🍔 Online Food Ordering System

A complete, responsive, and beautifully designed Online Food Ordering System built with PHP, MySQL, HTML, CSS, JavaScript, and Bootstrap. 
This system provides a seamless experience for customers to browse the menu, add items to their cart, and place orders. It also features a powerful Admin Portal to manage the entire restaurant menu, track orders, and view revenue.

---

## ✨ Features

### 🛒 Frontend (Customer Experience)
- **Modern & Responsive UI**: Beautiful glassmorphism, hover animations, and vibrant styling. Fully optimized for all devices (Mobile, Tablet, Desktop).
- **Interactive Menu**: Users can browse foods by categories or view the full menu.
- **Dynamic Cart System**: Add items, update quantities, calculate subtotal and delivery fees dynamically.
- **Checkout Process**: Secure and straightforward order placement.
- **User Dashboard**: Customers can track their order history and view detailed order summaries.
- **Authentication**: Complete user registration and login system.
- **AI Chatbot**: Gemini-powered AI assistant available on all pages to help customers with menu recommendations and general queries.

### 🛡️ Backend (Admin Portal)
- **Secure Admin Login**: Protected admin routing and session management.
- **Dashboard Overview**: Track total orders, revenue, and recent activity.
- **Manage Categories**: Add, edit (with image uploads), and delete food categories.
- **Manage Foods**: Full CRUD (Create, Read, Update, Delete) for menu items with pricing and image uploads.
- **Order Management**: View incoming orders, customer details, and update order statuses (Ordered, On Delivery, Delivered, Cancelled).
- **Customer Messages**: Read and manage support messages submitted through the Contact Us form.
- **Admin Settings**: Securely change admin username and password from within the portal.

---

## 🛠️ Technologies Used

- **Frontend**: HTML5, Vanilla CSS3, Bootstrap 5, FontAwesome (Icons), Google Fonts (Inter & Outfit).
- **Backend**: PHP (Procedural with MySQLi Object-Oriented approach).
- **Database**: MySQL.
- **Architecture**: Session-based authentication, parameterized prepared SQL statements for top-tier security against SQL injection.

---

## 📁 Folder Structure

```text
Food_Ordering_System/
│
├── admin/                  # Admin portal files
│   ├── includes/           # Admin header, footer, etc.
│   ├── index.php           # Admin Dashboard
│   ├── foods.php           # Food management
│   ├── categories.php      # Category management
│   ├── orders.php          # Order management
│   ├── messages.php        # Contact messages management
│   ├── login.php           # Admin login
│   └── settings.php        # Update admin credentials
│
├── css/
│   └── style.css           # Global custom styles and animations
│
├── images/                 # All uploaded and static images
│
├── includes/
│   ├── config.php          # API keys and configuration
│   ├── db_connect.php      # Database connection & sanitization scripts
│   ├── header.php          # Frontend header
│   ├── navbar.php          # Frontend navigation
│   └── footer.php          # Frontend footer
│
├── sql/
│   └── database.sql        # Database schema and seed data
│
├── index.php               # Home page
├── menu.php                # Food menu page
├── cart.php                # Shopping cart page
├── checkout.php            # Checkout processing
├── login.php               # User login
├── register.php            # User registration
├── dashboard.php           # Customer dashboard
└── chatbot_api.php         # Backend API for Gemini integration
```

---

## 🚀 Installation & Setup

Follow these steps to run the project on your local machine:

1. **Prerequisites**: Ensure you have [XAMPP](https://www.apachefriends.org/) (or WAMP/MAMP) installed on your system.
2. **Clone/Move the Project**: Place the `Food_Ordering_System` folder inside your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\Food_Ordering_System`).
3. **Start XAMPP**: Open the XAMPP Control Panel and start **Apache** and **MySQL**.
4. **Database Setup**:
   - Open your browser and go to `http://localhost/phpmyadmin/`.
   - Create a new database named `food_order_db`.
   - Click on the **Import** tab, choose the `sql/database.sql` file from this project folder, and click **Go**.
5. **Run the Website**:
   - Customer Frontend: `http://localhost/Food_Ordering_System/`
   - Admin Portal: `http://localhost/Food_Ordering_System/admin/`

---

## 🔐 Default Credentials

To access the admin portal and manage the restaurant, use the following default credentials:

- **Admin Login URL**: `http://localhost/Food_Ordering_System/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

*(Note: It is highly recommended to change these credentials from the Admin Settings page after your first login).*

---

## 💡 Notes for Development
- Ensure `extension=mysqli` is enabled in your `php.ini` file.
- The project securely hashes all passwords using PHP's native `password_hash()` algorithm.
- File uploads are routed to the `images/` directory. Ensure this directory has appropriate read/write permissions if deploying to a live Linux server.
