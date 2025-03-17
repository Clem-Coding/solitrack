<?php

namespace App\Factory;

use App\Entity\Category;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Category>
 */
final class CategoryFactory extends PersistentProxyObjectFactory
{
    public function __construct() {}

    public static function class(): string
    {
        return Category::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => 'Default Category', // Pour éviter que Faker génère du texte
        ];
    }

    public static function createCategories(): void
    {
        self::createOne(['name' => 'Clothing']);
        self::createOne(['name' => 'Books']);
        self::createOne(['name' => 'Electronics']);
        self::createOne(['name' => 'Other']);
    }
}
