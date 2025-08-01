
# Basic PHP/MySQL RESTful Blog API

A minimal, framework-free REST API in **core PHP** for CRUD operations on blog posts stored in MySQL. It auto-creates the database and table (on first run), uses PDO with prepared statements for security, and returns JSON with proper HTTP status codes.

---

## Table of Contents

1. [Tech Stack & Prerequisites](#tech-stack--prerequisites)  
2. [Installation & Setup](#installation--setup)  
3. [Configuration](#configuration)  
4. [Running the API](#running-the-api)  
5. [Endpoints](#endpoints)  
6. [Request & Response Examples](#request--response-examples)  
7. [Error Handling & Status Codes](#error-handling--status-codes)  
8. [Testing with Postman](#testing-with-postman)  
9. [Security Considerations](#security-considerations)  

---

## Tech Stack & Prerequisites

- **PHP 7.4+** (with PDO extension)  
- **MySQL 5.7+**  
- A local web server (Apache/XAMPP/Laragon) or PHP’s built-in server  
- [Postman](https://www.postman.com/) (or curl) for testing

---

## Installation & Setup

1. **Clone the repo** (or copy files) into your server’s document root:

   ```bash
   git clone https://your-repo-url.git rest_api_t3
   cd rest_api_t3
    ````

2. **Ensure PHP can write** (no special permissions needed for this sample).

3. **Start your server**:

   * **Apache/XAMPP/Laragon**: Place the folder under `htdocs` or `www`, then browse.
   * **Built-in PHP server** (from the project root):

     ```bash
     php -S localhost:8000
     ```

---

## Configuration

*All settings live in* **config.php**:

```php
// config.php (excerpt)

// Database connection settings
define('DB_HOST', 'localhost');    // MySQL host
define('DB_NAME', 'blog_api');     // Database name
define('DB_USER', 'root');         // DB user
define('DB_PASS', '');             // DB password

// On first run, the script will:
// 1) Connect without a DB, 2) CREATE DATABASE IF NOT EXISTS,
// 3) USE the DB.
```

> **Auto-creation:**
>
> * On every request, **config.php** creates the database if missing,
> * **api.php** then creates the `posts` table if not already present.

---

## Running the API

No URL rewriting needed. All traffic goes through **api.php**:

```
http://localhost/.../rest_api_t3/api.php
```

Anything after `api.php/` is the **PATH\_INFO** that your router parses:

* `api.php/posts`
* `api.php/posts/3`
* etc.

> **Tip:** If you see `{ "error": "Not Found" }`, ensure you’re hitting `api.php/posts…` (not just `/posts`).

---

## Endpoints

| Method | URI                   | Description                 |
| ------ | --------------------- | --------------------------- |
| GET    | `/api.php/posts`      | Retrieve **all** blog posts |
| GET    | `/api.php/posts/{id}` | Retrieve a **single** post  |
| POST   | `/api.php/posts`      | **Create** a new post       |
| PUT    | `/api.php/posts/{id}` | **Update** an existing post |
| DELETE | `/api.php/posts/{id}` | **Delete** a post           |

---

## Request & Response Examples

### 1. GET all posts

```http
GET /api.php/posts HTTP/1.1
Host: localhost
```

**Response** (200 OK)

```json
[
  {
    "id": 5,
    "title": "Hello World",
    "content": "First post content…",
    "created_at": "2025-08-01 14:23:45"
  },
  {
    "id": 4,
    "title": "Another Post",
    "content": "More content…",
    "created_at": "2025-07-30 09:17:02"
  }
]
```

---

### 2. GET single post

```http
GET /api.php/posts/5 HTTP/1.1
```

**Response**

* **200 OK** (found)

  ```json
  {
    "id": 5,
    "title": "Hello World",
    "content": "First post content…",
    "created_at": "2025-08-01 14:23:45"
  }
  ```
* **404 Not Found** (missing)

  ```json
  { "error": "Post not found" }
  ```

---

### 3. POST create post

```http
POST /api.php/posts HTTP/1.1
Content-Type: application/json

{
  "title": "New Post",
  "content": "This is my new blog post."
}
```

**Response**

* **201 Created**

  ```json
  { "message": "Post created", "id": 6 }
  ```

---

### 4. PUT update post

```http
PUT /api.php/posts/6 HTTP/1.1
Content-Type: application/json

{
  "title": "Updated Title",
  "content": "Updated content text."
}
```

**Response**

* **200 OK**

  ```json
  { "message": "Post updated" }
  ```

---

### 5. DELETE a post

```http
DELETE /api.php/posts/6 HTTP/1.1
```

**Response**

* **200 OK**

  ```json
  { "message": "Post deleted" }
  ```
* **404 Not Found** (if ID doesn’t exist)

  ```json
  { "error": "Post not found" }
  ```

---

## Error Handling & Status Codes

* **200 OK** – Successful GET, PUT, DELETE
* **201 Created** – Successful resource creation (POST)
* **400 Bad Request** – Missing or invalid input
* **404 Not Found** – Invalid endpoint or missing record
* **405 Method Not Allowed** – Unsupported HTTP method
* **500 Internal Server Error** – Database connection or query failure

All error responses return a JSON object:

```json
{ "error": "Descriptive message here" }
```

---

## Testing with Postman

1. In **Postman** set method → **POST**, **GET**, etc.
2. URL → `http://localhost/.../api.php/posts[/{id}]`
3. If sending JSON, go to **Body → raw → JSON** and enter:

   ```json
   { "title": "...", "content": "..." }
   ```
4. Hit **Send**, inspect the JSON response and status code.

---

## Security Considerations

* **Prepared Statements** (PDO) to prevent SQL injection.
* **`filter_var(..., FILTER_SANITIZE_STRING)`** to strip unwanted tags.
* Proper **HTTP status codes** and JSON responses.
* Further enhancements: authentication (API keys/JWT), HTTPS, rate-limiting.

---
