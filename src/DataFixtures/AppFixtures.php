<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $categories;

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private SluggerInterface $slugger,
        CategoryRepository $categories
    ) {
        $this->categories = $categories;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUser(
            $manager,
            'admin@users.com',
            '123456789',
            ['ROLE_ADMIN'],
            'Admin',
            'Adminovich',
            '+123456789',
            '00000',
            'City No.0',
            'Street No.0',
            'Build No.0'
        );

        for ($i = 1; $i < 6; $i++) {
            $this->createUser(
                $manager,
                $i . 'user@users.com',
                '123456789',
                [],
                $i . 'User',
                $i . 'Userovich',
                $i . '+123456789',
                $i . '0000',
                'City No.' . $i,
                'Street No.' . $i,
                'Build No.' . $i
            );
        }

        $this->createCategory(
            $manager,
            'Plantes d\'intérieur',
            'section-plantes-interieur.jpeg',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
            Donec eget lobortis orci. Nullam et nibh lacus. Nulla dui metus, 
            pharetra euismod luctus vitae, euismod egestas sem. 
            Sed commodo massa quam, quis hendrerit ex imperdiet nec.'
        );
        $this->createCategory(
            $manager,
            'Plantes d\'extérieur',
            'section-plantes-exterieur.jpeg',
            'Nunc molestie nisl eu hendrerit sagittis. 
            Nullam quis sapien eleifend, malesuada massa et, ullamcorper sem. 
            Phasellus accumsan ut felis ut fermentum. Sed ligula risus, ultrices sit amet lobortis ut, aliquet sit amet risus. 
            Vestibulum quis magna at ante aliquet porta at ut arcu.'
        );

        $manager->flush();

        for ($i = 1; $i < 6; $i++) {
            $this->createProduct(
                $manager,
                $this->categories->findOneByName('Plantes d\'intérieur'),
                $i . 'title',
                $i . 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed magna mi, molestie et iaculis vitae, eleifend sed ante. Nunc nec gravida neque. Phasellus rhoncus malesuada enim quis congue. Phasellus viverra ligula tincidunt erat rutrum, sit amet malesuada tellus ultrices. Maecenas nibh elit, volutpat eget ipsum id, eleifend interdum turpis. Suspendisse vulputate consequat felis, eu porttitor sapien bibendum at. Etiam faucibus diam in diam finibus, a semper magna ultrices.',
                $i . 'image-interieur.jpg',
                $i . 'commonName',
                $i . 'genre',
                ($i + 100) . 'cm',
                $i . 'foliage',
                $i . 'Sed in ultricies erat. Etiam accumsan sodales dolor vitae facilisis. Aliquam erat volutpat. Nulla nulla arcu, lacinia a porta at, hendrerit at nunc. Aenean mattis, arcu eu malesuada dapibus, felis enim mattis magna, molestie auctor magna odio eget augue. Donec et volutpat enim, vitae elementum tortor. Mauris convallis sed turpis ut varius.',
                $i . 'bloom',
                false,
                $i * 5,
                $i + 3,
                false,
                0,
            );
        };

        for ($i = 1; $i < 6; $i++) {
            $this->createProduct(
                $manager,
                $this->categories->findOneByName('Plantes d\'extérieur'),
                $i . 'title',
                $i . 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed magna mi, molestie et iaculis vitae, eleifend sed ante. Nunc nec gravida neque. Phasellus rhoncus malesuada enim quis congue. Phasellus viverra ligula tincidunt erat rutrum, sit amet malesuada tellus ultrices. Maecenas nibh elit, volutpat eget ipsum id, eleifend interdum turpis. Suspendisse vulputate consequat felis, eu porttitor sapien bibendum at. Etiam faucibus diam in diam finibus, a semper magna ultrices.',
                $i . 'image-exterieur.jpg',
                $i . 'commonName',
                $i . 'genre',
                ($i + 100) . 'cm',
                $i . 'foliage',
                $i . 'Sed in ultricies erat. Etiam accumsan sodales dolor vitae facilisis. Aliquam erat volutpat. Nulla nulla arcu, lacinia a porta at, hendrerit at nunc. Aenean mattis, arcu eu malesuada dapibus, felis enim mattis magna, molestie auctor magna odio eget augue. Donec et volutpat enim, vitae elementum tortor. Mauris convallis sed turpis ut varius.',
                $i . 'bloom',
                true,
                $i * 5,
                $i + 3,
                true,
                -10,
            );
        };

        $manager->flush();
    }

    private function createCategory(
        ObjectManager $manager,
        $name,
        $image,
        $description
    ) {
        $category = new Category;
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setImage($image);
        $category->setDescription($description);
        $manager->persist($category);
    }

    private function createUser(
        ObjectManager $manager,
        $email,
        $password,
        array $role,
        $name,
        $surname,
        $telMob,
        $codeZIP,
        $city,
        $street,
        $buildNum
    ) {
        $user = new User;
        $user->setEmail($email);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setRoles($role);
        $user->setName($name);
        $user->setSurname($surname);
        $user->setMobTel($telMob);
        $user->setCodeZIP($codeZIP);
        $user->setCity($city);
        $user->setStreet($street);
        $user->setBuildNum($buildNum);

        $manager->persist($user);
    }

    private function createProduct(
        ObjectManager $manager,
        $category,
        $title,
        $description,
        $image,
        $commonName,
        $genre,
        $size,
        $foliage,
        $watering,
        $bloom,
        bool $isFragrantBloom,
        float $price,
        int $stockQnty,
        bool $isResistedCold,
        $coldResistance
    ) {
        $product = new Product;
        $product->setCategory($category);
        $product->setTitle($title);
        $product->setDescription($description);
        $product->setImage($image);
        $product->setCommonName($commonName);
        $product->setGenre($genre);
        $product->setSize($size);
        $product->setFoliage($foliage);
        $product->setWatering($watering);
        $product->setBloom($bloom);
        $product->setIsFragrantBloom($isFragrantBloom);
        $product->setPrice($price);
        $product->setinStockQnty($stockQnty);
        $product->setIsResistedToCold($isResistedCold);
        $product->setColdResistance($coldResistance);
        $product->setSlug($this->slugger->slug($product->getTitle())->lower());

        $manager->persist($product);
    }
}
