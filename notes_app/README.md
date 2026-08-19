# 🚀 Laravel Projects Monorepo / Collection

Koleksi dan showcase backend API / aplikasi web yang dibangun menggunakan ekosistem **Laravel**, **PostgreSQL**, dan **RESTful API Architecture**.

---

## 📌 Daftar Project

| # | Project Name | Tech Stack | Status | Deskripsi Singkat |
|---|---|---|---|---|
| 1 | **Notes API** | Laravel 11, PostgreSQL, JWT Auth, Raw SQL | 🟢 Completed | REST API manajemen catatan pribadi (BREAD), proteksi JWT, UUID primary keys, dan PostgreSQL atomic queries. |
| 2 | *Next Project...* | - | 🟡 Planned | - |

---

## 📝 Project Showcase #1: Notes REST API

RESTful API backend untuk manajemen catatan pribadi yang aman dan terisolasi per user. Dibangun dengan pendekatan **Raw SQL Optimization** di PostgreSQL untuk performa kueri atomik dan bebas *race conditions*.

### 🛠️ Tech Stack & Key Features
- **Framework:** Laravel 13 (PHP 8.4+)
- **Database:** PostgreSQL (UUID v4 Primary Keys)
- **Authentication:** Stateless JWT (`php-open-source-saver/jwt-auth`)
- **Query Strategy:** Parameterized Raw SQL (`DB::selectOne`, `DB::delete`, `RETURNING` clauses)
- **Features:**
  - 🔐 Auth: Register, Login, Me, Refresh Token, Logout.
  - 📄 BREAD Notes: Browse (Search & Pin Sort), Read, Edit (Atomic Update), Add, Delete.
  - 🏷️ Tagging System: Many-to-Many relationship (PostgreSQL `JSON_AGG`).
  - ⚡ Full-Text Search: Case-insensitive search menggunakan PostgreSQL `ILIKE`.

---

### 🚀 Cara Menjalankan Project (Local Setup)

#### 1. Clone & Install Dependencies
```bash
git clone [https://github.com/username-anda/nama-repo.git](https://github.com/username-anda/nama-repo.git)
cd nama-repo
composer install
