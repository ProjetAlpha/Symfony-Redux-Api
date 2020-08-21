<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminFixtures extends Fixture
{
    private $encoder;

    public const ADMIN_USER_REFERENCE = 'admin-test-user';

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setLastname('admin-lastname');
        $userAdmin->setFirstname('admin-firstname');
        $userAdmin->setEmail('admin@mail.com');
        $userAdmin->setIsAdmin(true);
        $userAdmin->setExpireAtToken(time() + 60 * 60);
        $userAdmin->setConfirmationLink(bin2hex(random_bytes(32)));
        $userAdmin->setApiToken(bin2hex(random_bytes(32)));
        $userAdmin->setPassword($this->encoder->encodePassword($userAdmin, "admin"));
        
        $manager->persist($userAdmin);
        $manager->flush();

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::ADMIN_USER_REFERENCE, $userAdmin);
    }
}
