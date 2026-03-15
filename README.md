# NutriSport Challenge - E-Commerce API

## Project Overview

The API supports two main user types: **Clients** (customers) and **Agents** (backoffice administrators), each with their own authentication and authorization flows.

### Key Features

- **Dual Authentication**: Separate auth systems for clients and agents using JWT tokens
- **Product Catalog**: Browse and manage nutritional supplement products with site-specific pricing
- **Shopping Cart**: Stateless cart management for seamless checkout
- **Order Management**: Complete order lifecycle from placement to fulfillment
- **Feed Integration**: Product feeds in JSON and XML formats for external integrations
- **Role-Based Access Control**: Permission-based access for agent operations

---

## Project Structure

### Directory Layout

```
app/
├── Console/              # Artisan commands
├── Enums/                # Enumeration classes
│   ├── OrderStatus.php   # Order status states
│   └── PaymentMethod.php # Payment method options
├── Events/               # Event classes
│   └── OrderPlaced.php   # Order placement event
├── Exceptions/           # Custom exception classes
│   ├── CartException.php
│   ├── OrderException.php
│   └── ProductException.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── Client/           # Client-facing endpoints
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── CartController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   └── ProductController.php
│   │   │   ├── BackOffice/       # Admin endpoints
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   └── ProductController.php
│   │   │   └── Feed/             # Feed endpoints
│   │   │       └── ProductFeedController.php
│   ├── Middleware/
│   │   ├── ResolveSite.php       # Site resolution from header
│   │   └── EnsureAgentHasAccess.php
│   ├── Requests/                 # Form request validation
│   │   ├── Client/
│   │   │   ├── LoginRequest.php
│   │   │   ├── RegisterRequest.php
│   │   │   ├── AddToCartRequest.php
│   │   │   ├── PlaceOrderRequest.php
│   │   │   ├── UpdateProfileRequest.php
│   │   │   └── UpdatePasswordRequest.php
│   │   └── BackOffice/
│   │       ├── LoginRequest.php
│   │       ├── CreateProductRequest.php
│   │       ├── ListOrdersRequest.php
│   │       └── ListProductsRequest.php
│   └── Resources/                # API response transformers
│       ├── UserResource.php
│       ├── ProductResource.php
│       ├── OrderResource.php
│       ├── OrderItemResource.php
│       ├── OrderListResource.php
│       └── AgentResource.php
├── Interfaces/
│   └── FeedGeneratorInterface.php # Feed generation contract
├── Models/                        # Eloquent models
│   ├── User.php
│   ├── Product.php
│   ├── ProductPrice.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Site.php
│   └── Agent.php
├── Services/                      # Business logic layer
│   ├── BaseService.php            # Base service class
│   ├── AuthService.php            # Client authentication
│   ├── CartService.php            # Cart management
│   ├── OrderService.php           # Order processing
│   ├── ProductService.php         # Product management
│   ├── ReportService.php          # Analytics & reports
│   ├── Feed/
│   │   ├── JsonFeedGenerator.php  # JSON feed generation
│   │   └── XmlFeedGenerator.php   # XML feed generation
│   └── AgentAuthService.php       # Agent authentication
└── Listeners/                     # Event listeners
    └── SendAdminOrderNotification.php

database/
├── migrations/                    # Database schema
├── factories/                     # Model factories for testing
│   ├── UserFactory.php
│   ├── ProductFactory.php
│   ├── OrderFactory.php
│   ├── OrderItemFactory.php
│   └── AgentFactory.php
└── seeders/                       # Database seeders
    └── DatabaseSeeder.php

routes/
├── api_v1/
│   ├── client.php                # Client API routes
│   └── backoffice.php            # Admin API routes
└── web.php                        # Web routes

resources/
├── views/
│   └── emails/                    # Email templates
│       ├── orders/
│       │   ├── admin-notification.blade.php
│       │   └── client-confirmation.blade.php
│       └── ...
└── js/
    ├── app.js
    ├── bootstrap.js
    └── echo.js                    # WebSocket configuration

tests/
├── Feature/                       # Feature tests
│   ├── CartTest.php
│   ├── OrderTest.php
│   └── ...
└── Unit/                          # Unit tests
    └── ...
```

### Core Classes Overview

#### Services (Business Logic)

