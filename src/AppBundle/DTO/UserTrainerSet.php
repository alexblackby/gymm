<?php

namespace AppBundle\DTO;

use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;


class UserTrainerSet
{
    /**
     * @var int
     * @Assert\NotBlank()
     * @SWG\Property(type="integer", description="Порядковый номер подхода, начиная с нуля")
     */
    public $num;

    /**
     * @var int
     * @Assert\NotBlank()
     * @SWG\Property(type="integer", minimum=1, maximum=100, description="Число повторений упражнения")
     */
    public $reps;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=0, max=150)
     * @SWG\Property(type="integer", minimum=0, maximum=150, description="Дополнительный вес при выполнении упражнения, кг")
     */
    public $weight;
}