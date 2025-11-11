markdown
# Franklin Monorepo - API Documentation

## Base URL
http://localhost:8000/api

text

## Authentication
All protected endpoints require JWT token in Authorization header:
Authorization: Bearer {token}

text

## Endpoints

### Authentication

#### POST /api/login
Login user and get JWT token.

**Request:**
```json
{
    "email": "admin@example.com",
    "password": "password123"
}
Response:

json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "roles": [],
            "permissions": []
        },
        "token": "jwt_token_here"
    },
    "message": "Login berhasil"
}
POST /api/logout
Logout user and invalidate token.

Headers:

text
Authorization: Bearer {token}
Response:

json
{
    "success": true,
    "message": "Logged out successfully"
}
GET /api/user
Get authenticated user profile.

Headers:

text
Authorization: Bearer {token}
Response:

json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "roles": [],
            "permissions": []
        }
    }
}
File Upload
POST /api/upload
Upload files (requires authentication).

Headers:

text
Authorization: Bearer {token}
Content-Type: multipart/form-data
Body:

file (file): File to upload

Response:

json
{
    "success": true,
    "data": {
        "filename": "uploaded_file.jpg",
        "path": "storage/uploads/filename.jpg",
        "url": "http://localhost:8000/storage/uploads/filename.jpg"
    },
    "message": "File uploaded successfully"
}
Error Responses
401 Unauthorized
json
{
    "success": false,
    "message": "Unauthorized"
}
422 Validation Error
json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."]
    }
}
500 Server Error
json
{
    "success": false,
    "message": "Server error"
}
Default Test Account
Email: admin@example.com

Password: password123

text

**Buat file: `docs/SETUP_GUIDE.md`**

```markdown
# Setup Guide

## Prerequisites
- PHP 8.1+
- Composer
- Node.js 16+
- SQLite/MySQL

## Installation Steps

1. **Clone Repository**
```bash
git clone https://github.com/username/franklin-monorepo.git
cd franklin-monorepo
Install Dependencies

bash
composer install
npm install
Environment Setup

bash
cp .env.example .env
php artisan key:generate
Database Setup

bash
# For SQLite (default)
touch database/database.sqlite

# Or for MySQL, update .env:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=franklin_db
DB_USERNAME=root
DB_PASSWORD=
Run Migrations & Seeders

bash
php artisan migrate
php artisan db:seed
Start Development Server

bash
php artisan serve
Testing API with Postman
Import postman-collection.json to Postman

Set environment variables:

base_url: http://localhost:8000

Use login endpoint to get token

Token will be automatically set for authenticated requests

Default Admin User
After running seeders, you can login with:

Email: admin@example.com

Password: password123

text

## 3. Tambahkan ke Git dan Push

```bash
# Tambahkan file baru ke git
git add postman-collection.json
git add docs/

# Commit perubahan
git commit -m "Add Postman collection and API documentation"

# Push ke GitHub
git push origin main