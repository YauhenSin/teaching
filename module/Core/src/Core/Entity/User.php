<?php

namespace Core\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use ZfcUser\Entity\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as AT;
use Core\Service\User as UserService;

/**
 * @ORM\Entity(repositoryClass="Core\Repository\UserRepository")
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\HasLifecycleCallbacks()
 * @AT\Name("user")
 */
class User implements UserInterface, ProviderInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @AT\Exclude
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     * @AT\Type("Zend\Form\Element\Email")
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     * @AT\Validator({"name":"EmailAddress"})
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", length=255)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     */
    protected $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", length=255)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     */
    protected $lastName;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=128)
     * @AT\Type("Zend\Form\Element\Password")
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     */
    protected $password;

    /**
     * @AT\Type("Zend\Form\Element\Password")
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"Identical", "options":{"token" : "password"}})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     *
     */
    protected $passwordConfirm;

    /**
     * @AT\Type("Zend\Form\Element\Checkbox")
     * @AT\Validator({"name":"NotEmpty", "options":{"type":{"integer", "zero"}}})
     */
    protected $terms;

    /**
     * @var int
     * @ORM\Column(name="state", type="integer")
     * @AT\Exclude
     */
    protected $state;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     * @AT\Required(false)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(name="display_name", type="string", length=255, nullable=true)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     * @AT\Required(false)
     */
    protected $displayName;

    /**
     * @var DateTime
     * @ORM\Column(name="created_at", type="datetime")
     * @AT\Exclude
     */
    protected $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(name="updated_at", type="datetime")
     * @AT\Exclude
     */
    protected $updatedAt;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Core\Entity\Role")
     * @ORM\JoinTable(name="user_role_linker",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $roles;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     * @AT\Required(false)
     */
    protected $phone;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="UserAddress", mappedBy="user")
     */
    protected $addresses;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Product", mappedBy="user")
     */
    protected $products;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @AT\Type("Zend\Form\Element\Email")
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     * @AT\Validator({"name":"EmailAddress"})
     * @AT\Required(false)
     */
    protected $paypalEmail;

    /**
     * @var string
     * @ORM\Column(name="activation_code", type="string", length=250, nullable=true)
     * @AT\Exclude
     */
    protected $activationCode;

    /**
     * Synchronized product with magento
     *
     * @var string
     * @ORM\Column(name="synchronized", type="boolean")
     * @AT\Exclude
     */
    protected $synchronized;

    /**
     * @AT\Type("Zend\Form\Element\Submit")
     */
    public $submit;

    /**
     * Initializes variables
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->roles = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->synchronized = false;
    }

    /**
     * Operations before update
     *
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new DateTime();
    }

    /**
     * Set id.
     *
     * @param int $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email.
     *
     * @param string $email
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
     * Set first name.
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set last name.
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get first + last name.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * Set password.
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set state.
     *
     * @param int $state
     * @return User
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set username.
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     * @return User
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
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
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param DateTime $updatedAt
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
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add a role to the user.
     *
     * @param \Core\Entity\Role $role
     *
     * @return User
     */
    public function addRole($role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->getValues();
    }

    /**
     * Remove role
     *
     * @param \Core\Entity\Role $role
     */
    public function removeRole($role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Add address
     *
     * @param \Core\Entity\UserAddress $address
     *
     * @return User
     */
    public function addAddress($address)
    {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \Core\Entity\UserAddress $address
     */
    public function removeAddress($address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Core\Entity\UserAddress []
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Add product
     *
     * @param \Core\Entity\Product $product
     *
     * @return User
     */
    public function addProduct($product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \Core\Entity\Product $product
     */
    public function removeProduct($product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Core\Entity\Product []
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set activationCode
     *
     * @param string $activationCode
     *
     * @return User
     */
    public function setActivationCode($activationCode)
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    /**
     * Get activationCode
     *
     * @return string
     */
    public function getActivationCode()
    {
        return $this->activationCode;
    }

    /**
     * Set paypalEmail
     *
     * @param string $paypalEmail
     *
     * @return User
     */
    public function setPaypalEmail($paypalEmail)
    {
        $this->paypalEmail = $paypalEmail;

        return $this;
    }

    /**
     * Get paypalEmail
     *
     * @return string
     */
    public function getPaypalEmail()
    {
        return $this->paypalEmail;
    }

    /**
     * Set synchronized
     *
     * @param boolean $synchronized
     * @return self
     */
    public function setSynchronized($synchronized)
    {
        $this->synchronized = $synchronized;

        return $this;
    }

    /**
     * Get synchronized
     *
     * @return boolean
     */
    public function getSynchronized()
    {
        return $this->synchronized;
    }

    /**
     * @return \Core\Service\User
     */
    public function getService()
    {
        return new UserService($this);
    }
}
