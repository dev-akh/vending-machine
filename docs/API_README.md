# Vending Machine API Documentation

## Overview

This is a comprehensive RESTful API for managing a vending machine system. The API provides endpoints for user authentication, product management, purchase processing, and sales analytics.

## Base URL

```
Development: http://localhost:8000/api/v1
Production: https://api.vending-machine.com/v1
```

## Authentication

The API uses Bearer Token authentication. Obtain a token by logging in via the `/auth/login` endpoint.

### Authentication Flow

1. **Login**: Send credentials to `/auth/login` to receive a token
2. **Include Token**: Add the token to your requests as a Bearer token
3. **Access Protected Endpoints**: Use the token to access protected resources

### Example Authentication

```bash
# Login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'

# Response
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": {...},
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}

# Use token for authenticated requests
curl -X GET http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

## API Endpoints

### Authentication

#### POST /auth/login
Authenticate user and return access token.

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "Bearer",
  "expires_at": "2024-01-03T12:00:00.000000Z",
  "user": {
    "id": 1,
    "name": "User 1",
    "role": "user"
  }
}
```

#### POST /auth/logout
Invalidate the current access token.

**Headers:**
- `Authorization: Bearer {token}`

#### GET /auth/me
Get information about the currently authenticated user.

**Headers:**
- `Authorization: Bearer {token}`

#### POST /auth/refresh
Refresh an access token using a refresh token.

**Request Body:**
```json
{
  "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

**Response:**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "Bearer",
  "expires_at": "2024-01-03T12:00:00.000000Z",
  "user": {
    "id": 1,
    "name": "User 1",
    "role": "user"
  }
}
```

### Products

#### GET /products
Get a paginated list of products with optional filtering and sorting.

**Query Parameters:**
- `search` (string): Search products by name
- `available_only` (boolean): Filter only available products
- `sort_by` (string): Sort field (name, price, quantity, created_at)
- `direction` (string): Sort direction (asc, desc)
- `per_page` (integer): Items per page (default: 20, max 100)
- `page` (integer): Page number

**Example:**
```bash
curl "http://localhost:8000/api/v1/products?search=cola&available_only=true&sort_by=price&direction=asc"
```

#### GET /products/{id}
Get detailed information about a specific product.

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Coca Cola",
    "description": "Refreshing cola drink",
    "price": 3.990,
    "quantity_available": 50,
    "is_available": true,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### POST /products
Create a new product (requires admin privileges).

**Request Body:**
```json
{
  "name": "Coca Cola",
  "description": "Refreshing cola drink",
  "price": 3.990,
  "quantity_available": 50
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Product created successfully",
  "data": {
    "id": 1,
    "name": "Coca Cola",
    "description": "Refreshing cola drink",
    "price": 3.990,
    "quantity_available": 50,
    "is_available": true,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### PUT /products/{id}
Update product information (requires admin privileges).

**Request Body:**
```json
{
  "name": "Updated Coca Cola",
  "description": "Updated refreshing cola drink",
  "price": 4.500,
  "quantity_available": 75
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Product updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Coca Cola",
    "description": "Updated refreshing cola drink",
    "price": 4.500,
    "quantity_available": 75,
    "is_available": true,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T01:00:00.000000Z"
  }
}
```

#### DELETE /products/{id}
Delete a product (requires admin privileges).

**Response:**
```json
{
  "status": "success",
  "message": "Product deleted successfully"
}
```

**Note:** Products with existing transactions cannot be deleted.

#### POST /products/{id}/purchase
Process a product purchase transaction.

**Request Body:**
```json
{
  "quantity": 2,
  "amount_paid": 10.000
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Purchase successful",
  "data": {
    "transaction": {
      "id": 1,
      "user_id": 1,
      "product_id": 1,
      "unit_price": 3.990,
      "quantity": 2,
      "total_price": 7.980,
      "amount_paid": 10.000,
      "change_given": 2.020,
      "status": "completed",
      "created_at": "2024-01-01T12:30:00.000000Z",
      "updated_at": "2024-01-01T12:30:00.000000Z"
    },
    "change_given": 2.020,
    "product_remaining": 48
  }
}
```

#### GET /products/statistics
Get product and sales statistics.

### Transactions (Admin Only)

#### GET /transactions
Get a paginated list of all transactions with optional filtering and sorting.

**Headers:**
- `Authorization: Bearer {token}`

**Query Parameters:**
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 20, max 100)
- `status` (string): Filter by transaction status (completed, pending, failed)
- `user_id` (integer): Filter by user ID
- `product_id` (integer): Filter by product ID
- `date_from` (string): Filter transactions from date (Y-m-d)
- `date_to` (string): Filter transactions to date (Y-m-d)
- `sort_by` (string): Sort field (created_at, total_price, status)
- `direction` (string): Sort direction (asc, desc)

**Example:**
```bash
curl "http://localhost:8000/api/v1/transactions?status=completed&page=1&per_page=20" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### GET /transactions/{id}
Get detailed information about a specific transaction.

**Headers:**
- `Authorization: Bearer {token}`

