<?php

namespace App\DataFixtures;

use App\Entity\AllCompetition;
use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
        private const ALLCOMPETITIONS = [
            [
                'title' => 'Maraton Warszawski',
                'content' => '41.  PZU Maratonie Warszawskim masz okazję aktywnie wesprzeć działanie jednej z dziesięciu organizacji dobroczynnych. Podejmij wyzwanie! Pomagaj, robiąc to, co lubisz najbardziej – biegając.',
                'date' => '2020-09-27',
                'fee' => '200'
                ],
            [
                'title' => 'Półmaraton Warszawski',
                'content' => '41.  PZU Półmaratonie Warszawskim masz okazję aktywnie wesprzeć działanie jednej z dziesięciu organizacji dobroczynnych. Podejmij wyzwanie! Pomagaj, robiąc to, co lubisz najbardziej – biegając.',
                'date' => '2020-05-23',
                'fee' => '150'
            ],
            [
                'title' => 'Bieg Powstania Warszawskiego',
                'content' => 'Jaka jest cena siedzącego trybu życia? Wspaniała atmosfera. Nowy rekord Biegu Powstania Warszawskiego. 29 lipca 2019' ,
                'date' => '2020-07-29',
                'fee' => '120'
            ],
        ];

        private const USERS = [
            [
                'username'=> 'admin',
                'firstName' => 'admin',
                'lastName' => 'admin',
                'password' => 'admin123',
                'passwordAgain'=> 'admin123',
                'gender' => 'male',
                'dateOfBirth' => '1991-01-01',
                'city' => 'Warszawa', 
                'country' => 'PL',
                'nationality' => 'PL',
                'phone' => '111111111',
                'email' => 'admin@admin.pl',
                'roles' => [User::ROLE_SUPERADMIN],
                'enabled' => true

            ],
            [
                'username'=> 'karolkle',
                'firstName' => 'Karol',
                'lastName' => 'Kle',
                'password' => 'karol123',
                'passwordAgain'=> 'karol123',
                'gender' => 'male',
                'dateOfBirth' => '1991-01-01',
                'city' => 'Warszawa',
                'country' => 'PL',
                'nationality' => 'PL',
                'phone' => '111111112',
                'email' => 'karolkle@kar.pl',
                'roles' => [User::ROLE_USER],
                'enabled' => true

            ],
    ];
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(
            UserPasswordEncoderInterface $passwordEncoder,
             TokenGenerator $tokenGenerator)
        {
            $this->passwordEncoder = $passwordEncoder;
            $this->tokenGenerator = $tokenGenerator;
        }

        public function load(ObjectManager $manager){
        $this->loadUsers($manager);
        $this->loadCompetitions($manager);

        }

        public function loadUsers(ObjectManager $manager){
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setFirstName($userFixture['firstName']);
            $user->setLastName($userFixture['lastName']);
            $user->setEmail($userFixture['email']);
            $user->setGender($userFixture['gender']);
            $user->setCity($userFixture['city']);
            $user->setCountry($userFixture['country']);
            $user->setNationality($userFixture['nationality']);
            $user->setDateOfBirth($userFixture['dateOfBirth']);
            $user->setPhone($userFixture['phone']);
            $user->setRoles($userFixture['roles']);
            $user->setEnabled($userFixture['enabled']);
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $userFixture['password']
                )
            );
            if (!$userFixture['enabled']) {
                $user->setConfirmationToken(
                    $this->tokenGenerator->getRandomSecureToken()
                );
            }
            $this->addReference('user_' .$userFixture['username'], $user);
            $manager->persist($user);
        }
        $manager->flush();
        }

    public function loadCompetitions(ObjectManager $manager){
        foreach (self::ALLCOMPETITIONS as $competitionFixture) {
            $competition = new allCompetition();
            $competition->setTitle($competitionFixture['title']);
            $competition->setcontent($competitionFixture['content']);
            $competition->setDate($competitionFixture['date']);
            $competition->setFee($competitionFixture['fee']);
            $this->addReference('title_' .$competitionFixture['title'], $competition);
            $manager->persist($competition);
        }
        $manager->flush();
    }



}
