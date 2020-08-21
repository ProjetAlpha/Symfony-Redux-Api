<?php

namespace App\DataFixtures;

use App\Tests\FileManagement\TestImage;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixtures extends Fixture implements ContainerAwareInterface, DependentFixtureInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    public const IMAGE_REFERENCE = 'article-image';

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $image = new Image();
            $info = new TestImage($this->container->getParameter('image_test').'Bob-Marley.jpg');
            
            // create image
            $name = $info->getName().$i.'.'.$info->getExtension();
            $target = dirname($info->getPath()).'/'.$name;
            
            if (file_exists($target)) {
                unlink($target);
            }

            file_put_contents($target, file_get_contents($info->getPath()));

            $image->setUserId($this->getReference(AdminFixtures::ADMIN_USER_REFERENCE));
            $this->addReference(self::IMAGE_REFERENCE.$i, $image);
            $image->setPath($target);
            $image->setName($info->getName());

            $manager->persist($image);
            $manager->flush();

            $images[] = $image;
        }
    }

    public function getDependencies()
    {
        return [
            AdminFixtures::class
        ];
    }
}
