<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public const USER_REFERENCE = 'user-test';

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setLastname('user-lastname'.$i);
            $user->setFirstname('user-firstname'.$i);
            $user->setEmail('user-'.$i.'@mail.com');
            $user->setIsAdmin(false);
            $user->setExpireAtToken(time() + 60 * 60);
            $user->setConfirmationLink(bin2hex(random_bytes(32)));
            $user->setApiToken(bin2hex(random_bytes(32)));
            $user->setPassword($this->encoder->encodePassword($user, "user"));

            $manager->persist($user);
            $manager->flush();
        }

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        // $this->addReference(self::USER_REFERENCE, $user);
    }
}
