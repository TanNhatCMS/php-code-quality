<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Set\LaravelLevelSetList;
use Rector\Set\ValueObject\LevelSetList;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    // Autoload project Laravel đã được mount vào /project
    $rectorConfig->autoloadPaths([
        '/project/vendor/autoload.php',
    ]);

    // Các thư mục trong project Laravel
    $rectorConfig->paths([
        '/project/app',
        '/project/routes',
        '/project/database',
        '/project/tests',
    ]);

    // Bỏ qua những thư mục không cần phân tích
    $rectorConfig->skip([
        '/project/storage',
        '/project/bootstrap/cache',
        '/project/vendor',
    ]);

    // Cấu hình PHP version
    $rectorConfig->phpVersion(PhpVersion::PHP_82);

    // Bộ rule áp dụng
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        LaravelLevelSetList::UP_TO_LARAVEL_110,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
    ]);
};