#### GET /transactions/statistics
Get comprehensive transaction statistics and analytics.

**Headers:**
- `Authorization: Bearer {token}`

**Query Parameters:**
- `date_from` (string): Filter statistics from date (Y-m-d)
- `date_to` (string): Filter statistics to date (Y-m-d)

**Response:**
```json
{
  "status": "success",
  "data": {
    "total_transactions": 150,
    "total_revenue": 1250.750,
    "average_transaction_value": 8.338,
    "completed_transactions": 145,
    "pending_transactions": 3,
    "failed_transactions": 2,
    "top_products": [
      {
        "product_id": 1,
        "product_name": "Coca Cola",
        "total_sold": 50,
        "revenue": 199.500
      }
    ]
  }
}
```

## Response Format

All API responses follow a consistent format:

### Success Response
```json
{
  "status": "success",
  "message": "Operation completed successfully",
  "data": {...}
}
```

### Error Response
```json
{
  "status": "error",
  "message": "Error description",
  "errors": {
    "field": ["Error message"]
  }
}
```

### HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `409` - Conflict
- `422` - Validation Error
- `500` - Internal Server Error

## Rate Limiting

API requests are limited to 60 requests per minute per IP address.

Rate limit headers are included in responses:
- `X-RateLimit-Limit`: Request limit per minute
- `X-RateLimit-Remaining`: Remaining requests
- `X-RateLimit-Reset`: Time when rate limit resets

## Error Handling

### Validation Errors
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "name": ["The name field is required."],
    "price": ["The price must be at least 0.001."]
  }
}
```

### Authentication Errors
```json
{
  "status": "error",
  "message": "Unauthorized"
}
```

### Authorization Errors
```json
{
  "status": "error",
  "message": "Access forbidden"
}
```

## Data Types

### Product
```json
{
  "id": 1,
  "name": "Coca Cola",
  "description": "Refreshing cola drink",
  "price": 3.990,
  "quantity_available": 50,
  "is_available": true,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Transaction
```json
{
  "id": 1,
  "user_id": 1,
  "product_id": 1,
  "unit_price": 3.990,
  "quantity": 2,
  "total_price": 7.980,
  "amount_paid": 10.000,
  "change_given": 2.020,
  "status": "completed",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

## SDK Examples

### JavaScript (Axios)
```javascript
// Login
const login = async (email, password) => {
  try {
    const response = await axios.post('/api/v1/auth/login', {
      email,
      password
    });
    
    const { token } = response.data.data;
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    
    return response.data;
  } catch (error) {
    console.error('Login failed:', error.response.data);
  }
};

// Get products
const getProducts = async (filters = {}) => {
  try {
    const response = await axios.get('/api/v1/products', {
      params: filters
    });
    return response.data.data;
  } catch (error) {
    console.error('Failed to get products:', error.response.data);
  }
};

// Purchase product
const purchaseProduct = async (productId, quantity, amountPaid) => {
  try {
    const response = await axios.post(`/api/v1/products/${productId}/purchase`, {
      quantity,
      amount_paid: amountPaid
    });
    return response.data.data;
  } catch (error) {
    console.error('Purchase failed:', error.response.data);
  }
};
```

### Python (Requests)
```python
import requests

class VendingMachineAPI:
    def __init__(self, base_url):
        self.base_url = base_url
        self.token = None
    
    def login(self, email, password):
        response = requests.post(f"{self.base_url}/auth/login", json={
            "email": email,
            "password": password
        })
        
        if response.status_code == 200:
            data = response.json()
            self.token = data['data']['token']
            return data
        else:
            raise Exception(f"Login failed: {response.json()}")
    
    def get_headers(self):
        return {"Authorization": f"Bearer {self.token}"}
    
    def get_products(self, **filters):
        response = requests.get(
            f"{self.base_url}/products",
            headers=self.get_headers(),
            params=filters
        )
        return response.json()
    
    def purchase_product(self, product_id, quantity, amount_paid):
        response = requests.post(
            f"{self.base_url}/products/{product_id}/purchase",
            headers=self.get_headers(),
            json={
                "quantity": quantity,
                "amount_paid": amount_paid
            }
        )
        return response.json()

# Usage
api = VendingMachineAPI("http://localhost:8000/api/v1")
api.login("admin@example.com", "password")
products = api.get_products(available_only=True, sort_by="price")
```

## Testing

### Postman Collection
A Postman collection is available in `/docs/postman-collection.json` for easy testing of all endpoints.

### Example Test Cases
```bash
# Test authentication
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password"}'

# Test product listing
curl -X GET http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer YOUR_TOKEN"

# Test product purchase
curl -X POST http://localhost:8000/api/v1/products/1/purchase \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"quantity": 1, "amount_paid": 5.000}'
```

## Changelog

### v1.0.0 (Current)
- Initial API release
- Authentication endpoints
- Product management
- Purchase processing
- Statistics endpoint

## Support

For API support and questions:
- Email: support@vending-machine.com
- Documentation: https://docs.vending-machine.com
- Issues: https://github.com/vending-machine/api/issues
