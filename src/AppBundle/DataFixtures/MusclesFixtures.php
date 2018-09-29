<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Muscle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use function Stringy\create as Str;

class MusclesFixtures extends Fixture
{
    const BICEPS_REFERENCE = 'muscle-biceps';
    const TRICEPS_REFERENCE = 'muscle-triceps';
    const CHEST_REFERENCE = 'muscle-chest';

    public function load(ObjectManager $manager)
    {
        $data = $this->getMuscles();
        foreach ($data as $groupName => $items) {
            $group = new Muscle();
            $group->setTitle($groupName);
            $manager->persist($group);

            $referenceName = $this->getReferenceForName($groupName);
            if ($referenceName) {
                $this->addReference($referenceName, $group);
            }

            foreach ($items as $item) {
                $muscle = new Muscle();
                $muscle->setTitle(Str($item[0])->upperCaseFirst());
                if (isset($item[1])) {
                    $muscle->setDescription(Str($item[1])->upperCaseFirst());
                }
                $muscle->setParent($group);
                $manager->persist($muscle);

                $referenceName = $this->getReferenceForName($item[0]);
                if ($referenceName) {
                    $this->addReference($referenceName, $muscle);
                }
            }
        }
        $manager->flush();
    }

    private function getMuscles()
    {
        $data = [
            "грудные мышцы" => [
                [
                    "большая грудная мышца",
                    "Одна из главных мышц этой группы, приводящая руку к туловищу и вращающая ее"
                ],
                [
                    "передняя зубчатая мышца",
                    "Отвечает за вращение и отведение от позвоночника лопатки, поднимание руки над головой"
                ],
                ["малая грудная мышца", "Находится под большой грудной мышцей, опускает руку к туловищу"],
                ["межреберные мышцы", "Способствуют дыхательным движениям"],
            ],
            "Мышцы спины" => [
                [
                    "широчайшая мышца спины",
                    "Поворачивает руку внутрь, приводит ее к туловищу, наклоняет само туловище, участвует в движениях всего плечевого пояса и придает торсу конусообразную форму"
                ],
                ["трапециевидная мышца", "Обеспечивает подъем, вращение и сближение лопаток, отведение головы назад"],
                ["длинные мышцы", "Проходят вдоль всего позвоночника, наклоняют, разгибают и вращают туловище"],
            ],
            "Плечевой пояс" => [
                ["Передняя дельта", ""],
                ["Средняя дельта", ""],
                ["Задняя дельта", ""],
            ],
            "Пресс" => [
                ["прямые мышц живота", "Наклоняют туловище, подтягивают ноги к груди"],
                ["косые мышцы живота.", "обеспечивают сгибание и повороты туловища"],
            ],
            "Мышцы рук" => [
                [
                    "бицепс",
                    "Сгибает руку в локтевом суставе, обеспечивает отведение и приведение руки при повороте кисти ладонью вверх"
                ],
                ["трицепс", "Разгибает руку в локтевом суставе и отводит ее назад"],
                [
                    "мышцы предплечья",
                    "основная функция — сгибание и разгибание пальцев рук, обеспечивание всех движений пальцев и кисти"
                ],
            ],
            "Мышцы ног" => [
                [
                    "четырехглавая мышца бедра",
                    "Разгибает ногу в коленном суставе, сгибает бедро, поворачивает ногу наружу и вовнутрь"
                ],
                ["большая ягодичная мышца", "С ее помощью происходит разгибание и поворот бедра наружу"],
                [
                    "двуглавая мышца бедра",
                    "Эта мышца сгибает ногу в коленном суставе, вращает наружу, разгибает ее в тазобедренном суставе"
                ],
                ["икроножная мышца", "Обеспечивает движение ноги в голеностопном суставе"],
            ],
        ];
        return $data;
    }

    private function getReferenceForName(string $name): ?string
    {
        if ($name == 'грудные мышцы') {
            return self::CHEST_REFERENCE;
        }
        if ($name == 'бицепс') {
            return self::BICEPS_REFERENCE;
        }
        if ($name == 'трицепс') {
            return self::TRICEPS_REFERENCE;
        }

        return null;
    }
}
