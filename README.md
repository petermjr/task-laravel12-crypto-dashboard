# Crypto Dashboard

A Laravel 12 application for monitoring cryptocurrency prices and market trends.

## Overview

This application provides a dashboard for tracking cryptocurrency data, including:
- Real-time price monitoring refreshed every 1m
- Historical price charts

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue.js 3.5 with Tailwind CSS
- **Database**: MySQL
- **Charting**: Chart.js 4.4

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL

## Installation

1. Clone the repository
2. Install PHP dependencies:
   ```
   composer install
   ```
3. Copy the environment file:
   ```
   cp .env.example .env
   ```
4. Generate application key:
   ```
   php artisan key:generate
   ```
5. Configure database settings in `.env`
6. Set up the database:
   ```
   composer run-script setup-db
   ```
7. Install and build frontend assets:
   ```
   npm install
   npm run build
   ```

## Running the Application

Start the application server:

`php artisan serve`

Run the scheduler (in a separate console):

`php artisan schedule:work`

Access the app at http://127.0.0.1:8000]
Please allow 1 minute for the scheduled job to fetch some data from the external financial data API 
so it will appear in the dashboard

## Testing

Run the test suite:

`composer run-script test`
