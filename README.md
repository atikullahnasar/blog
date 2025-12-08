Blog Package:

A simple and flexible Laravel package for managing blog posts with built-in migrations, routes, and admin panel integration.

Installation Guide

Follow these steps to install and set up the package in your Laravel project:

Step 1: Install the Package
composer require atikullahnasar/blog:dev-main

Step 2: Publish the Migrations
php artisan vendor:publish --provider="atikullahnasar\blog\Provider\BlogPackageServiceProvider" --tag=blog-migrations

Step 3: Run the Migrations
php artisan migrate

Step 4 (Final): Access Blog Management

After installation, open:

/beft/blogs


Example:

http://example.com/beft/blogs
