# Online Service Management System (OSMS)

## 📋 Project Summary

The **Online Service Management System (OSMS)** is a comprehensive web-based platform designed to streamline the management of technical service requests. The system facilitates efficient communication between customers, administrators, and technicians through an intuitive workflow that includes request submission, work assignment, status tracking, and completion verification.

### Key Features:
- **Customer Portal**: Submit service requests with detailed information
- **Admin Dashboard**: Manage requests, assign work to technicians, and track progress
- **Technician Interface**: View assigned work, accept/reject requests, and mark completion
- **Real-time Status Tracking**: Monitor request status from submission to completion
- **Work Order Management**: Comprehensive work order system with detailed tracking
- **Multi-user Authentication**: Separate login systems for admin, technicians, and requesters

---

## 🛠️ Technologies Used

### Frontend
- **HTML5** - Structure and semantic markup
- **CSS3** - Styling and responsive design
- **Bootstrap 4** - UI components and grid system
- **JavaScript** - Client-side interactivity

### Backend
- **PHP 7+** - Server-side scripting and business logic
- **Session Management** - User authentication and authorization

### Database
- **MySQL** - Relational database management
- **phpMyAdmin** - Database administration interface

---

## 🌐 Live Hosting

**Domain**: [osms-php.xo.je](http://osms-php.xo.je)  
**Hosting Provider**: InfinityFree (Free Web Hosting)

---

## 📥 Installation Guide

If you download and want to run this project locally, follow these steps:

### Prerequisites
1. **XAMPP** (or any PHP development environment)
   - Download from: [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Includes Apache, MySQL, and PHP

### Installation Steps

1. **Clone or Download the Project**
   ```
   git clone <repository-url>
   ```
   Or download as ZIP and extract it.

2. **Move Project to Web Directory**
   - Copy the project folder to `C:\xampp\htdocs\` (Windows)
   - Or `/opt/lampp/htdocs/` (Linux)
   - Rename folder to `OSMS_PHP`

3. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL** services

4. **Create Database**
   - Open browser and go to `http://localhost/phpmyadmin`
   - Create a new database named `osms_db`
   - Import the database:
     - Click on `osms_db` database
     - Go to **Import** tab
     - Choose the SQL file from project: `database/osms_db.sql`
     - Click **Go** to import

5. **Configure Database Connection**
   - Open `dbConnection.php` in the root directory
   - Update database credentials if needed:
     ```php
     $conn = new mysqli("localhost", "root", "", "osms_db");
     ```

6. **Access the Application**
   - Open browser and navigate to:
     - Homepage: `http://localhost/OSMS_PHP/`
     - Admin Login: `http://localhost/OSMS_PHP/Admin/login.php`
     - Technician Login: `http://localhost/OSMS_PHP/Technician/TechnicianLogin.php`

---


## 🎓 Academic Information

**Course**: Database Management System (DBMS)  
**Institution**: North East University Bangladesh  
**Developer**: Abu Sufian Emon  
**Academic Year**: 2025

## 🙏 Acknowledgments

**Supervisor**: Razorshi Prozzwal Talukder.

Thank you for your guidance and supervision throughout this project.

---

## 🚀 Features Breakdown

### For Customers (Requesters)
- Submit service requests with detailed descriptions
- Track request status in real-time
- View assignment details and technician information
- Receive updates on work progress

### For Administrators
- View and manage all service requests
- Assign work to available technicians
- Track technician performance and workload
- Manage technician accounts
- Generate work orders
- View comprehensive reports

### For Technicians
- View assigned work orders
- Accept or reject assignments
- Update work status (pending → accepted → completed)
- Access customer contact information
- Track personal work history and statistics

---

## 🔒 Security Features

- Session-based authentication
- Role-based access control (Admin, Technician, Requester)
- SQL injection prevention using prepared statements
- Password protection for all user types
- Secure logout functionality

---

## 📄 License

This project is developed for academic purposes as part of the DBMS course curriculum.

---

