<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use AppBundle\Entity\UserTrain;
use AppBundle\Entity\UserTrainer;
use AppBundle\Entity\UserTrainerSet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class UserTrainsFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array(
            UsersFixtures::class
        );
    }


    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        /** @var User $user */
        $user = $this->getReference(UsersFixtures::USER_REFERENCE);

        $dates = $this->getDates();
        foreach ($dates as $date) {

            $train = new UserTrain();
            $train->setId(Uuid::uuid4());
            $train->setUser($user);
            $train->setCreateTime($date);
            $manager->persist($train);

            $trainers = $this->getRandomTrainers(rand(5, 7));
            foreach ($trainers as $trainerTitle) {

                $trainer = new UserTrainer();
                $trainer->setId(Uuid::uuid4());
                $trainer->setUser($user);
                $trainer->setTrain($train);
                $trainer->setTitle($trainerTitle);
                $trainer->setCreateTime(++$date);
                $manager->persist($trainer);

                $sets = $this->getSets(rand(3, 4));
                foreach ($sets as $set) {
                    $trainerSet = new UserTrainerSet($trainer, $set['num'], $set['reps'], $set['weight']);
                    $manager->persist($trainerSet);
                }
            }

        }

        $manager->flush();
    }

    private function getDates()
    {
        // генерируем 30 тренировок начиная со вчерашнего дня и через день назад
        $dates = [];
        $time = time();
        for ($i = 1; $i <= 30; $i++) {
            $dates[] = $time - 86400 * ($i * 2);
        }
        return $dates;
    }

    private function getRandomTrainers($count)
    {
        $trainers = $this->getTrainers();
        $keys = array_rand($trainers, $count);
        return array_filter(
            $trainers,
            function ($key) use ($keys) {
                return in_array($key, $keys);
            },
            ARRAY_FILTER_USE_KEY);
    }

    private function getTrainers()
    {
        $data = [
            "Жим штанги лежа",
            "Армейский жим",
            "Подъем гантели из-за головы",
            "Пуловер",
            "Бабочка",
            "Гиперэкстензия",
            "Разгибание рук на блоке",
            "Сгибание рук на блоке",
            "Приседание",
            "Тяга верхнего блока",
            "Тяга нижнего блока",
            "Сведение гантелей к груди",
            "Хаммер грудь",
            "Хаммер спина",
            "Разгибание ног сидя",
            "Бицепс с EZ штангой"
        ];
        return $data;
    }

    private function getSets($count)
    {
        $sets = [];
        $weight = $this->getRandomWeight();
        $reps = $this->getRandomReps();
        for ($i = 0; $i < $count; $i++) {
            $weight = round($weight + ($i * $weight * 0.1));
            $sets[] = ["num" => $i, "weight" => $weight, "reps" => $reps];
        }
        return $sets;
    }

    private function getRandomWeight()
    {
        $items = [10, 12, 15, 20, 25, 30, 35, 40, 50];
        $key = array_rand($items, 1);
        return $items[$key];
    }

    private function getRandomReps()
    {
        $items = [8, 10, 12, 15];
        $key = array_rand($items, 1);
        return $items[$key];
    }
}
