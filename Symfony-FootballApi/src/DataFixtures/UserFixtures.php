<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Favorite;
use App\Entity\User;
use App\Tests\Fixtures\Config;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'firstName' => Config::USER_FIRST_NAME_ONE,
                'lastName' => Config::USER_LAST_NAME_ONE,
                'email' => Config::USER_EMAIL_ONE,
                'password' => Config::USER_PASSWORD_ONE,
            ],
            [
                'firstName' => Config::USER_FIRST_NAME_TWO,
                'lastName' => Config::USER_LAST_NAME_TWO,
                'email' => Config::USER_EMAIL_TWO,
                'password' => Config::USER_PASSWORD_TWO,
            ],
            [
                'firstName' => Config::USER_FIRST_NAME_THREE,
                'lastName' => Config::USER_LAST_NAME_THREE,
                'email' => Config::USER_EMAIL_THREE,
                'password' => Config::USER_PASSWORD_THREE,
            ],
        ];
        $userEntities = [];

        foreach ($users as $userData) {
            $user = new User();
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setEmail($userData['email']);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $userEntities[] = $user;
        }

        $firstUserFavorites = [
            [
                'favoritePosition' => Config::FAVORITE_POSITION_ONE,
                'teamName' => Config::FAVORITE_TEAM_NAME_ONE,
                'teamCrest' => Config::FAVORITE_TEAM_CREST_ONE,
                'teamId' => Config::FAVORITE_TEAM_ID_ONE,
            ],
            [
                'favoritePosition' => Config::FAVORITE_POSITION_TWO,
                'teamName' => Config::FAVORITE_TEAM_NAME_TWO,
                'teamCrest' => Config::FAVORITE_TEAM_CREST_TWO,
                'teamId' => Config::FAVORITE_TEAM_ID_TWO,
            ],
            [
                'favoritePosition' => Config::FAVORITE_POSITION_THREE,
                'teamName' => Config::FAVORITE_TEAM_NAME_THREE,
                'teamCrest' => Config::FAVORITE_TEAM_CREST_THREE,
                'teamId' => Config::FAVORITE_TEAM_ID_THREE,
            ],
        ];

        foreach ($firstUserFavorites as $favoriteData) {
            $favorite = new Favorite();
            $favorite->setUser($userEntities[0]);
            $favorite->setFavoritePosition($favoriteData['favoritePosition']);
            $favorite->setTeamName($favoriteData['teamName']);
            $favorite->setTeamCrest($favoriteData['teamCrest']);
            $favorite->setTeamId($favoriteData['teamId']);
            $manager->persist($favorite);
        }
        $manager->flush();
    }
}
