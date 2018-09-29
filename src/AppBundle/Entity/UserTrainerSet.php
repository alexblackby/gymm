<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="user_trainer_set")
 * @Serializer\ExclusionPolicy("all")
 */
class UserTrainerSet
{
    /**
     * @var UserTrainer
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="UserTrainer", inversedBy="sets")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $trainer;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @SWG\Property(type="integer", description="Порядковый номер подхода, начиная с нуля")
     * @Serializer\Expose()
     */
    private $num;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min=1, max=100)
     * @SWG\Property(type="integer", minimum=1, maximum=100, description="Число повторений упражнения")
     * @Serializer\Expose()
     */
    private $reps;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min=0, max=150)
     * @SWG\Property(type="integer", minimum=0, maximum=150, description="Дополнительный вес при выполнении упражнения, кг")
     * @Serializer\Expose()
     */
    private $weight;


    /**
     * UserTrainerSet constructor.
     * @param UserTrainer $trainer
     * @param $num
     * @param int $reps
     * @param int $weight
     */
    public function __construct(UserTrainer $trainer, int $num, int $reps, int $weight = 0)
    {
        $this->trainer = $trainer;
        $this->num = $num;
        $this->reps = $reps;
        $this->weight = $weight;
    }


    /**
     * @return UserTrainer
     */
    public function getTrainer(): UserTrainer
    {
        return $this->trainer;
    }

    /**
     * @return mixed
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @return int
     */
    public function getReps(): int
    {
        return $this->reps;
    }

}