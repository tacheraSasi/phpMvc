# Enhanced PHP MVC Framework

A minimal but powerful PHP MVC framework with modern features.

## Features

- **Middleware System**: Global, per-route, and chainable middleware
- **Request/Response Wrappers**: Enhanced request handling and response formatting
- **Error Handling**: Centralized exception and HTTP error handling
- **CLI Tool**: Code generation with `viper` commands
- **Environment Config**: Auto-loading .env files and config helpers
- **Dependency Injection**: Simple DI container with constructor injection
- **Module System**: Auto-loaded modules with routes, services, and config
- **Validation**: DTO validation system for request data
- **Security**: CORS, rate limiting, and request parsing
- **Logging**: Request and error logging

## Quick Start

1. Copy `.env.example` to `.env` and configure your settings
2. Use the CLI tool to generate components:
   ```bash
   php viper make:controller UserController
   php viper make:service UserService
   php viper make:module UserModule
   ```

3. Define routes with middleware:
   ```php
   $app->get('/users', [UserController::class, 'index'])
       ->middleware(['auth', 'cors']);
   ```

## CLI Commands

- `viper make:controller <Name>` - Generate a controller
- `viper make:service <Name>` - Generate a service
- `viper make:module <Name>` - Generate a module
- `viper make:model <Name>` - Generate a model

## Requirements

- PHP 8.0+
- Composer (recommended)
