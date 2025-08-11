# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a real-time chat application built with Laravel 12 and React, using Inertia.js for seamless SPA behavior. The application allows users to sign in, chat in real-time with each other, and interact with AI using specific commands. The project uses TypeScript for the frontend with Tailwind CSS v4 for styling, following modern Laravel architecture with server-side rendering capabilities.

## Development Expertise & Principles

You are an expert Laravel and React developer with the following principles:

### Core Expertise
- **Laravel**: Expert-level knowledge of Laravel framework, best practices, and ecosystem
- **React**: Expert-level proficiency in React, hooks, and modern patterns
- **TypeScript**: Strong typing across all code is mandatory
- **Tailwind CSS v4**: Preferred CSS framework for all styling needs

### Design Requirements
- **Theme Support**: Everything must support light and dark mode with system detection
- **Type Safety**: TypeScript is a must with strong typing across all code
- **Responsive Design**: Mobile-first approach for all components

### Development Principles (in order of priority)
1. **DRY (Don't Repeat Yourself)**: Eliminate code duplication through reusable components, utilities, and abstractions
2. **SOLID Principles**: 
   - Single Responsibility: Each class/component should have one reason to change
   - Open/Closed: Open for extension, closed for modification
   - Liskov Substitution: Derived classes must be substitutable for base classes
   - Interface Segregation: Many specific interfaces over general-purpose ones
   - Dependency Inversion: Depend on abstractions, not concretions
3. **YAGNI (You Aren't Gonna Need It)**: Build only what's necessary, avoid premature optimization

## Common Development Commands

### Quick Format & Lint

```bash
# Run all formatting and linting checks
./format.sh

# Options:
./format.sh -v          # Verbose output
./format.sh -c          # Continue on errors
./format.sh -s          # Skip dependency checks
./format.sh -h          # Show help
```

This script runs all code quality tools in sequence:
- PHP IDE Helper generation
- PHPStan static analysis
- Duster code formatting
- Prettier formatting
- ESLint
- TypeScript type checking

### Backend (PHP/Laravel)

```bash
# Run development server with queue worker
composer dev

# Run development server with SSR
composer dev:ssr

# Run tests
composer test
# Or directly with Pest
php artisan test

# Static analysis
./vendor/bin/phpstan analyse

# Code formatting and linting with Duster
./vendor/bin/duster lint
./vendor/bin/duster fix

# Database operations
php artisan migrate
php artisan migrate:rollback
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models
```

### Frontend (React/TypeScript)

```bash
# Development server
npm run dev

# Build for production
npm run build

# Build with SSR
npm run build:ssr

# Type checking
npm run types

# Linting
npm run lint

# Code formatting
npm run format
npm run format:check
```

## Architecture

### Backend Structure

- **Framework**: Laravel 12 with Inertia.js for SPA-like behavior
- **Testing**: Pest PHP testing framework with RefreshDatabase trait
- **Static Analysis**: PHPStan (level 7) with Larastan extension
- **Code Style**: Tightenco/Duster for consistent formatting

### Frontend Structure

- **Framework**: React 19 with TypeScript
- **State Management**: Inertia.js handles server-state synchronization
- **Routing**: Ziggy for Laravel route generation in JavaScript
- **UI Components**: Radix UI primitives with custom components in `resources/js/components/ui/`
- **Styling**: Tailwind CSS v4 with CSS-in-JS support via Vite
- **Build Tool**: Vite with Laravel plugin for HMR and asset compilation

### Key Directories

- `app/Http/Controllers/` - HTTP controllers organized by feature (Auth, Settings)
- `app/Http/Middleware/` - Custom middleware including HandleInertiaRequests and HandleAppearance
- `resources/js/pages/` - React page components corresponding to Inertia routes
- `resources/js/components/` - Reusable React components and UI primitives
- `resources/js/layouts/` - Layout components for different sections (app, auth, settings)
- `resources/js/hooks/` - Custom React hooks for appearance, mobile detection, etc.
- `routes/` - Route definitions split by concern (web.php, auth.php, settings.php)

### Authentication & Authorization

The application includes a full authentication system with:
- Login, registration, password reset flows
- Email verification
- Session-based authentication
- Inertia-based auth pages in `resources/js/pages/auth/`

### Database

- Default: SQLite (`database/database.sqlite`)
- Migrations in `database/migrations/`
- Factories for testing in `database/factories/`

### Testing Strategy

- Feature tests in `tests/Feature/` for integration testing
- Unit tests in `tests/Unit/` for isolated component testing
- Pest configuration in `tests/Pest.php`
- All feature tests use RefreshDatabase trait

### Component Patterns

React components follow these patterns:
- TypeScript with explicit type definitions in `resources/js/types/`
- Radix UI for accessible, unstyled components
- class-variance-authority (CVA) for component variants
- clsx and tailwind-merge for conditional styling
- Custom hooks in `resources/js/hooks/` for shared logic

### State Management

- Server state managed by Inertia.js
- Shared props defined in HandleInertiaRequests middleware
- Appearance state (theme) handled via cookies and middleware
- Sidebar state persisted in cookies

### Build Configuration

- Vite configured for React with automatic JSX runtime
- TypeScript configured with strict mode
- ESLint with React and React Hooks plugins
- Prettier with Tailwind CSS plugin for consistent formatting