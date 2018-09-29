<?php

namespace Tests\AppBundle\Unit\Entity;

use AppBundle\DTO\UserTrainerSet as UserTrainerSetDTO;
use AppBundle\Entity\UserTrainer;
use PHPUnit\Framework\TestCase;


class UserTrainerTest extends TestCase
{
    public function testAddSets()
    {
        $userTrainer = new UserTrainer();

        $setData1 = new UserTrainerSetDTO();
        $setData1->weight = 35;
        $setData1->reps = 15;
        $setData1->num = 1;

        $setData2 = new UserTrainerSetDTO();
        $setData2->weight = 40;
        $setData2->reps = 15;
        $setData2->num = 2;

        $userTrainer->addSet($setData1);
        $userTrainer->addSet($setData2);

        $this->assertEquals(2, $userTrainer->getSets()->count());
    }
}