<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrainerCategoryRepository")
 * @ORM\Table(name="trainer_category")
 * @UniqueEntity(fields="title", message="Такая категория уже существует.")
 * @Serializer\ExclusionPolicy("all")
 */
class TrainerCategory
{
    const SORTABLE_FIELDS = ['id', 'title'];

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     * @Assert\Length(min="10", max="255")
     * @Assert\NotBlank()
     * @Serializer\Expose()
     */
    private $title;


    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Trainer", mappedBy="category", cascade={"remove"})
     */
    private $trainers;


    public function __construct()
    {
        $this->trainers = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string)$this->getTitle();
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
     * @return Collection
     */
    public function getTrainers()
    {
        return $this->trainers;
    }

    /**
     * @return array
     */
    public function getSortableFields()
    {
        return ['id', 'title'];
    }
}