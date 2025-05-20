# API Documentation

This document provides detailed information about the Appointment Booking System API endpoints.

## Base URL

```
http://localhost:8000/api
```

## Authentication

All API endpoints except login and registration require authentication using a Bearer token.

### Headers

```
Authorization: Bearer <your_token>
Content-Type: application/json
Accept: application/json
```

## Endpoints

### Authentication

#### Register User

```http
POST /auth/register
```

Request Body:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

Response (200 OK):
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2024-03-20T10:00:00.000000Z"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

#### Login

```http
POST /auth/login
```

Request Body:
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

Response (200 OK):
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

### Appointments

#### List Appointments

```http
GET /appointments
```

Query Parameters:
- `start_date` (optional): Filter appointments from this date (YYYY-MM-DD)
- `end_date` (optional): Filter appointments until this date (YYYY-MM-DD)
- `status` (optional): Filter by status (upcoming, past, cancelled)

Response (200 OK):
```json
{
    "appointments": [
        {
            "id": 1,
            "title": "Team Meeting",
            "description": "Weekly team sync",
            "start_time": "2024-03-20T10:00:00.000000Z",
            "end_time": "2024-03-20T11:00:00.000000Z",
            "timezone": "Asia/Kolkata",
            "status": "upcoming",
            "reminder_minutes": 30,
            "created_at": "2024-03-19T15:00:00.000000Z"
        }
    ]
}
```

#### Create Appointment

```http
POST /appointments
```

Request Body:
```json
{
    "title": "Team Meeting",
    "description": "Weekly team sync",
    "start_time": "2024-03-20T10:00:00.000000Z",
    "end_time": "2024-03-20T11:00:00.000000Z",
    "timezone": "Asia/Kolkata",
    "reminder_minutes": 30
}
```

Response (201 Created):
```json
{
    "message": "Appointment created successfully",
    "appointment": {
        "id": 1,
        "title": "Team Meeting",
        "description": "Weekly team sync",
        "start_time": "2024-03-20T10:00:00.000000Z",
        "end_time": "2024-03-20T11:00:00.000000Z",
        "timezone": "Asia/Kolkata",
        "status": "upcoming",
        "reminder_minutes": 30,
        "created_at": "2024-03-19T15:00:00.000000Z"
    }
}
```

#### Get Appointment Details

```http
GET /appointments/{id}
```

Response (200 OK):
```json
{
    "appointment": {
        "id": 1,
        "title": "Team Meeting",
        "description": "Weekly team sync",
        "start_time": "2024-03-20T10:00:00.000000Z",
        "end_time": "2024-03-20T11:00:00.000000Z",
        "timezone": "Asia/Kolkata",
        "status": "upcoming",
        "reminder_minutes": 30,
        "created_at": "2024-03-19T15:00:00.000000Z",
        "guests": [
            {
                "email": "guest@example.com",
                "name": "Guest User"
            }
        ]
    }
}
```

#### Cancel Appointment

```http
DELETE /appointments/{id}
```

Response (200 OK):
```json
{
    "message": "Appointment cancelled successfully"
}
```

### Error Responses

All endpoints may return the following error responses:

#### 400 Bad Request
```json
{
    "message": "Validation failed",
    "errors": {
        "field": ["Error message"]
    }
}
```

#### 401 Unauthorized
```json
{
    "message": "Unauthenticated"
}
```

#### 403 Forbidden
```json
{
    "message": "You do not have permission to perform this action"
}
```

#### 404 Not Found
```json
{
    "message": "Resource not found"
}
```

#### 500 Internal Server Error
```json
{
    "message": "Internal server error"
}
```

## Rate Limiting

The API implements rate limiting to prevent abuse. The current limits are:
- 60 requests per minute for authenticated users
- 30 requests per minute for unauthenticated users

When rate limit is exceeded, the API will return a 429 Too Many Requests response:
```json
{
    "message": "Too many requests, please try again later"
}
```

## Webhooks

The API supports webhooks for appointment events. To set up a webhook:

```http
POST /webhooks
```

Request Body:
```json
{
    "url": "https://your-domain.com/webhook",
    "events": ["appointment.created", "appointment.cancelled"]
}
```

Webhook events include:
- `appointment.created`
- `appointment.updated`
- `appointment.cancelled`
- `appointment.reminder`

## Support

For API support or to report issues, please:
1. Create an issue in the repository
2. Contact the development team at api-support@example.com 