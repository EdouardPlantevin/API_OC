<?php

namespace App\DataFixtures;

use App\Entity\Color;
use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\Storage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }


    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $colors = ["Noir", "Vert alpin", "Argent", "Or", "Graphite", "Bleu alpin"];
        $storages = ["128 Go", "256 Go", "512 Go", "1 To"];
        $products = ["Iphone 11", "Iphone 11 pro max", "Iphone 12", "Iphone 10", "Iphone 12 pro"];
        $entityColors = [];
        $entityStorages = [];

        foreach ($colors  as $color)
        {
            $c = new Color();
            $c->setTitle($color);
            $manager->persist($c);
            $entityColors[] = $c;
        }

        foreach ($storages as $storage)
        {
            $s = new Storage();
            $s->setTitle($storage);
            $manager->persist($s);
            $entityStorages[] = $s;
        }

        $index = 0;
        foreach ($products as $product)
        {
            $p = new Product();
            $p->setMarque("Apple")
                ->setModel($product)
                ->addColor($entityColors[$index])
                ->addStorage($entityStorages[0])
                ->setSize("6.1");

            $manager->persist($p);

            $index++;
        }


        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            if ($i == 0)
            {
                $user->setEmail("test@test.com");
            }
            else
            {
                $user->setEmail($faker->email());
            }
            $password = $this->hasher->hashPassword($user, 'password');
            $user->setPassword($password);

            $manager->persist($user);

            for ($c = 0; $c < 3; $c++)
            {
                $customer = new Customer();
                $customer->setEmail($faker->email())
                        ->setFirstname($faker->firstName())
                        ->setLastname($faker->lastName())
                        ->setPhone($faker->phoneNumber())
                        ->setUser($user)
                ;
                $manager->persist($customer);
            }
        }




        $manager->flush();
    }

}
