<?php

namespace AppBundle\Entity;

use AppBundle\Interfaces\HasAvatar;
use AppBundle\Validator\Constraints as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @UniqueEntity(fields="email", message="Данный адрес уже зарегистрирован.")
 * @Vich\Uploadable
 */
class User implements UserInterface, HasAvatar, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @AppAssert\EmailTrusted()
     */
    private $email;

    /**
     * @ORM\Column(type="boolean", name="has_email_activated")
     */
    private $hasEmailActivated;

    /**
     * @Vich\UploadableField(mapping="avatar", fileNameProperty="avatarName")
     * @Assert\Image(minWidth = 150, minHeight = 150)
     * @var File
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $avatarName;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $lastActivity;

    /**
     * @var string
     * @Assert\Length(min=4,minMessage="Пароль не должен быть короче 4 символов.")
     */
    private $plainPassword;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvatar(): ?File
    {
        return $this->avatar;
    }

    public function setAvatar(?File $file = null): void
    {
        $this->avatar = $file;
        $this->lastActivity = new \DateTime();
    }

    /**
     * @return string
     */
    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    /**
     * @param string $avatarName
     */
    public function setAvatarName(?string $avatarName): void
    {
        $this->avatarName = $avatarName;
    }

    /**
     * @return \DateTime
     */
    public function getLastActivity(): \DateTime
    {
        return $this->lastActivity;
    }


    /**
     * @param \DateTime $lastActivity
     */
    public function setLastActivity(\DateTime $lastActivity): void
    {
        $this->lastActivity = $lastActivity;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        if ($this->getEmail() == 'admin@test.com') {
            return ['ROLE_ADMIN', 'ROLE_USER'];
        }

        return ['ROLE_USER'];
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSalt()
    {

    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }


    public function getHasEmailActivated(): bool
    {
        return $this->hasEmailActivated;
    }

    public function setHasEmailActivated(bool $hasEmailActivated): void
    {
        $this->hasEmailActivated = $hasEmailActivated;
    }


    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function __toString()
    {
        return $this->getEmail();
    }

}
