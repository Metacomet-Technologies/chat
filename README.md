# Real-Time Chat Application

[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F84328ec6-bf29-455d-b98b-919d1641226f%3Fdate%3D1%26label%3D1%26commit%3D1&style=for-the-badge)](https://forge.laravel.com/servers/847680/sites/2812408)

A modern real-time chat application built with Laravel, React, and TypeScript that enables users to communicate instantly with AI-powered assistance.

## Features

- 🔐 **User Authentication**: Secure sign-in and registration system
- 💬 **Real-Time Chat**: Instant messaging between users
- 🤖 **AI Integration**: Command-based AI responses with conversation context
- 🎨 **Theme Support**: Light, dark, and system-detected themes
- ⚡ **Modern Stack**: Laravel 12 + React 19 + TypeScript + Tailwind CSS v4
- 📱 **Responsive Design**: Works seamlessly across all devices

## Tech Stack

### Backend
- **Laravel 12**: PHP framework for robust backend
- **Inertia.js**: Seamless SPA experience without API complexity
- **SQLite**: Default database (easily switchable to MySQL/PostgreSQL)
- **Pest PHP**: Modern testing framework

### Frontend
- **React 19**: Latest React with TypeScript
- **Tailwind CSS v4**: Utility-first CSS framework
- **Radix UI**: Accessible component primitives
- **Vite**: Lightning-fast build tool

## Prerequisites

- PHP 8.2+
- Node.js 18+
- Composer
- npm or yarn

## Installation

### 1. Clone the repository
```bash
git clone <repository-url>
cd chat
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node dependencies
```bash
npm install
```

### 4. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Database setup
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed # Optional: seed with sample data
```

### 6. Build frontend assets
```bash
npm run build
```

## Development

### Start development servers
```bash
# Run both backend and frontend with hot reload
composer dev

# Or run separately:
php artisan serve  # Backend
npm run dev       # Frontend with Vite
```

## Testing

### Run all tests
```bash
composer test
```

### Run specific test suites
```bash
php artisan test --filter=Feature
php artisan test --filter=Unit
```

### Frontend type checking
```bash
npm run types
```

## Code Quality

### Quick Format & Lint
```bash
# Run all code quality checks at once
./format.sh

# Options:
./format.sh -v          # Verbose output
./format.sh -c          # Continue on errors
./format.sh -s          # Skip dependency checks
./format.sh -h          # Show help
```

This script automatically runs:
- IDE Helper generation
- PHPStan static analysis
- Duster code formatting
- Prettier formatting
- ESLint
- TypeScript type checking

### PHP
```bash
# Static analysis with PHPStan
./vendor/bin/phpstan analyse

# Code formatting
./vendor/bin/duster fix

# Linting check
./vendor/bin/duster lint
```

### JavaScript/TypeScript
```bash
# Type checking
npm run types

# Linting
npm run lint

# Formatting
npm run format
```

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Request handlers
│   │   └── Middleware/      # HTTP middleware
│   └── Models/              # Eloquent models
├── resources/
│   ├── js/
│   │   ├── components/      # Reusable React components
│   │   ├── pages/          # Inertia page components
│   │   ├── layouts/        # Layout components
│   │   └── hooks/          # Custom React hooks
│   └── css/                # Stylesheets
├── routes/                 # Application routes
├── database/
│   ├── migrations/         # Database migrations
│   └── factories/          # Model factories
└── tests/                  # Test suites
```

## Development Principles

### Code Standards
- **TypeScript**: Strong typing across all frontend code
- **DRY**: Don't Repeat Yourself - reusable components and utilities
- **SOLID**: Single responsibility, Open/closed, Liskov substitution, Interface segregation, Dependency inversion
- **YAGNI**: You Aren't Gonna Need It - build only what's necessary

### Styling Guidelines
- Tailwind CSS v4 for all styling
- Support for light/dark/system themes
- Mobile-first responsive design
- Consistent spacing and color schemes

### Best Practices
- Comprehensive type definitions
- Automated testing for critical paths
- Code formatting on commit
- Regular dependency updates

## Deployment

### Production build
```bash
npm run build
php artisan optimize
```

### Environment configuration
Ensure `.env` is properly configured with:
- Database credentials
- App URL
- Mail configuration (if needed)
- Queue driver (for real-time features)

## Contributing

1. Create a feature branch
2. Make your changes with appropriate tests
3. Run linters and formatters
4. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