| Service | Purpose | Key Methods |
|---------|---------|------------|
| **AuthService** | Client authentication & profile | `register()`, `login()`, `updateProfile()`, `updatePassword()` |
| **CartService** | Shopping cart management | `show()`, `store()`, `delete()`, `clear()` |
| **OrderService** | Order processing & fulfillment | `store()`, `index()`, `getRecentOrders()`, `validateStock()` |
| **ProductService** | Product catalog management | `index()`, `show()`, `store()`, `generateFeed()` |
| **ReportService** | Analytics & reporting | `generateDailyReport()`, `getMostSoldProduct()`, `getRevenuePerSite()` |
| **AgentAuthService** | Agent authentication | `login()`, `logout()`, `refresh()` |

#### Controllers (API Endpoints)

**Client Controllers:**
- `AuthController`: User registration, login, profile management
- `CartController`: Cart operations (add, remove, clear items)
- `OrderController`: Order creation and history
- `ProductController`: Product browsing and search

**BackOffice Controllers:**
- `AuthController`: Agent login and session management
- `OrderController`: View all orders with filters
- `ProductController`: Create and manage products

**Feed Controller:**
- `ProductFeedController`: Generate JSON/XML product feeds

#### Exceptions (Error Handling)

| Exception | Usage |
|-----------|-------|
| **CartException** | Cart-related errors (not found, empty) |
| **OrderException** | Order processing errors (stock, validation) |
| **ProductException** | Product-related errors (not found, invalid) |

---

## API Endpoints Summary

### Client Routes (`/api/v1/client`)

```
Authentication:
  POST   /auth/register          - Register new user
  POST   /auth/login             - Login user
  POST   /auth/logout            - Logout user
  POST   /auth/refresh           - Refresh JWT token
  GET    /auth/profile           - Get user profile
  PUT    /auth/profile           - Update profile
  PUT    /auth/password          - Change password

Products:
  GET    /products               - List products (paginated)
  GET    /products/{id}          - Get single product

Cart:
  GET    /cart/{cartId}          - View cart
  POST   /cart/items             - Add to cart
  DELETE /cart/{cartId}/items/{productId} - Remove from cart
  DELETE /cart/{cartId}          - Clear cart

Orders:
  GET    /orders                 - List user orders
  POST   /orders                 - Create order
```

### BackOffice Routes (`/api/v1/backoffice`)

```
Authentication:
  POST   /auth/login             - Agent login
  POST   /auth/logout            - Agent logout
  POST   /auth/refresh           - Refresh agent token

Management:
  GET    /orders                 - List all orders
  POST   /products               - Create product
```

### Feed Routes (`/api/v1/feeds`)

```
Public Feeds:
  GET    /products.json          - Product feed (JSON)
  GET    /products.xml           - Product feed (XML)
```

---

## API Endpoints

### Base URL

All requests require the `X-Site-Domain` header (except feed endpoints):
```
X-Site-Domain: test.nutri-sport.fr
```

---

## 1. Client Authentication

**Summary:** User registration, login, profile management, and session handling for customers.

### Register
Create a new customer account.

**Endpoint:** `PUT /auth/register`

**Rate Limit:** 10 requests per minute

**Request Body:**
```json
{
    "email": "customer@example.com",
    "password": "SecurePassword123!",
    "first_name": "John",
    "last_name": "Doe"
}
```

**Response (201):**
```json
{
    "data": {
        "id": 1,
        "email": "customer@example.com",
        "first_name": "John",
        "last_name": "Doe",
        "created_at": "2024-01-15T10:30:00Z"
    },
    "message": "Registration successful"
}
```

---

### Login
Authenticate and receive JWT token.

**Endpoint:** `POST /auth/login`

**Rate Limit:** 10 requests per minute

**Request Body:**
```json
{
    "email": "customer@example.com",
    "password": "SecurePassword123!"
}
```

**Response (200):**
```json
{
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer",
        "expires_in": 3600
    },
    "message": "Login successful"
}
```

---

### Get Profile
Retrieve authenticated user's profile information.

**Endpoint:** `GET /auth/profile`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Response (200):**
```json
{
    "data": {
        "id": 1,
        "email": "customer@example.com",
        "first_name": "John",
        "last_name": "Doe",
        "created_at": "2024-01-15T10:30:00Z"
    }
}
```

---

### Update Profile
Modify user profile information.

**Endpoint:** `PUT /auth/profile`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Request Body:**
```json
{
    "first_name": "Jonathan",
    "last_name": "Smith"
}
```

**Response (200):**
```json
{
    "data": {
        "id": 1,
        "email": "customer@example.com",
        "first_name": "Jonathan",
        "last_name": "Smith"
    },
    "message": "Profile updated successfully"
}
```

