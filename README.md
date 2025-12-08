step 1: composer require atikullahnasar/blog:dev-main
step 2: php artisan vendor:publish --provider="atikullahnasar\blog\Provider\BlogPackageServiceProvider" --tag=blog-migrations
step 3: php artisan migrate
