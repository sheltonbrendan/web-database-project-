# Online Buying and Selling Platform

A simple web-based buying and selling platform that allows users to post products for sale, browse available items, and manage product listings through a database-connected system.

## Project Description
This project is designed to demonstrate the implementation of a web application connected to a database using a connection string. The platform supports basic CRUD operations (Create, Read, Update, Delete) for managing products and users.

## Features
- User registration and login
- Add products for sale
- View available products
- Update product details
- Delete product listings
- Database storage and retrieval
- Responsive web interface

## Technologies Used
- Frontend: HTML, CSS, Bootstrap
- Backend: PHP
- Database: MySQL
- Server Environment: XAMPP

## Database Connection
The system connects to a MySQL database using a connection string.

Example:
```php
$conn = new mysqli("localhost", "root", "", "online_market");
```

## Installation and Setup

### Requirements
- XAMPP or any local server environment
- PHP
- MySQL
- Web browser

### Steps
1. Clone or download the project files
2. Move the project folder into the `htdocs` directory in XAMPP
3. Start Apache and MySQL from the XAMPP Control Panel
4. Open phpMyAdmin
5. Create a database named:

```sql
online_market
```

6. Import the provided SQL file into the database
7. Open the browser and run:

```text
http://localhost/project-folder-name
```

## Project Structure

```text
/project-folder
│
├── css/
├── images/
├── includes/
├── database/
├── index.php
├── login.php
├── register.php
├── products.php
├── add_product.php
├── database.sql
└── README.md
```

## Functionalities
- Buyers can browse products
- Sellers can upload products
- Products are stored in the database
- Users can manage listings through CRUD operations

## Future Improvements
- Online payment integration
- Product search and filtering
- User messaging system
- Admin dashboard
- Product categories

## Author
Brendan Shelton

## License
This project is for educational purposes only.