---

### Update Password
Change user password.

**Endpoint:** `PUT /auth/password`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Request Body:**
```json
{
    "current_password": "SecurePassword123!",
    "new_password": "NewSecurePassword456!",
    "new_password_confirmation": "NewSecurePassword456!"
}
```

**Response (200):**
```json
{
    "message": "Password updated successfully"
}
```

---

### Refresh Token
Generate a new access token using the current token.

**Endpoint:** `POST /auth/refresh`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Response (200):**
```json
{
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

---

### Logout
Invalidate the current session.

**Endpoint:** `POST /auth/logout`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Response (200):**
```json
{
    "message": "Logout successful"
}
```

---

## 2. Product Catalog

**Summary:** Browse and retrieve product information with site-specific pricing and availability.

### List Products
Get paginated list of all products with prices for the current site.

**Endpoint:** `GET /products`

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15)

**Response (200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Whey Protein Isolate",
            "description": "High-quality whey protein isolate",
            "price": 49.99,
            "in_stock": true
        },
        {
            "id": 2,
            "name": "Creatine Monohydrate",
            "description": "Pure creatine monohydrate powder",
            "price": 19.99,
            "in_stock": true
        }
    ],
    "links": {
        "first": "{{ url }}/api/v1/products?page=1",
        "last": "{{ url }}/api/v1/products?page=5",
        "prev": null,
        "next": "{{ url }}/api/v1/products?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "per_page": 15,
        "to": 15,
        "total": 73
    }
}
```

---

### Get Single Product
Retrieve detailed information for a specific product.

**Endpoint:** `GET /products/{id}`

**Response (200):**
```json
{
    "data": {
        "id": 1,
        "name": "Whey Protein Isolate",
        "description": "High-quality whey protein isolate",
        "price": 49.99,
        "in_stock": true
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Product not found",
    "data": null
}
```

---

## 3. Shopping Cart

**Summary:** Manage shopping cart items without authentication. Create, view, and modify cart contents.

### Create/Add to Cart
Add products to a new or existing cart.

**Endpoint:** `POST /cart/items`

**Request Body:**
```json
{
    "cart_id": "abc123def456",
    "product_id": 1,
    "quantity": 2
}
```

**Response (201):**
```json
{
    "data": {
        "cart_id": "abc123def456",
        "items": [
            {
                "product_id": 1,
                "name": "Whey Protein Isolate",
                "quantity": 2,
                "price": 49.99,
                "subtotal": 99.98
            }
        ],
        "total": 99.98
    }
}
```

---

### Get Cart
Retrieve cart contents and totals.

**Endpoint:** `GET /cart/{cartId}`

**Response (200):**
```json
{
    "data": {
        "cart_id": "abc123def456",
        "items": [
            {
                "product_id": 1,
                "name": "Whey Protein Isolate",
                "quantity": 2,
                "price": 49.99,
                "subtotal": 99.98
            }
        ],
        "total": 99.98
    }
}
```

---

### Remove from Cart
Delete a product from cart.

**Endpoint:** `DELETE /cart/{cartId}/items/{productId}`

**Response (200):**
```json
{
    "message": "Item removed from cart",
    "data": {
        "cart_id": "abc123def456",
        "items": [],
        "total": 0
    }
}
```

---

## 4. Orders (Authenticated)

**Summary:** Create, retrieve, and manage customer orders. Requires client authentication.

### Create Order
Place a new order from cart.

**Endpoint:** `POST /orders`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Request Body:**
```json
{
    "cart_id": "abc123def456",
    "payment_method": "credit_card",
    "shipping_address": {
        "street": "123 Main St",
        "city": "Paris",
        "postal_code": "75001",
        "country": "FR"
    }
}
```

**Response (201):**
```json
{
    "data": {
        "id": 1,
        "order_number": "ORD-2024-001",
        "status": "pending",
        "total": 99.98,
        "items": [
            {
                "product_id": 1,
                "name": "Whey Protein Isolate",
                "quantity": 2,
                "price": 49.99
            }
        ],
        "created_at": "2024-01-15T10:30:00Z"
    }
}
```

---

### List Orders
Get all orders for authenticated user.

**Endpoint:** `GET /orders`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Query Parameters:**
- `status` (optional): Filter by status (pending, processing, shipped, delivered)
- `page` (optional): Page number

**Response (200):**
```json
{
    "data": [
        {
            "id": 1,
            "order_number": "ORD-2024-001",
            "status": "shipped",
            "total": 99.98,
            "created_at": "2024-01-15T10:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 5
    }
}
```

