# 🌿 EcoEvents

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Blade](https://img.shields.io/badge/Blade-Template-orange?style=flat-square)
![MySQL](https://img.shields.io/badge/MySQL-Database-00758F?style=flat-square&logo=mysql&logoColor=white)
![JWT](https://img.shields.io/badge/Auth-JWT-green?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-blue?style=flat-square)

---

## 🌎 Overview

**EcoEvents** is a **Laravel-based web application** that connects users with **environmental and sustainability-focused events**.  
It allows users to **discover, create, and participate** in eco-friendly initiatives, promoting awareness and collaboration in local communities.

🧩 **Built with:** Laravel • Blade • Vite • MySQL  
🔐 **Authentication:** JSON Web Tokens (JWT)  
🎯 **Goal:** Encourage real-world ecological actions and sustainable community engagement.

---

## ✨ Features

- 🔐 Secure **JWT Authentication** (login & register)
- 🌍 Browse eco-friendly events near you
- 🗓️ Create, edit, and manage events
- 👥 Join and participate in local initiatives
- 📸 Add event images and detailed info
- 🔎 Search & filter by category or location
- 📱 Fully responsive and mobile-friendly design

---

## 🧰 Tech Stack

| Category | Technology |
|-----------|-------------|
| **Backend** | Laravel 11 (PHP 8.2+) |
| **Frontend** | Blade + Vite |
| **Database** | MySQL |
| **Authentication** | JSON Web Token (JWT) |
| **Styling** | Bootstrap / TailwindCSS |
| **Package Managers** | Composer & npm |

---

## ⚙️ Installation

### 1️⃣ Clone the repository
```bash
git clone https://github.com/<your-team>/ecoevents.git
cd ecoevents
```

### 2️⃣ Install dependencies
```bash
composer install
npm install
```

### 3️⃣ Create environment file
```bash
cp .env.example .env
```

### 4️⃣ Configure the database
Update your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecoevents
DB_USERNAME=root
DB_PASSWORD=
```

### 5️⃣ Run migrations and seeders
```bash
php artisan migrate --seed
```

### 6️⃣ Generate the application key
```bash
php artisan key:generate
```

### 7️⃣ Start the development servers
```bash
npm run dev
php artisan serve
```

🌐 **Visit your app:** [http://localhost:8000](http://localhost:8000)

---

## 🔑 JWT Authentication Setup

EcoEvents uses **JWT (JSON Web Tokens)** for secure authentication.

To set it up:

```bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

This command generates a secret key used for signing tokens — make sure it's included in your `.env`.

---

## 👥 Team

| Name | Role |
|------|------|
| **Taha Yessin Hadded** | Full Stack Developer |
| *Member 2* | Frontend Developer |
| *Member 3* | Backend Developer |
| *Member 4* | QA / Documentation |

---

## 🪪 License

This project is licensed under the **MIT License**.  
See the [LICENSE](LICENSE) file for more details.

---

> 💬 *“Small actions, when multiplied by millions of people, can transform the world.”* — **Howard Zinn**

---

🌱 Developed with passion by the **EcoEvents Team**
