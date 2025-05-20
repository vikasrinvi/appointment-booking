# Appointment Booking System

A modern web application for managing appointments with features like calendar view, email notifications, and timezone support.

## Features

- 📅 Interactive calendar view for appointment management
- 🔐 User authentication and authorization
- 📧 Email notifications for appointments
- 🌍 Timezone support (default: Asia/Kolkata)
- 📱 Responsive design for all devices
- 🎨 Modern UI with Tailwind CSS
- 🔄 Real-time updates

## Prerequisites

- Node.js (v14 or higher)
- PHP (v8.0 or higher)
- Composer
- MySQL (v5.7 or higher)
- npm or yarn

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd appointment-booking
```

2. Install backend dependencies:
```bash
composer install
```

3. Configure the environment:
```bash
cp .env.example .env
```
Edit the `.env` file with your database credentials and other configurations.

4. Generate application key:
```bash
php artisan key:generate
```

5. Run database migrations:
```bash
php artisan migrate
```

6. Install frontend dependencies:
```bash
cd frontend
npm install
```

## Running the Application

1. Start the Laravel backend server:
```bash
php artisan serve
```

2. In a separate terminal, start the Vue.js frontend:
```bash
cd frontend
npm run dev
```

The application will be available at:
- Frontend: http://localhost:5173
- Backend API: http://localhost:8000

## Testing

### Backend Tests

Run Laravel tests:
```bash
php artisan test
```

### Frontend Tests

Run Vue.js tests:
```bash
cd frontend
npm run test
```


## Project Structure

```
appointment-booking/
├── app/                    # Laravel backend
│   ├── Http/              # Controllers and Middleware
│   ├── Models/            # Eloquent Models
│   └── Services/          # Business Logic
├── frontend/              # Vue.js frontend
│   ├── src/
│   │   ├── components/    # Reusable Vue components
│   │   ├── views/         # Page components
│   │   ├── stores/        # Pinia stores
│   │   └── router/        # Vue Router configuration
│   └── tests/             # Frontend tests
└── tests/                 # Backend tests
```

## API Documentation

The API documentation is available in two formats:

1. Interactive Swagger UI: `/docs`
   - Provides an interactive interface to explore and test the API
   - Includes detailed request/response examples
   - Shows authentication requirements
   - Allows testing endpoints directly from the browser

2. OpenAPI Specification: `/api/docs/swagger.json`
   - Raw OpenAPI/Swagger specification
   - Can be imported into API testing tools
   - Useful for generating client libraries

### Key Endpoints

- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `GET /api/appointments` - List appointments
- `POST /api/appointments` - Create appointment
- `DELETE /api/appointments/{id}` - Cancel appointment

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, email vikasmrnv@@gmail.com or create an issue in the repository.
