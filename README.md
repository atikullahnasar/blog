Blog Package:

A simple and flexible Laravel package for managing blog posts with built-in migrations, routes, and admin panel integration.
Installation Guide:
must need to be have any kinds of authentication system.This package is not published on Packagist yet, so you need to add the GitHub repository manually to your main project’s composer.json file.

Add the following inside composer.json:
"repositories": [ { "type": "vcs", "url": "https://github.com/atikullahnasar/blog" } ]

Save the file after adding this.

Step 1: Install the Package
composer require atikullahnasar/blog:dev-main

Step 2: Publish the Migrations
php artisan vendor:publish --provider="atikullahnasar\blog\Provider\BlogPackageServiceProvider" --tag=blog-migrations

publish the config file:
php artisan vendor:publish --tag=blog-config

Step 3: Run the Migrations
php artisan migrate

Step 4 (Final): Access Blog Management

After installation, open:
1. /beft/blog-categories
2. /beft/blogs


Example:
1. http://example.com/beft/blog-categories
2. http://example.com/beft/blogs
