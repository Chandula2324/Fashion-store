# Fashion Store - Online Fashion & Clothes Store

A complete web-based fashion e-commerce application built with **vanilla PHP, MySQL, HTML, CSS, and JavaScript** (no frameworks).

## Features

### Customer
- Browse clothing catalog with filters (Category: Men/Women/Kids, Type, Search)
- Product detail pages with size selection
- Session-based shopping cart
- Secure checkout with mock payment gateway
- Order confirmation

### Admin
- Secure login system
- Dashboard with sales statistics
- Inventory CRUD (Create, Read, Update, Delete products)
- Order management with status updates

## Technology Stack
- **Frontend:** HTML5, CSS3 (Custom Properties), Vanilla JavaScript
- **Backend:** PHP 7.4+ (Procedural)
- **Database:** MySQL / MariaDB

## Installation

1. Extract the ZIP file to your web server (XAMPP `htdocs/`, WAMP `www/`, or live server)
2. Import `database/fashion_store.sql` into phpMyAdmin
3. Update `includes/db.php` with your database credentials:
   ```php
   $username = 'root';      // Your MySQL username
   $password = '';          // Your MySQL password
   ```
4. Ensure the `uploads/` folder is writable (`chmod 755 uploads/`)
5. Access the site at `http://localhost/fashion-store/`

## Default Login
- **Username:** `admin`
- **Password:** `admin123`

## File Structure
```
fashion-store/
├── admin/
│   ├── index.php       # Dashboard
│   ├── products.php    # Inventory CRUD
│   ├── orders.php      # Order management
│   └── logout.php
├── assets/
│   ├── css/
│   │   ├── style.css   # Customer styles
│   │   └── admin.css   # Admin styles
│   └── js/
│       ├── main.js     # Customer scripts
│       └── admin.js    # Admin scripts
├── database/
│   └── fashion_store.sql
├── includes/
│   ├── db.php          # Database connection
│   └── functions.php   # Helper functions
├── uploads/            # Product images
├── index.php           # Customer homepage
├── product.php         # Product detail
├── cart.php            # Shopping cart
├── checkout.php        # Checkout & payment
└── login.php           # Admin login
```

## Database Schema

### Tables
- **admins** - Store manager accounts
- **products** - Clothing items with details (name, brand, category, type, sizes, color, price, stock, image)
- **orders** - Customer orders with shipping info and payment status
- **order_items** - Individual items within each order

## AI Usage Disclosure
This project was developed with assistance from AI tools for:
- Code generation and scaffolding
- CSS design system architecture
- Database schema design
- Bug fixing and optimization

All code was reviewed, understood, and customized by the development team to ensure full comprehension and maintainability.

## License
This project is for educational purposes.
