<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrainerRepository")
 * @ORM\Table(name="trainer")
 */
class Trainer
{
    const SORTABLE_FIELDS = ['id', 'title', 'category_id'];
    const SEARCHABLE_FIELDS = ['title', 'description'];

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"trainer_list","trainer_view"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Length(max="255")
     * @Serializer\Groups({"trainer_list","trainer_view"})
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=4000, nullable=false)
     * @Assert\Length(max="4000")
     * @Serializer\Groups({"trainer_view"})
     */
    private $description;

    /**
     * @var TrainerCategory
     * @ORM\ManyToOne(targetEntity="TrainerCategory", inversedBy="trainers")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $category;


    /**
     * @ORM\ManyToMany(targetEntity="Muscle")
     * @ORM\JoinTable(
     *     name="trainers_muscles",
     *     joinColumns={@ORM\JoinColumn(name="trainer_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="muscle_id", referencedColumnName="id", onDelete="CASCADE")})
     */
    private $muscles;


    public function __construct()
    {
        $this->muscles = new ArrayCollection();
        $this->description = '';
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        if (!is_null($description)) {
            $this->description = $description;
        } else {
            $this->description = '';
        }
    }

    public function getMuscles(): Collection
    {
        return $this->muscles;
    }

    public function addMuscle(Muscle $muscle)
    {
        $this->muscles->add($muscle);
    }

    public function removeMuscle(Muscle $muscle)
    {
        $this->muscles->removeElement($muscle);
    }

    public function getCategory(): ?TrainerCategory
    {
        return $this->category;
    }

    public function setCategory(?TrainerCategory $category)
    {
        $this->category = $category;
    }

    public function __toString()
    {
        return (string)$this->getTitle();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

}