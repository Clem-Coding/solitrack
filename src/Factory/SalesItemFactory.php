<?php

// namespace App\Factory;

// use App\Entity\SalesItem;
// use App\Repository\SalesItemRepository;
// use Doctrine\ORM\EntityRepository;
// use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
// use Zenstruck\Foundry\Persistence\Proxy;
// use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

// /**
//  * @extends PersistentProxyObjectFactory<SalesItem>
//  */
// final class SalesItemFactory extends PersistentProxyObjectFactory{
//     /**
//      * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
//      *
//      * @todo inject services if required
//      */
//     public function __construct()
//     {
//     }

//     public static function class(): string
//     {
//         return SalesItem::class;
//     }

//     /**
//      * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
//      *
//      * @todo add your default values here
//      */
//     protected function defaults(): array|callable
//     {
//         return [
//             'category' => CategoryFactory::new(),
//             'sale' => 1::new(),
//         ];
//     }

//     /**
//      * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
//      */
//     protected function initialize(): static
//     {
//         return $this
//             // ->afterInstantiate(function(SalesItem $salesItem): void {})
//         ;
//     }
// }
