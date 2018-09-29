<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="user_train",
 *     indexes={@ORM\Index(name="ut_create_time_idx", columns={"user_id","create_time"})}
 *     )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserTrainRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class UserTrain
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @Assert\NotBlank()
     * @Assert\Uuid()
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Serializer\Expose()
     * @Serializer\Groups({"train_create"})
     */
    private $createTime;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="UserTrainer", mappedBy="train", cascade={"remove"})
     * @ORM\OrderBy({"createTime" = "ASC"})
     * @Serializer\Expose()
     */
    private $trainers;


    public function __construct()
    {
        $this->trainers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getCreateTime(): int
    {
        return $this->createTime;
    }

    /**
     * @param int $createTime
     */
    public function setCreateTime(int $createTime): void
    {
        $this->createTime = $createTime;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }


    /**
     * @return Collection
     */
    public function getTrainers()
    {
        return $this->trainers;
    }


}