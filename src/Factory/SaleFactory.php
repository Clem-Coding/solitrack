<?php

// namespace App\Factory;

// use App\Entity\Sale;
// use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

// /**
//  * @extends PersistentProxyObjectFactory<Sale>
//  */
// final class SaleFactory extends PersistentProxyObjectFactory
// {
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
//         return Sale::class;
//     }

//     /**
//      * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
//      *
//      * @todo add your default values here
//      */
//     protected function defaults(): array|callable
//     {
//         return [
//             'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
//             'totalPrice' => self::faker()->randomFloat(),
//             'user' => UserFactory::new(),
//         ];
//     }

//     /**
//      * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
//      */
//     protected function initialize(): static
//     {
//         return $this
//             // ->afterInstantiate(function(Sale $sale): void {})
//         ;
//     }
// }
