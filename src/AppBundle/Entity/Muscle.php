<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MuscleRepository")
 * @ORM\Table(name="muscle")
 * @UniqueEntity(fields="title", message="Такая мышца уже существует.")
 * @Serializer\ExclusionPolicy("all")
 */
class Muscle
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
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min="2",max="255")
     * @Serializer\Expose()
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=4000, nullable=false)
     * @Assert\Length(min="10",max="4000")
     * @Serializer\Expose()
     */
    private $description;


    /**
     * @var Muscle
     * @ORM\ManyToOne(targetEntity="Muscle", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * @Serializer\Expose()
     * @Serializer\MaxDepth(1)
     */
    private $parent;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Muscle", mappedBy="parent",  cascade={"remove"})
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->description = '';
    }

    public function getParent(): ?Muscle
    {
        return $this->parent;
    }

    public function setParent(Muscle $parent): void
    {
        $this->parent = $parent;
    }

    public function getChildren(): Collection
    {
        return $this->children;
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