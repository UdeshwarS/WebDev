# 🚗 DriveSmart Driving School Web App

## 📌 Project Overview
DriveSmart is a web-based driving school management system. It allows students to book driving lessons and view availability, while the instructor can manage schedules and bookings.

This project is built using:
- HTML
- CSS
- JavaScript
- PHP
- MySQL

---

# 📁 Project Structure & File Guide

## 🏠 `index.html`
- This is the **landing page (homepage)** of the website.
- First page users see when they open the site.
- Contains:
  - About section
  - Pricing
  - Contact information
  - Login / Signup buttons

---

## 🔐 `auth/` (Authentication System)

### 📄 `auth/login.php`
- Login page for students and instructor
- Verifies user credentials from the database
- Starts user session after successful login

### 📄 `auth/signup.php`
- Registration page for new users
- Creates student or instructor accounts
- Stores user data in the database

### 📄 `auth/logout.php`
- Ends the user session
- Redirects user back to landing page

---

## ⚙️ `config/`

### 📄 `config/db.php`
- Database connection file
- Connects the project to MySQL database
- Used by all backend PHP files

---

## 🎨 `assets/`

### 📁 `assets/css/style.css`
- Contains all styling for the website
- Controls layout, colors, fonts, and responsiveness

### 📁 `assets/js/script.js`
- Handles front-end interactions
- Form validation and UI behavior

### 📁 `assets/images/`
- Stores images like logos, banners, and icons

---

## 🧩 `includes/` (Reusable Components)

### 📄 `includes/header.php`
- Contains HTML `<head>` section
- Includes CSS links and page setup

### 📄 `includes/navbar.php`
- Navigation bar used across all pages

### 📄 `includes/footer.php`
- Footer section

---

## 👨‍🎓 `student/` (Student Features)

### 📄 `student/dashboard.php`
- Shows student dashboard
- Displays upcoming bookings and driving hours

### 📄 `student/booking.php`
- Allows students to book driving lessons
- Sends booking data to database

### 📄 `student/booking.html`
- Front-end Booking Form

---

## 🧑‍🏫 `instructor/` (Instructor Features)

### 📄 `instructor/dashboard.php`
- Instructor overview page
- Shows upcoming lessons and cancellation requests

### 📄 `instructor/availability.php`
- Allows instructor to add or remove available time slots

---

## 📅 `calendar/`

### 📄 `calendar/view.php`
- Displays instructor availability in calendar format
- Students can view and select available time slots

---

## 🗄️ `database/`

### 📄 `database/schema.sql`
- Contains all database tables:
  - users
  - bookings
  - availability

---

# 🔄 Application Flow

1. User opens `index.php`
2. User logs in or signs up
3. Student accesses dashboard or booking page
4. Instructor manages availability and bookings
5. System stores everything in MySQL database

---

# 👥 Team Responsibilities

- Akil → Authentication system (login/signup/logout)
- Ayesha → Booking system
- Anas → Calendar & availability system
- Udeshwar → Dashboards

---

# 🎯 Project Goal
To build a simple but functional driving school management system that reduces manual scheduling and improves booking efficiency for both students and instructors.

---

# 🚀 Notes
- Use `includes/` files for consistent layout