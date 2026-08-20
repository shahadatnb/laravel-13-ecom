# AGENTS.md

# AI Development Guide

Project Name:
Enterprise Ecommerce ERP

Stack

Backend
- Laravel 13
- PHP 8.3+
- MySQL 8
- Redis
- Queue

Frontend

- Vue 3
- Composition API
- Pinia
- Vue Router
- TailwindCSS

Admin

- AdminLTE

Authentication

- Laravel Sanctum

--------------------------------------------------

# PRIMARY OBJECTIVE

This project must always remain

- Clean
- Maintainable
- Scalable
- Modular
- Secure
- Testable

Never sacrifice quality for speed.

--------------------------------------------------

# DEVELOPMENT PRINCIPLES

Always follow

- SOLID
- DRY
- KISS
- Clean Architecture
- PSR-12

Never violate architecture.

--------------------------------------------------

# PROJECT STRUCTURE

Backend

app/

Actions

DTO

Repositories

Services

Policies

Requests

Resources

Events

Listeners

Jobs

Notifications

Traits

Enums

Helpers

Frontend

resources/js/

Pages

Components

Layouts

Stores

Router

Services

Utils

Composables

--------------------------------------------------

# MODULES

Authentication

Users

Roles

Permissions

Dashboard

Category

Brand

Products

Stock

Orders

Order Items

Coupons

Wallet

Affiliate

Commission

Withdraw

Reports

Banner

Slider

Settings

Notification

Blog

CMS

--------------------------------------------------

# CODING STYLE

Prefer readable code.

Prefer small methods.

Prefer reusable code.

Never duplicate business logic.

Never create huge controllers.

Never create fat models.

--------------------------------------------------

# CONTROLLERS

Controller responsibilities

Validate Request

Call Service

Return Resource

Nothing more.

--------------------------------------------------

# SERVICES

Business Logic lives here.

Examples

OrderService

WalletService

AffiliateService

CommissionService

ProductService

ReportService

--------------------------------------------------

# REPOSITORIES

Repository only communicates with database.

No Request.

No Response.

No Validation.

--------------------------------------------------

# DATABASE

Always create

Migration

Model

Factory

Seeder

Policy

Resource

FormRequest

Feature Test

--------------------------------------------------

# VALIDATION

Always use FormRequest.

Custom validation messages.

Never validate inside controller.

--------------------------------------------------

# AUTHORIZATION

Always use

Policy

Gate

Middleware

Never trust frontend.

--------------------------------------------------

# API STANDARD

Use REST.

Use Resource.

Standard JSON Response.

Use Pagination.

Use Filtering.

Use Sorting.

Use Search.

--------------------------------------------------

# PERFORMANCE

Always eager load.

Never create N+1 query.

Cache settings.

Cache categories.

Queue heavy jobs.

Optimize images.

--------------------------------------------------

# SECURITY

Escape output.

Validate upload.

Use CSRF.

Prevent XSS.

Prevent SQL Injection.

Prevent Mass Assignment.

--------------------------------------------------

# FILE UPLOAD

Use Storage.

Never use public path directly.

Generate unique filename.

Generate thumbnails.

--------------------------------------------------

# FRONTEND

Vue Composition API only.

Pinia only.

Axios only.

Tailwind only.

Reusable Components.

--------------------------------------------------

# ADMIN PANEL

AdminLTE only.

Business UI must stay consistent.

--------------------------------------------------

# AFFILIATE SYSTEM

Three Level Commission.

Wallet Based.

Withdraw Approval.

Commission History.

Rank System.

Bonus System.

--------------------------------------------------

# WALLET

Credit

Debit

Transaction History

Withdraw Request

Balance Lock

Audit Log

--------------------------------------------------

# PRODUCT

Product Images

Gallery

Variants

Attributes

Brand

Category

Stock

Wholesale Price

Retail Price

SEO

--------------------------------------------------

# ORDER

Cart

Checkout

Invoice

Payment

Shipment

Return

Refund

--------------------------------------------------

# DOCUMENTATION

Every complex logic

Must contain

PHPDoc

Comments

Readable names

--------------------------------------------------

# BEFORE WRITING CODE

Always

Read existing architecture.

Reuse existing Service.

Reuse Repository.

Reuse Components.

Reuse Helpers.

Never duplicate.

--------------------------------------------------

# NEVER DO

Never use raw SQL if Eloquent can solve it.

Never disable validation.

Never skip authorization.

Never hardcode URLs.

Never hardcode credentials.

Never commit secrets.

Never create duplicated APIs.

Never break architecture.

--------------------------------------------------

# AI WORKFLOW

Requirement

↓

Database

↓

Migration

↓

Model

↓

Repository

↓

Service

↓

Controller

↓

Resource

↓

Route

↓

Vue Store

↓

Vue Component

↓

Testing

↓

Documentation

--------------------------------------------------

# SUCCESS CONDITION

Every generated code must be

Production Ready

Maintainable

Scalable

Secure

Optimized

Reusable

Documented

Testable

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/boost (BOOST) - v2
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v3

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
