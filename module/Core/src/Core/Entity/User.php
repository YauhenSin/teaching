<?php

namespace Core\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use ZfcUser\Entity\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as AT;
use Core\Service\Entity\User as UserService;

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
     * @ORM\Column(name="middle_name", type="string", length=255, nullable=true)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     */
    protected $middleName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", length=255)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     */
    protected $lastName;

    /**
     * @var string
     * @ORM\Column(name="contact_name", type="string", length=255, nullable=true)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     */
    protected $contactName;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=128)
     * @AT\Type("Zend\Form\Element\Password")
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"min":"6", "max":"128"}})
     */
    protected $password;

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
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(name="display_name", type="string", length=255, nullable=true)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     */
    protected $displayName;

    /**
     * For student
     * @var Group
     * @ORM\ManyToOne(targetEntity="Core\Entity\Group", inversedBy="students")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=true)
     * @AT\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @AT\Options({"target_class":"Core\Entity\Group", "property":"title", "optgroup_identifier":"teacherName"})
     **/
    protected $group;

    /**
     * For teacher
     * @var Group []
     * @ORM\OneToMany(targetEntity="Core\Entity\Group", mappedBy="teacher")
     * @AT\Exclude
     **/
    protected $groups;

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
     */
    protected $phone;

    /**
     * @var string
     * @ORM\Column(name="additional_phone", type="string", length=255, nullable=true)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     * @AT\Required(false)
     */
    protected $additionalPhone;

    /**
     * @var DateTime
     * @ORM\Column(name="date_of_birth", type="datetime", nullable=true)
     * @AT\Type("Date")
     * @AT\Options({"format":"Y-m-d"})
     * @AT\Validator({"name":"Date", "options":{"format":"Y-m-d"}})
     * @AT\Required(false)
     */
    protected $dateOfBirth;

    /**
     * @var float
     * @ORM\Column(name="education_price", type="float", nullable=true)
     * @AT\Validator({"name":"Float"})
     */
    protected $educationPrice;

    /**
     * @var string
     * @ORM\Column(name="education_price_note", type="string", length=255, nullable=true)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     * @AT\Required(false)
     */
    protected $educationPriceNote;

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
        $this->groups = new ArrayCollection();
        $this->roles = new ArrayCollection();
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
     * Set group
     *
     * @param \Core\Entity\Group $group
     *
     * @return User
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Core\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add group
     *
     * @param \Core\Entity\Group $group
     *
     * @return User
     */
    public function addGroup($group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param \Core\Entity\Group $group
     */
    public function removeGroup($group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     *
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set contactName
     *
     * @param string $contactName
     *
     * @return User
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get contactName
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set additionalPhone
     *
     * @param string $additionalPhone
     *
     * @return User
     */
    public function setAdditionalPhone($additionalPhone)
    {
        $this->additionalPhone = $additionalPhone;

        return $this;
    }

    /**
     * Get additionalPhone
     *
     * @return string
     */
    public function getAdditionalPhone()
    {
        return $this->additionalPhone;
    }

    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     *
     * @return User
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set educationPrice
     *
     * @param float $educationPrice
     *
     * @return User
     */
    public function setEducationPrice($educationPrice)
    {
        $this->educationPrice = $educationPrice;

        return $this;
    }

    /**
     * Get educationPrice
     *
     * @return float
     */
    public function getEducationPrice()
    {
        return $this->educationPrice;
    }

    /**
     * Set educationPriceNote
     *
     * @param string $educationPriceNote
     *
     * @return User
     */
    public function setEducationPriceNote($educationPriceNote)
    {
        $this->educationPriceNote = $educationPriceNote;

        return $this;
    }

    /**
     * Get educationPriceNote
     *
     * @return string
     */
    public function getEducationPriceNote()
    {
        return $this->educationPriceNote;
    }

    /**
     * @return \Core\Service\Entity\User
     */
    public function getService()
    {
        return new UserService($this);
    }
}