---

## 5. Agent Authentication

**Summary:** Backoffice agent login and session management for administrators.

### Agent Login
Authenticate as a backoffice agent.

**Endpoint:** `POST /backoffice/auth/login`

**Request Body:**
```json
{
    "email": "agent@nutrisport.com",
    "password": "AgentPassword123!"
}
```

**Response (200):**
```json
{
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer",
        "expires_in": 3600
    },
    "message": "Login successful"
}
```

---

### Agent Refresh Token
Generate new token for agent session.

**Endpoint:** `POST /backoffice/auth/refresh`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Response (200):**
```json
{
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

---

### Agent Logout
End agent session.

**Endpoint:** `POST /backoffice/auth/logout`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Response (200):**
```json
{
    "message": "Logout successful"
}
```

---

## 6. Backoffice Management

**Summary:** Administrative operations for managing orders and products. Requires agent authentication and appropriate permissions.

### List Orders (Agent)
Retrieve all orders in the system.

**Endpoint:** `GET /backoffice/orders`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Query Parameters:**
- `status` (optional): Filter by status
- `page` (optional): Page number

**Response (200):**
```json
{
    "data": [
        {
            "id": 1,
            "order_number": "ORD-2024-001",
            "customer_email": "customer@example.com",
            "status": "shipped",
            "total": 99.98,
            "created_at": "2024-01-15T10:30:00Z"
        }
    ]
}
```

---

### Create Product (Agent)
Add a new product to catalog.

**Endpoint:** `POST /backoffice/products`

**Headers:**
- `Authorization: Bearer {access_token}` (required)

**Request Body:**
```json
{
    "name": "BCAA Complex",
    "description": "Branched-chain amino acids supplement",
    "sku": "BCAA-001",
    "base_price": 34.99
}
```

**Response (201):**
```json
{
    "data": {
        "id": 10,
        "name": "BCAA Complex",
        "description": "Branched-chain amino acids supplement",
        "sku": "BCAA-001",
        "base_price": 34.99,
        "created_at": "2024-01-15T10:30:00Z"
    }
}
```

---

## 7. Product Feeds

**Summary:** Public product feeds for external integrations. No authentication required.

### Get Product Feed
Export products in JSON or XML format.

**Endpoint:** `GET /feeds/products.{format}`

**Parameters:**
- `format` (required): `json` or `xml`

**Response (JSON):**
```json
{
    "products": [
        {
            "id": 1,
            "name": "Whey Protein Isolate",
            "description": "High-quality whey protein isolate",
            "sku": "WPI-001",
            "price": 49.99
        }
    ]
}
```

**Response (XML):**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<products>
    <product>
        <id>1</id>
        <name>Whey Protein Isolate</name>
        <description>High-quality whey protein isolate</description>
        <sku>WPI-001</sku>
        <price>49.99</price>
    </product>
</products>
```

---

## Error Handling

All endpoints return consistent error responses:

**400 Bad Request:**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required"]
    }
}
```

**401 Unauthorized:**
```json
{
    "success": false,
    "message": "Unauthenticated"
}
```

**403 Forbidden:**
```json
{
    "success": false,
    "message": "Insufficient permissions"
}
```

**429 Too Many Requests:**
```json
{
    "success": false,
    "message": "Too many requests. Please try again later."
}
```

---

## Postman Collection

A complete Postman collection is available for testing all API endpoints.

### Import the Collection

1. Open Postman
2. Click **Import** button
3. Select **File** tab
4. Choose `public/nutrisport-challenge.postman_collection.json`
5. Click **Import**

The collection includes:
- All Client endpoints (Auth, Products, Cart, Orders)
- All BackOffice endpoints (Auth, Orders, Products)
- All Feed endpoints (JSON, XML)
- Pre-configured headers and authentication
- Sample request bodies
- Environment variables for easy testing

---

## Installation & Setup

### Requirements
- PHP 8.2+
- Laravel 11+
- MySQL 8.0+
- Composer

### Installation Steps

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env`
4. Generate app key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Seed database: `php artisan db:seed`
7. Start server: `php artisan serve`

### Quick Testing with Postman

After installation, import the Postman collection (see **Postman Collection** section above) to quickly test all endpoints without manual configuration.

---

## Testing

Run the test suite:
```bash
php artisan test
```

Run specific test file:
```bash
php artisan test tests/Feature/CartTest.php
```

---

## License

This project is licensed under the MIT License.
