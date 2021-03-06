<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** 
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $adminRole = new Role();

        $adminRole->setTitle('ROLE_ADMIN');

        $manager->persist($adminRole);

        $adminUser = new User();

        $adminUser->setFirstName('Damien')
            ->setLastName('Aubry')
            ->setEmail('damien@symfony.com')
            ->setHash($this->encoder->encodePassword($adminUser, 'password'))
            ->setProfilPicture('xd')
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
            ->addUserRole($adminRole);

        $manager->persist($adminUser);

        //Création des utilisateurs
        $users = [];

        $genres = ['male', 'female'];

        for ($k = 1; $k <= 10; $k++) {
            $user = new User();

            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname)
                ->setLastName($faker->lastname)
                ->setEmail($faker->email)
                ->setIntroduction($faker->email)
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setHash($hash)
                ->setProfilPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        //Création de mes 50 annonces
        for ($i = 1; $i <= 50; $i++) {
            $ad = new Ad();

            $ad->setTitle($faker->sentence())
                ->setPrice($faker->numberBetween($min = 100, $max = 1000))
                ->setContent('<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>')
                ->setCoverImage($faker->imageUrl(1000, 400))
                ->setUpdatedAt($faker->dateTime($max = 'now', $timezone = null))
                ->setRooms($faker->numberBetween($min = 1, $max = 8))
                ->setAuthor($user);

            $user = $users[mt_rand(0, count($users) - 1)];

            //Création de mes images en fonction de mes annonces
            for ($j = 1; $j < mt_rand(2, 5); $j++) {
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);

                $manager->persist($image);
            }
            //Gestion des réservations
            for ($l = 1; $l <= mt_rand(0, 10); $l++) {
                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');

                $duration = mt_rand(3, 10);

                //On clone la startdate avoir la même valeur mais sous une var différente
                $endDate = (clone $startDate)->modify("+$duration days");
                $amount = $ad->getPrice() * $duration;

                //récup un user au hasard
                $booker = $users[mt_rand(0, count($users) - 1)];

                $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setCreatedAt($createdAt)
                    ->setAmount($amount)
                    ->setComment($faker->paragraph());

                $manager->persist($booking);

                //Gestion des commentaires
                if(mt_rand(0, 1))
                {
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph())
                            ->setRating(mt_rand(1, 5))
                            ->setAuthor($booker)
                            ->setAd($ad);

                    $manager->persist($comment);
                }
            }

            $manager->persist($ad);
        }
        $manager->flush();
    }
}
