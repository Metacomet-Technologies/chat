# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12+ application with Inertia.js v2, React 19+, TypeScript, and Tailwind CSS v4+. It's an API-heavy real-time chat application following domain-driven design principles with comprehensive API versioning. The application allows users to sign in, chat in real-time with each other, and interact with AI using specific commands.

## Core Architecture Principles

- **Domain-first organization** with API versioning per domain
- **Strong typing throughout** (DTOs, Actions, React components)
- **Principle priority**: DRY → SOLID → YAGNI
- **Separate DTOs** for requests/responses using spatie/laravel-data
- **Action classes** with descriptive methods accepting associative arrays
- **Standard Eloquent models** with strict typing
- **Laravel Sanctum** SPA authentication
- **Laravel Pennant** for feature flagging and version switching

## Technology Stack

- **Backend**: Laravel 12+ with PHP 8.3+
- **Frontend**: Inertia.js v2 + React 19+ + TypeScript
- **Styling**: Tailwind CSS v4+ (light/dark mode + system detection)
- **Real-time**: Laravel Reverb + Laravel Echo React hooks
- **API Documentation**: OpenAPI compliance via dedoc/scramble
- **Authentication**: Laravel Sanctum SPA
- **Feature Flags**: Laravel Pennant
- **Data Transfer**: spatie/laravel-data
- **Testing**: Pest PHP testing framework
- **Static Analysis**: PHPStan (level 7) with Larastan
- **Code Style**: Tightenco/Duster

## Development Expertise & Principles

You are an expert Laravel and React developer with the following principles:

### Core Expertise
- **Laravel**: Expert-level knowledge of Laravel framework, best practices, and ecosystem
- **React**: Expert-level proficiency in React, hooks, and modern patterns
- **TypeScript**: Strong typing across all code is mandatory
- **Tailwind CSS v4**: Preferred CSS framework for all styling needs
- **Domain-Driven Design**: Organize code by business domains

### Design Requirements
- **Theme Support**: Everything must support light and dark mode with system detection
- **Type Safety**: TypeScript is a must with strong typing across all code
- **Responsive Design**: Mobile-first approach for all components
- **API-First**: Design APIs with versioning and OpenAPI compliance

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

# Type checking
npm run types

# Linting
npm run lint

# Code formatting
npm run format
npm run format:check
```

## Folder Structure

```
app/
├── Domains/
│   └── {Domain}/
│       ├── Actions/
│       │   ├── V1/
│       │   └── V2/
│       ├── Data/
│       │   ├── V1/
│       │   └── V2/
│       └── Models/
└── Http/
    └── Controllers/
        ├── Api/
        │   ├── V1/
        │   └── V2/
        └── Web/
            └── (Inertia controllers)

resources/
├── js/
│   ├── components/
│   │   ├── ui/           # ONLY shadcn components
│   │   └── (custom components)
│   ├── pages/
│   ├── layouts/
│   └── hooks/
└── views/

tests/
├── Unit/
│   └── Domains/
│       └── {Domain}/
│           ├── Actions/
│           └── Data/
└── Feature/
    └── Domains/
        └── {Domain}/
```

## API Patterns

- **URL path versioning**: `/api/v1/users`, `/api/v2/users`
- **Separate controllers per version** in `app/Http/Controllers/Api/V{n}/`
- **DTOs versioned alongside controllers** using spatie/laravel-data
- **Actions versioned per domain** in `app/Domains/{Domain}/Actions/V{n}/`
- **OpenAPI spec auto-generation** via dedoc/scramble
- **TypeScript generation** from OpenAPI + Laravel Data

## Architecture

### Backend Structure

- **Framework**: Laravel 12 with Inertia.js for SPA-like behavior
- **Domain Organization**: Domain-driven design with versioned actions and DTOs
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

- `app/Domains/` - Domain-driven business logic with versioned Actions and Data
- `app/Http/Controllers/` - HTTP controllers organized by API version and feature
- `app/Http/Middleware/` - Custom middleware including HandleInertiaRequests and HandleAppearance
- `resources/js/pages/` - React page components corresponding to Inertia routes
- `resources/js/components/` - Reusable React components (custom components at root level)
- `resources/js/components/ui/` - ONLY shadcn/Radix UI components
- `resources/js/layouts/` - Layout components for different sections (app, auth, settings)
- `resources/js/hooks/` - Custom React hooks for appearance, mobile detection, etc.
- `routes/` - Route definitions split by concern (web.php, auth.php, settings.php, api.php)

### Authentication & Authorization

The application includes a full authentication system with:
- Laravel Sanctum SPA authentication
- Login, registration, password reset flows
- Email verification
- Session-based authentication
- Inertia-based auth pages in `resources/js/pages/auth/`

### Database

- Default: SQLite (`database/database.sqlite`)
- Migrations in `database/migrations/`
- Factories for testing in `database/factories/`

### Testing Strategy

- **Unit tests for DTOs**: Validation rules, data transformation
- **Unit tests for Actions**: Business logic isolation
- **Feature tests**: Full request flows with API versioning
- **Domain-organized test structure** mirroring app structure
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

### Laravel Echo React Hooks

The application uses @laravel/echo-react (v2.2.0) for WebSocket integration. Available hooks:

#### `useEcho<TPayload>(channelName, event, callback, dependencies, visibility)`
- Main hook for listening to channels (private by default)
- Parameters:
  - `channelName`: string - The channel to connect to
  - `event`: string | string[] - Event(s) to listen for
  - `callback`: (payload: TPayload) => void - Callback when event received
  - `dependencies`: any[] - React dependencies for the callback
  - `visibility`: 'private' | 'public' | 'presence' - Channel type (default: 'private')
- Returns: `{ leaveChannel, leave, stopListening, listen, channel }`

#### `useEchoPublic<TPayload>(channelName, event, callback, dependencies)`
- For public channels (no authentication required)

#### `useEchoPresence<TPayload>(channelName, event, callback, dependencies)`
- For presence channels (who's online functionality)

#### `useEchoModel<TPayload, TModel>(model, identifier, event, callback, dependencies)`
- For Laravel model broadcasting events
- Example: `useEchoModel('App.Models.User', userId, 'UserUpdated', callback)`

#### `useEchoNotification<TPayload>(channelName, callback, event, dependencies)`
- For Laravel notification events

Example usage in components:
```tsx
// Listen to private channel for messages
useEcho<{ message: Message }>(
    `room.${roomId}`,
    '.message.sent',
    (e) => {
        setMessages(prev => [...prev, e.message]);
    },
    [roomId], // Dependencies
    'private' // Channel type
);
```

Note: The Echo instance must be configured using `configureEcho()` in app.tsx before using any hooks.

### Build Configuration

- Vite configured for React with automatic JSX runtime
- TypeScript configured with strict mode
- ESLint with React and React Hooks plugins
- Prettier with Tailwind CSS plugin for consistent formatting

## Development Workflow

- **Laravel Pennant** manages version rollouts (lottery + hard logic)
- **Frontend stays version-agnostic** - consumes versioned APIs transparently
- **Full type safety** from database to React components
- **Scramble auto-generates OpenAPI specs** from DTOs
- **TypeScript types generated** from OpenAPI specifications
- **Feature flags** control access to new API versions
- **Gradual rollout** using Pennant's lottery feature
- **API versioning** allows backward compatibility during transitions
- only shadcn components go in the ui folder
