# 📝 Notes App REST API

Backend RESTful API untuk aplikasi manajemen catatan pribadi yang aman, dan terisolasi per user. Dibangun menggunakan **Laravel**, **PostgreSQL**, **JWT Authentication**, dan pendekatan kueri **Raw SQL**.

---

## 🛠️ Tech Stack & Architecture

* **Framework:** Laravel 13 (PHP 8.4+)
* **Database:** PostgreSQL (UUID v4 Primary Keys)
* **Authentication:** Stateless JWT Auth (`php-open-source-saver/jwt-auth`)
* **Query Engine:** Parameterized Raw SQL (`DB::select`, `DB::selectOne`, `DB::delete` dengan PostgreSQL `RETURNING` clauses)
* **API Style:** RESTful JSON Envelope Standard

---

## ✨ Fitur Utama

* 🔐 **Autentikasi Stateless JWT**: Register, Login, User Profile, dan Token Invalidation (Logout).
* 📄 **BREAD Operations**:
  * **Browse**: Mengambil daftar semua catatan terurut berdasarkan status pin dan waktu pembuatan terbaru.
  * **Read**: Mengambil detail catatan spesifik beserta relasi tags.
  * **Edit / Update**: Pembaruan catatan secara atomik langsung dengan klausul PostgreSQL `RETURNING`.
  * **Add / Store**: Pembuatan catatan baru dengan verifikasi otorisasi pemilik.
  * **Delete**: Penghapusan catatan aman berbasis *affected rows*.
* 🔍 **Full-Text Case-Insensitive Search**: Pencarian catatan berdasarkan judul atau konten menggunakan operator PostgreSQL `ILIKE`.
* 🏷️ **Many-to-Many Tagging System**: Manajemen tag catatan menggunakan tabel pivot `note_tags` yang di-agregasi menggunakan fungsi PostgreSQL `JSON_AGG`.
* 🛡️ **Data Security**: Proteksi penuh terhadap SQL Injection via *parameterized queries* dan isolasi ketat kepemilikan catatan berdasarkan `user_id`.

---
