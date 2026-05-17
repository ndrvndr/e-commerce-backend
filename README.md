<h1 align="center">🛍️ E-Commerce Backend</h1>

<p align="center">
  A production-ready REST API and admin panel for a modern e-commerce platform,<br/>
  built with <strong>Laravel 13</strong>, <strong>Filament 5</strong>, and <strong>Cloudflare R2</strong>.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"/>
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
  <img src="https://img.shields.io/badge/Filament-5-F59E0B?style=for-the-badge" alt="Filament"/>
  <img src="https://img.shields.io/badge/Sanctum-API_Auth-38BDF8?style=for-the-badge" alt="Sanctum"/>
  <img src="https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker"/>
</p>

---

## 📋 Table of Contents

- [Live Demo](#-live-demo)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Getting Started](#-getting-started)
  - [Prerequisites](#prerequisites)
  - [Local Setup](#local-setup)
  - [Docker Setup](#docker-setup)
- [Environment Variables](#-environment-variables)
- [API Reference](#-api-reference)
  - [Catalogs](#catalogs)
  - [Products](#products)
  - [Gallery](#gallery)
  - [Authentication](#authentication)
  - [Addresses](#addresses)
- [Data Models](#-data-models)
- [Admin Panel](#-admin-panel)
- [File Storage](#-file-storage)
- [Roles & Permissions](#-roles--permissions)
- [Auth Mechanism](#-auth-mechanism)
- [Deployment Scenarios](#-deployment-scenarios)

---

## 🌐 Live Demo

| | |
|---|---|
| **Admin Panel** | [https://e-commerce-backend-6m2p.onrender.com/admin](https://e-commerce-backend-6m2p.onrender.com/admin) |
| **Email** | `guest@guest.com` |
| **Password** | `guest123` |

> ⚠️ Hosted on Render's free tier — the server may take **~30 seconds** to wake up on the first visit.

---

## ✨ Features

- 🔐 **Token-based API auth** via Laravel Sanctum
- 🔑 **Google OAuth** via Laravel Socialite — stateless, token-redirect flow
- 🗂️ **Catalog & Product management** with slugs, rich filtering, sorting, and pagination
- 🎨 **Per-color image variants** — each color option carries its own image gallery
- 📦 **Stock tracking** via product variations (color × size)
- 🖼️ **Gallery management** — sortable homepage gallery (max 30 images)
- 📍 **User address book** — up to 4 addresses per user with a default flag
- 🛡️ **Role-based access control** (Spatie Permissions + Filament Shield)
- 🗄️ **Cloudflare R2 / AWS S3** file storage with automatic cleanup on delete/update
- 🎛️ **Filament 5 admin panel** — manage products, catalogs, gallery, and users
- 🐳 **Docker-ready** — multi-stage build with Nginx + PHP-FPM

---

## 🛠 Tech Stack

| Category | Technology |
|---|---|
| Framework | Laravel 13 |
| Language | PHP 8.3+ |
| Admin Panel | Filament 5 |
| API Auth | Laravel Sanctum |
| Social Auth | Laravel Socialite (Google) |
| Roles & Permissions | Spatie Laravel Permission + Filament Shield |
| File Storage | Cloudflare R2 / AWS S3 (`league/flysystem-aws-s3-v3`) |
| Database | PostgreSQL (production) · SQLite (local dev) |
| Frontend Assets | Vite + Node 20 |
| Containerisation | Docker (multi-stage) + Nginx |

---

## 📁 Project Structure

```
app/
├── Filament/
│   └── Resources/          # Admin panel resources (Catalogs, Products, Gallery, Users)
├── Http/
│   ├── Controllers/
│   │   ├── Api/            # CatalogController, ProductController, GalleryController, AddressController
│   │   └── Auth/           # AuthController, SocialiteController
│   └── Resources/          # API JSON resources (Catalog, Product, ProductVariation)
├── Models/                 # Address, Catalog, GalleryImage, Product, ProductVariation, User
├── Policies/
└── helpers.php             # r2_url() helper

database/
├── factories/
├── migrations/
└── seeders/

routes/
├── api.php                 # All REST API routes
└── web.php                 # Admin redirect + OAuth routes
```

---

## 🚀 Getting Started

### Prerequisites

- PHP **8.3+** with extensions: `pdo`, `pdo_pgsql`, `gd`, `zip`, `intl`, `opcache`
- **Composer** 2+
- **Node.js** 20+ and **npm**
- A **PostgreSQL** or **SQLite** database
- An **AWS S3** or **Cloudflare R2** bucket

### Local Setup

**1. Clone the repository**

```bash
git clone <your-repo-url>
cd ecommerce-backend
```

**2. Run the one-command setup**

```bash
composer run setup
```

This single command will:
- Install all PHP dependencies (`composer install`)
- Copy `.env.example` → `.env` and generate an app key
- Run all database migrations
- Install frontend dependencies and build assets

**3. Configure your environment**

Edit the `.env` file with your database credentials, S3/R2 keys, Google OAuth secrets, and frontend URL. See [Environment Variables](#-environment-variables) for a full reference.

**4. Seed roles & permissions**

```bash
php artisan db:seed
php artisan shield:generate --all
```

**5. Start the development server**

```bash
composer run dev
```

This starts Laravel, the queue worker, Pail log viewer, and Vite all at once.

---

### Docker Setup

**Build and run with Docker Compose:**

```bash
docker-compose up --build
```

The app will be available at `http://localhost:8080`.

The `deploy.sh` entrypoint script automatically:
- Sets correct storage permissions
- Clears and re-caches config, routes, and views
- Runs `php artisan migrate --force`
- Links storage and publishes Filament assets
- Starts PHP-FPM and Nginx

---

## 🔧 Environment Variables

Variables are grouped by concern. Copy `.env.example` to `.env` and fill in the required values.

---

### Application

| Variable | Default | Description |
|---|---|---|
| `APP_NAME` | `Laravel` | Application name |
| `APP_ENV` | `local` | Environment: `local`, `staging`, `production` |
| `APP_KEY` | *(empty)* | Encryption key — generate with `php artisan key:generate` |
| `APP_DEBUG` | `true` | Show detailed errors. **Always `false` in production** |
| `APP_URL` | `http://localhost:8000` | Full public URL of this backend |

---

### Database

| Variable | Description |
|---|---|
| `DB_CONNECTION` | Driver — use `pgsql` for PostgreSQL |
| `DB_HOST` | Database host |
| `DB_PORT` | Database port (`5432` for PostgreSQL) |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database username |
| `DB_PASSWORD` | Database password |

> This project is configured for **PostgreSQL**. Managed services like [Neon](https://neon.tech) or [Supabase](https://supabase.com) work out of the box.

---

### Session

| Variable | Default | Description |
|---|---|---|
| `SESSION_DRIVER` | `cookie` | Stores session in an encrypted cookie (no DB table needed) |
| `SESSION_LIFETIME` | `120` | Session idle timeout in minutes |
| `SESSION_SECURE_COOKIE` | `false` | Set to `true` in production (requires HTTPS) |
| `SESSION_HTTP_ONLY` | `true` | Prevents JavaScript from accessing the session cookie |
| `SESSION_SAME_SITE` | `Lax` | `Lax` for local/same-site · `None` for cross-domain (requires `SESSION_SECURE_COOKIE=true`) |

---

### File Storage — Cloudflare R2

All media uploads use **Cloudflare R2** (S3-compatible). Get your credentials from the [Cloudflare Dashboard](https://dash.cloudflare.com) → R2 → Manage API Tokens.

| Variable | Description |
|---|---|
| `AWS_ACCESS_KEY_ID` | R2 Access Key ID |
| `AWS_SECRET_ACCESS_KEY` | R2 Secret Access Key |
| `AWS_DEFAULT_REGION` | Use `auto` for Cloudflare R2 |
| `AWS_BUCKET` | Your R2 bucket name |
| `AWS_USE_PATH_STYLE_ENDPOINT` | Must be `true` for R2 |
| `AWS_ENDPOINT` | R2 S3 API endpoint: `https://<ACCOUNT_ID>.r2.cloudflarestorage.com` |
| `AWS_URL` | Public URL for serving files (R2 public bucket URL or custom domain) |

---

### Google OAuth

Create OAuth credentials at [Google Cloud Console](https://console.cloud.google.com) → APIs & Services → Credentials → OAuth 2.0 Client IDs.

| Variable | Description |
|---|---|
| `GOOGLE_CLIENT_ID` | OAuth 2.0 Client ID |
| `GOOGLE_CLIENT_SECRET` | OAuth 2.0 Client Secret |
| `GOOGLE_REDIRECT_URI` | Must exactly match what is registered in Google Cloud Console |

---

### Frontend & Cookie Configuration

| Variable | Default | Description |
|---|---|---|
| `FRONTEND_URL` | `http://localhost:3000` | Primary frontend origin (used for CORS and OAuth redirects) |
| `FRONTEND_URLS` | *(unset)* | Comma-separated list for **multiple** frontend origins — overrides `FRONTEND_URL` |
| `COOKIE_DOMAIN` | *(empty)* | Cookie domain scope. Leave empty for single domain. Set to `.yourdomain.com` to share across subdomains |
| `COOKIE_SECURE` | `false` | Set to `true` in production (HTTPS). Required when `COOKIE_SAME_SITE=None` |
| `COOKIE_SAME_SITE` | `Lax` | `Strict` · `Lax` · `None` (cross-domain requires `COOKIE_SECURE=true`) |

---

### Sanctum

| Variable | Default | Description |
|---|---|---|
| `SANCTUM_TOKEN_EXPIRATION` | `10080` | Token lifetime in minutes (`10080` = 7 days) |
| `SANCTUM_STATEFUL_DOMAINS` | `localhost:3000` | Comma-separated list of domains that receive Sanctum's stateful cookies. Add your frontend domain(s) in production |

---

### Admin Panel

| Variable | Description |
|---|---|
| `ADMIN_EMAIL` | Email for the first seeded admin user |
| `ADMIN_PASSWORD` | Password for the first seeded admin user |

> These values are only used during `php artisan db:seed`. Change the password via the admin panel after first login.

---

## 📡 API Reference

All API routes are prefixed with `/api`. Authenticated routes require a `Bearer` token in the `Authorization` header.

---

### Catalogs

#### `GET /api/catalogs`

Returns all catalogs.

**Response**
```json
{
  "data": [
    {
      "id": 1,
      "name": "T-Shirts",
      "slug": "t-shirts",
      "description": "All tees",
      "image": "https://cdn.example.com/catalogs/tshirts.jpg"
    }
  ]
}
```

---

#### `GET /api/catalogs/{slug}`

Returns a single catalog with its active products and their variations.

---

### Products

#### `GET /api/products`

Returns a paginated list of active products. Supports rich filtering and sorting.

**Query Parameters**

| Parameter | Type | Description |
|---|---|---|
| `search` | `string` | Case-insensitive name search |
| `catalog_slug` | `string` | Filter by catalog slug (`all-products` returns everything) |
| `availability` | `string\|array` | `in_stock`, `out_of_stock`, or both |
| `price_min` | `number` | Minimum effective price (uses `discount_price` if set) |
| `price_max` | `number` | Maximum effective price |
| `sort` | `string` | `newest` (default), `oldest`, `price_asc`, `price_desc`, `a_z`, `z_a` |
| `per_page` | `integer` | Results per page (default: `12`) |

**Response**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Classic White Tee",
      "slug": "classic-white-tee",
      "price": 29.99,
      "discount_price": 19.99,
      "image_primary": "https://cdn.example.com/products/tee-front.jpg",
      "image_hover": "https://cdn.example.com/products/tee-back.jpg",
      "size_chart_image": "https://cdn.example.com/products/tee-sizes.jpg",
      "colors": [
        {
          "color": "White",
          "images": ["https://cdn.example.com/products/tee-white-1.jpg"]
        }
      ],
      "material": "100% Cotton",
      "weight": 0.25,
      "total_stock": 42,
      "variations": [
        { "id": 1, "color": "White", "size": "M", "stock": 10 }
      ],
      "catalog": { "id": 1, "name": "T-Shirts", "slug": "t-shirts" }
    }
  ],
  "links": { ... },
  "meta": { "current_page": 1, "last_page": 3, ... }
}
```

---

#### `GET /api/products/{slug}`

Returns a single active product with full detail.

---

### Gallery

#### `GET /api/gallery`

Returns an ordered list of gallery image URLs (up to 30).

**Response**
```json
{
  "data": [
    "https://cdn.example.com/gallery/photo1.jpg",
    "https://cdn.example.com/gallery/photo2.jpg"
  ]
}
```

---

### Authentication

#### `GET /auth/google/redirect`

Redirects the user to Google's OAuth consent screen.

#### `GET /auth/google/callback`

Handles the OAuth callback. Creates or retrieves the user, assigns the `guest` role on first login, then generates a **one-time code** (valid for 60 seconds) and redirects to:

```
{FRONTEND_URL}/authentication/callback?code=<one_time_code>
```

> The Sanctum token is **never** exposed in the redirect URL. The frontend must exchange this code via `POST /api/auth/verify-code`.

---

#### `POST /api/auth/verify-code`

Exchanges a one-time OAuth code for an authenticated session. On success, a Sanctum token is issued as an **HTTP-only, Secure cookie** (`auth_token`) — the token is never exposed in the response body.

**Request Body**
```json
{ "code": "<one_time_code_from_callback_url>" }
```

**Response** `200 OK`
```json
{ "message": "OK" }
```

```
Set-Cookie: auth_token=<token>; HttpOnly; Secure; SameSite=None; Path=/
```

> The browser automatically sends this cookie on every subsequent request. The `CookieToBearer` middleware converts it to a `Bearer` token transparently — no manual token handling needed on the frontend.

**Error** `401 Unauthorized`
```json
{ "message": "Invalid or expired code" }
```

---

#### `GET /api/me` 🔒

Returns the currently authenticated user.

**Response**
```json
{
  "data": {
    "id": 1,
    "name": "Jane Doe",
    "email": "jane@example.com"
  }
}
```

---

#### `POST /api/logout` 🔒

Revokes the current access token.

**Response**
```json
{ "message": "Logout success" }
```

---

### Addresses

All address routes require authentication (`Bearer` token). A user may have up to **4 addresses**, with one marked as default.

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/addresses` | List all addresses (default first) |
| `POST` | `/api/addresses` | Create a new address |
| `GET` | `/api/addresses/{id}` | Get a single address |
| `PUT/PATCH` | `/api/addresses/{id}` | Update an address |
| `DELETE` | `/api/addresses/{id}` | Delete an address |

**Address Fields**

| Field | Type | Rules |
|---|---|---|
| `country` | `string` | required |
| `first_name` | `string` | required |
| `last_name` | `string` | required |
| `address` | `string` | required |
| `note` | `string` | optional |
| `city` | `string` | required |
| `province` | `string` | required |
| `postal_code` | `integer` | required, 3–10 digits |
| `phone_number` | `string` | required |
| `is_default` | `boolean` | optional |

> **Rules:** Setting `is_default: true` automatically unsets the previous default. You cannot delete the default address while other addresses exist. You cannot unset the default flag directly — set another address as default first.

---

## 🗄️ Data Models

```
User
 ├── hasMany → Address
 └── hasOne  → Address (default)

Catalog
 └── hasMany → Product

Product
 ├── belongsTo → Catalog
 └── hasMany   → ProductVariation

GalleryImage  (standalone, sorted)
```

### Product Fields

| Field | Type | Description |
|---|---|---|
| `name` | string | Display name |
| `slug` | string | URL-friendly identifier |
| `description` | text | Full product description |
| `price` | decimal | Base price |
| `discount_price` | decimal\|null | Sale price (overrides `price` in filters) |
| `image_primary` | string | Main product image path (R2) |
| `image_hover` | string | Hover / alternate image path (R2) |
| `size_chart_image` | string | Size chart image path (R2) |
| `color_images` | JSON | Array of `{ color, images[] }` objects |
| `material` | string | Fabric / material description |
| `weight` | decimal | Weight in kg |
| `brand` | string | Brand name |
| `is_active` | boolean | Controls visibility in the API |

---

## 🎛️ Admin Panel

The Filament admin panel is available at `/admin`.

| Resource | Description |
|---|---|
| **Catalogs** | Create and manage product categories with images |
| **Products** | Full product editor — pricing, images, color variants, size charts, stock |
| **Gallery** | Upload and sort homepage gallery images (max 30) |
| **Users** | View users, assign roles |

Access requires one of the roles: `super_admin`, `staff`, or `guest`.

To create the first admin user:

```bash
php artisan make:filament-user
php artisan shield:super-admin --user=<email>
```

---

## 🗂️ File Storage

All media is stored in **Cloudflare R2** (S3-compatible). The `r2_url()` helper converts a stored path to its public CDN URL using `AWS_URL` from your `.env`.

**Automatic cleanup:** When a product image is replaced or the product is deleted, the old files are automatically removed from R2 — including all color variant images.

---

## 🛡️ Roles & Permissions

Roles are managed via **Spatie Laravel Permission** and surfaced in the admin panel through **Filament Shield**.

| Role | Description |
|---|---|
| `super_admin` | Full access to all admin panel features |
| `staff` | Access to manage products, catalogs, and gallery |
| `guest` | Limited read-only panel access; default role for new OAuth users |

Generate permission policies for all resources:

```bash
php artisan shield:generate --all
```

Assign the super admin role to a user:

```bash
php artisan shield:super-admin --user=<email>
```

---

## 🔐 Auth Mechanism

This project uses a **custom HTTP-only cookie + Sanctum token** approach, combining the security of HTTP-only cookies with the simplicity of token-based auth.

### Flow

```
┌─────────────┐                          ┌──────────────┐              ┌────────────┐
│   Frontend  │                          │    Backend   │              │   Google   │
└──────┬──────┘                          └──────┬───────┘              └─────┬──────┘
       │  GET /auth/google/redirect             │                            │
       │ ──────────────────────────────────>   │                            │
       │                                        │  redirect to Google OAuth  │
       │                                        │ ────────────────────────> │
       │                                        │  callback with auth code   │
       │                                        │ <──────────────────────── │
       │                                        │                            │
       │  redirect ?code=<one_time_code>        │                            │
       │ <──────────────────────────────────── │                            │
       │                                        │                            │
       │  POST /api/auth/verify-code            │                            │
       │ ──────────────────────────────────>   │                            │
       │                                        │  validate code             │
       │  200 OK + Set-Cookie: auth_token=...   │  create Sanctum token      │
       │ <──────────────────────────────────── │                            │
       │                                        │                            │
       │  GET /api/me (cookie sent auto)        │                            │
       │ ──────────────────────────────────>   │  CookieToBearer middleware  │
       │                                        │  injects Authorization header
       │  200 OK { data: { user } }             │                            │
       │ <──────────────────────────────────── │                            │
```

### Why HTTP-only Cookie?

| Approach | XSS Risk | CSRF Risk | Notes |
|---|---|---|---|
| `localStorage` | ❌ High (JS-accessible) | ✅ None | Common but vulnerable to XSS |
| Response body (memory) | ✅ None | ✅ None | Lost on page refresh |
| **HTTP-only Cookie** | ✅ None (JS-blocked) | ⚠️ Mitigated by SameSite | Best balance of security & UX |

The `CookieToBearer` middleware (`app/Http/Middleware/CookieToBearer.php`) transparently converts the `auth_token` cookie to a `Bearer` Authorization header on every request — the frontend needs **zero token management**.

---

## 🌍 Deployment Scenarios

Configure these `.env` variables based on how your frontend and backend are deployed:

### Same Domain
> Frontend and backend on the exact same domain (e.g. `example.com` serving both)

```env
FRONTEND_URL=https://example.com
COOKIE_DOMAIN=
COOKIE_SECURE=true
COOKIE_SAME_SITE=Strict
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=Strict
```

### Subdomain
> Frontend and backend on different subdomains (e.g. `app.example.com` + `api.example.com`)

```env
FRONTEND_URL=https://app.example.com
COOKIE_DOMAIN=.example.com
COOKIE_SECURE=true
COOKIE_SAME_SITE=Lax
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=Lax
```

> `COOKIE_DOMAIN` must start with a dot (`.example.com`) to share the cookie across all subdomains.

### Cross-Domain
> Frontend and backend on completely different domains (e.g. `frontend.com` + `api.backend.com`)

```env
FRONTEND_URL=https://frontend.com
# FRONTEND_URLS=https://frontend.com,https://admin.frontend.com  # for multiple origins
COOKIE_DOMAIN=
COOKIE_SECURE=true
COOKIE_SAME_SITE=None
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=None
```

> `SameSite=None` **requires** `Secure=true`. The browser will reject insecure `SameSite=None` cookies.

---

<p align="center">
  Built with ❤️ using <a href="https://laravel.com">Laravel</a> & <a href="https://filamentphp.com">Filament</a>
</p>
