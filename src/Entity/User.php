<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Assert\GroupSequence({"User", "Strict"})
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank()
     */
    private $username;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;
    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;
    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    /**
     * @var DateTime $createdAt
     * @ORM\Column(name="createdAt",type="datetime")
     */
    protected $createdAt;
    /**
     * @var DateTime $updatedAt
     * @ORM\Column(name="updatedAt",type="datetime")
     */
    protected $updatedAt;
    /**
     * @var DateTime $lastLogin
     * @ORM\Column(name="last_login",type="datetime",nullable=true)
     */
    protected $lastLogin;
    /**
     * @var string $locale
     * @ORM\Column(name="locale",type="string", length=100)
     */
    protected $locale;
    /**
     * @ORM\Column(name="first_name",type="string", length=100,nullable=false)
     * @Assert\NotBlank()
     */
    private $firstName;
    /**
     * @ORM\Column(name="last_name",type="string", length=100,nullable=false)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity="Location", mappedBy="user", cascade={"persist"})
     */
    private $location;

    /**
     * @ORM\OneToMany(targetEntity="Travel", mappedBy="user", cascade={"persist"})
     */
    private $travel;

    /**
     * @ORM\ManyToMany(targetEntity="Travel", inversedBy="sharedusers")
     * @ORM\JoinTable(name="travels_shared",
     * joinColumns={@ORM\JoinColumn(name="travel_id", referencedColumnName="id", nullable=false)},
     * inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)}
     * )
     */
    private $travelsshared;

    public function __construct()
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTime;
        $this->createdAt = new \DateTime;
        $this->locale = 'en';
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
        $this->travel = new \Doctrine\Common\Collections\ArrayCollection();
        $this->location = new \Doctrine\Common\Collections\ArrayCollection();
        $this->travelsshared = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
     * @Assert\IsTrue(message = "Password must be diferent than Firstname and Username", groups={"Strict"})
     */
    public function isPasswordCorrect()
    {
        return ($this->firstName !== $this->plainPassword && $this->username !== $this->plainPassword);
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
    public function getPassword()
    {
        return $this->password;
    }
    public function getRoles()
    {
        return array('ROLE_USER');
    }
    public function eraseCredentials()
    {
    }
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set username
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
     * Set password
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
     * Set email
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }
    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    /**
     * Set createdAt
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
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /**
     * Set updatedAt
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
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
        return $this;
    }
    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }
    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return User
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }
    /**
     * Set firstName
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
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    /**
     * Set lastName
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
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }


    /**
     * Add travel
     *
     * @param Travel $travel
     * @return User
     */
    public function addTravel(Travel $travel)
    {
        $this->travel[] = $travel;

        return $this;
    }


    /**
     * Get travel
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTravel()
    {
        return $this->travel;
    }


    /**
     * Remove travel
     *
     * @param \App\Entity\Travel $travel
     */
    public function removeTravel(Travel $travel)
    {
        $this->travel->removeElement($travel);
    }

    /**
     * Add travelsshared
     *
     * @param \App\Entity\Travel $travelsshared
     * @return User
     */
    public function addTravelsshared(Travel $travelsshared)
    {
        $this->travelsshared[] = $travelsshared;

        return $this;
    }

    /**
     * Remove travelsshared
     *
     * @param \App\Entity\Travel $travelsshared
     */
    public function removeTravelsshared(Travel $travelsshared)
    {
        $this->travelsshared->removeElement($travelsshared);
    }

    /**
     * Get travelsshared
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTravelsshared()
    {
        return $this->travelsshared;
    }


    /**
     * Add Location
     *
     * @param Location $location
     * @return User
     */
    public function addLocation(Travel $location)
    {
        $this->location[] = $location;

        return $this;
    }


    /**
     * Get Location
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocation()
    {
        return $this->location;
    }


    /**
     * Remove Location
     *
     * @param \App\Entity\Location $location
     */
    public function removeLocation(Location $location)
    {
        $this->location->removeElement($location);
    }
}
