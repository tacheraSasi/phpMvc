# API Examples and Usage Guide

This document provides practical examples of using the PHP MVC Framework's API endpoints.

## Base URL

When using the PHP built-in server:
```
http://localhost:8000/?url=ENDPOINT
```

With proper .htaccess configuration:
```
http://localhost:8000/ENDPOINT
```

## Authentication Examples

### 1. User Login
```bash
curl -X POST http://localhost:8000/?url=api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com"
    }
  }
}
```

### 2. User Registration
```bash
curl -X POST http://localhost:8000/?url=api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "securepass123"
  }'
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "id": 655,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-10-10 10:06:38"
    }
  }
}
```

### 3. Get Current User (Protected)
```bash
# Save the token from login
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."

curl http://localhost:8000/?url=api/auth/me \
  -H "Authorization: Bearer $TOKEN"
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "User profile retrieved",
  "data": {
    "id": 1,
    "email": "admin@example.com",
    "name": "Admin User"
  }
}
```

### 4. Refresh Token
```bash
curl -X POST http://localhost:8000/?url=api/auth/refresh \
  -H "Authorization: Bearer $TOKEN"
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Token refreshed",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

## User Management (CRUD)

### 1. List All Users
```bash
curl http://localhost:8000/?url=api/users
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Users retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com"
    },
    {
      "id": 2,
      "name": "John Doe",
      "email": "john@example.com"
    }
  ]
}
```

### 2. Create User
```bash
curl -X POST http://localhost:8000/?url=api/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Smith",
    "email": "jane@example.com"
  }'
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "id": 106,
    "name": "Jane Smith",
    "email": "jane@example.com",
    "created_at": "2025-10-10 10:03:32"
  }
}
```

### 3. Get User by ID
```bash
curl http://localhost:8000/?url=api/users/1
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "User found",
  "data": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com"
  }
}
```

### 4. Update User
```bash
curl -X PUT http://localhost:8000/?url=api/users/1 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Name",
    "email": "updated@example.com"
  }'
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Name",
    "email": "updated@example.com",
    "updated_at": "2025-10-10 10:03:33"
  }
}
```

### 5. Delete User
```bash
curl -X DELETE http://localhost:8000/?url=api/users/1
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

## Protected Endpoints

### 1. User Profile
```bash
curl http://localhost:8000/?url=api/profile \
  -H "Authorization: Bearer $TOKEN"
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "User profile retrieved",
  "data": {
    "id": 1,
    "email": "admin@example.com",
    "name": "Admin User"
  }
}
```

### 2. Dashboard
```bash
curl http://localhost:8000/?url=api/dashboard \
  -H "Authorization: Bearer $TOKEN"
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Dashboard data retrieved",
  "data": {
    "user": {
      "id": 1,
      "email": "admin@example.com",
      "name": "Admin User"
    },
    "stats": {
      "total_users": 1250,
      "active_sessions": 45,
      "api_calls_today": 2850
    },
    "recent_activity": [
      "User logged in",
      "API call to /users",
      "Data updated"
    ]
  }
}
```

## Callback Routes (Closures)

### 1. Simple Callback
```bash
curl http://localhost:8000/?url=callback-demo
```

**Response (200 OK):**
```json
{
  "message": "Hello from callback route!",
  "method": "GET",
  "uri": "callback-demo"
}
```

### 2. Callback with Parameter
```bash
curl http://localhost:8000/?url=callback-demo/test-123
```

**Response (200 OK):**
```json
{
  "message": "Callback with parameter",
  "id": "test-123"
}
```

### 3. POST Callback
```bash
curl -X POST http://localhost:8000/?url=callback-post-demo \
  -H "Content-Type: application/json" \
  -d '{"test": "data", "value": 123}'
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "POST callback works!",
  "received": {
    "test": "data",
    "value": 123
  }
}
```

## Error Responses

### 1. Validation Error (422)
```bash
curl -X POST http://localhost:8000/?url=api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "invalid-email",
    "password": "123"
  }'
```

**Response (422 Unprocessable Entity):**
```json
{
  "error": "Validation failed",
  "details": {
    "email": ["The email must be a valid email address."],
    "password": ["The password must be at least 6 characters."]
  }
}
```

### 2. Unauthorized (401)
```bash
curl http://localhost:8000/?url=api/profile
```

**Response (401 Unauthorized):**
```json
{
  "error": "Authorization header missing"
}
```

### 3. Not Found (404)
```bash
curl http://localhost:8000/?url=api/nonexistent
```

**Response (404 Not Found):**
```json
{
  "error": "API endpoint not found"
}
```

### 4. Invalid Credentials (401)
```bash
curl -X POST http://localhost:8000/?url=api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "wrong@example.com",
    "password": "wrongpassword"
  }'
```

**Response (401 Unauthorized):**
```json
{
  "error": "Invalid credentials"
}
```

## Demo Credentials

For testing purposes, you can use these pre-configured credentials:

**Admin User:**
- Email: `admin@example.com`
- Password: `password123`

**Regular User:**
- Email: `user@example.com`
- Password: `userpass`

## Web Pages

The framework also includes traditional web pages:

### Home Page
```bash
curl http://localhost:8000/?url=home
```
Returns HTML home page.

### Luna Page
```bash
curl http://localhost:8000/?url=luna
```
Returns HTML Luna page.

### Luna JSON Endpoint
```bash
curl http://localhost:8000/?url=luna/render
```

**Response (200 OK):**
```json
[
  "lunaPHP",
  "Laravel",
  "cakePHP"
]
```

### Login Page
```bash
curl http://localhost:8000/?url=login
```
Returns HTML login form.

### TODO App
```bash
curl http://localhost:8000/?url=todo
```
Returns HTML TODO application.

## Tips for Using the API

1. **Always include Content-Type header** for POST/PUT requests:
   ```
   -H "Content-Type: application/json"
   ```

2. **Save your token** after login for protected endpoints:
   ```bash
   TOKEN=$(curl -s ... | jq -r '.data.token')
   ```

3. **Check HTTP status codes** to understand the response:
   - 200: Success
   - 201: Created
   - 401: Unauthorized
   - 404: Not Found
   - 422: Validation Error

4. **All API responses** follow a consistent format:
   ```json
   {
     "success": true/false,
     "message": "...",
     "data": {...}
   }
   ```
   Or for errors:
   ```json
   {
     "error": "Error message",
     "details": {...}  // Optional
   }
   ```

## Testing with cURL

Create a shell script for quick testing:

```bash
#!/bin/bash

BASE="http://localhost:8000"

# Login and save token
TOKEN=$(curl -s -X POST "$BASE/?url=api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password123"}' \
  | jq -r '.data.token')

echo "Token: $TOKEN"

# Use the token for protected endpoints
curl -s "$BASE/?url=api/profile" \
  -H "Authorization: Bearer $TOKEN" \
  | jq .
```

## Next Steps

- Explore the [TESTING_REPORT.md](TESTING_REPORT.md) for detailed test results
- Check out the [README.md](README.md) for framework documentation
- Review the code in `app/controllers/` for implementation examples
