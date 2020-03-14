<?php

namespace App\Domain\User\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\ValueObject\UserId;

class User implements UserInterface
{
    /**
     * @var UserId
     */
    private $id;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $plainPassword;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;
    /**
     * @var \DateTime
     */
    protected $lastLogin;
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;

    /**
     * @var ArrayCollection
     */
    private $travelsshared;

    /**
     * @var string
     */
    private $token;

    public function __construct()
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->locale = 'en';

        $this->travelsshared = new ArrayCollection();

        $this->publishEvent();
    }

    public function publishEvent()
    {
    }

    /**
     * Check user equals.
     *
     * @param User $user
     *
     * @return bool
     */
    public function equalsTo(User $user): bool
    {
        return $this->id->equalsTo($user->id);
    }

    /**
     * Create from Id.
     *
     * @param int $anId
     *
     * @return User
     */
    public static function fromId(int $anId)
    {
        $user = new self();
        $user->id = new UserId($anId);

        return $user;
    }

    /**
     * Create from int Id.
     *
     * @param int $anId
     *
     * @return User
     */
    public static function byId(int $anId)
    {
        $user = new self();
        $user->id = new UserId($anId);

        return $user;
    }

    public function getId()
    {
        return $this->id;
    }

    public function userId()
    {
        if (null === $this->id) {
            $this->id = new UserId($this->id());
        }

        return $this->id;
    }

    /**
     * Check the password correct value, cannot be equal to the username and password.
     *
     * @return bool
     */
    public function isPasswordCorrect(): bool
    {
        return $this->firstName !== $this->plainPassword && $this->username !== $this->plainPassword;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize([
            $this->email,
            $this->username,
            $this->password,
        ]);
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->email,
            $this->username,
            $this->password) = unserialize($serialized);
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set lastLogin.
     *
     * @param \DateTime $lastLogin
     *
     * @return User
     */
    public function setLastLogin($lastLogin): User
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return User
     */
    public function setLocale(string $locale): User
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $password): User
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function addTravelsshared(Travel $travelsshared)
    {
        $this->travelsshared[] = $travelsshared;

        return $this;
    }

    public function removeTravelsshared(Travel $travelsshared)
    {
        $this->travelsshared->removeElement($travelsshared);
    }

    /**
     * Get travelsshared.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTravelsshared()
    {
        return $this->travelsshared;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }
}
