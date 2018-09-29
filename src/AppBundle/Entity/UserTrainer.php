<?php

namespace AppBundle\Entity;

use AppBundle\DTO\UserTrainerSet as UserTrainerSetDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(
 *     name="user_trainer",
 *     indexes={@ORM\Index(name="utr_create_time_idx", columns={"train_id","create_time"})}
 *     )
 * @Serializer\ExclusionPolicy("all")
 */
class UserTrainer
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
     */
    private $createTime;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Length(min="2", max="255")
     * @Assert\NotBlank()
     * @Serializer\Expose()
     */
    private $title;

    /**
     * @var Collection
     * @ORM\OneToMany(
     *     targetEntity="UserTrainerSet",
     *     mappedBy="trainer",
     *     cascade={"persist","remove"},
     *     orphanRemoval=true
     * )
     * @SWG\Property(type="array", @SWG\Items(ref=@Model(type=UserTrainerSetDTO::class)))
     * @Serializer\Expose()
     */
    private $sets;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var UserTrain
     * @ORM\ManyToOne(targetEntity="UserTrain", inversedBy="trainers")
     * @ORM\JoinColumn(name="train_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $train;


    /**
     * UserTrainer constructor.
     */
    public function __construct()
    {
        $this->sets = new ArrayCollection();
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
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return UserTrain
     */
    public function getTrain(): UserTrain
    {
        return $this->train;
    }

    /**
     * @param UserTrain $train
     */
    public function setTrain(UserTrain $train): void
    {
        $this->train = $train;
    }


    /**
     * @return int
     */
    public function getCreateTime(): ?int
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
    public function getSets(): Collection
    {
        return $this->sets;
    }

    /**
     * @param Collection $sets
     */
    public function setSets(Collection $sets): void
    {
        $this->sets = $sets;
    }

    /**
     * addSet and removeSet are used by Symfony Forms when de-serializing JSON through the form
     * @param UserTrainerSetDTO $setData
     */
    public function addSet(UserTrainerSetDTO $setData): void
    {
        $set = new UserTrainerSet($this, $setData->num, $setData->reps, $setData->weight);
        $this->getSets()->add($set);
    }


    public function removeSet(UserTrainerSetDTO $setData): void
    {
        // реализация этого метода не требуется,
        // его сигнатура просто нужна Symfony Forms для корректной работы с полем типа CollectionType
    }

}