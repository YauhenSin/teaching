<?php

namespace Core\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as AT;

/**
 * @ORM\Entity
 * @ORM\Table(name="groups")
 * @ORM\HasLifecycleCallbacks()
 * @AT\Name("group")
 */
class Group
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
     * @ORM\Column(name="title", type="string", length=255)
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name="weekday", type="string", length=255)
     * @AT\Type("Select")
     * @AT\Filter({"name":"StringTrim", "name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"250"}})
     * @AT\Options({"disable_inarray_validator":true})
     * @AT\Required(false)
     */
    protected $weekday;

    /**
     * @var DateTime
     * @ORM\Column(name="date_start", type="datetime", nullable=true)
     * @AT\Validator({"name":"Date", "options":{"format":"Y-m-d"}})
     * @AT\Required(false)
     */
    protected $dateStart;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Core\Entity\User", inversedBy="groups")
     * @ORM\JoinColumn(name="teacher_id", referencedColumnName="id", nullable=true)
     * @AT\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @AT\Options({"target_class":"Core\Entity\User"})
     */
    protected $teacher;

    /**
     * @var User []
     * @ORM\OneToMany(targetEntity="Core\Entity\User", mappedBy="group")
     * @AT\Exclude
     **/
    protected $students;

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
        $this->students = new ArrayCollection();
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Group
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return Group
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Group
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
     * @return Group
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
     * Set teacher
     *
     * @param \Core\Entity\User $teacher
     *
     * @return Group
     */
    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Get teacher
     *
     * @return \Core\Entity\User
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Add student
     *
     * @param \Core\Entity\User $student
     *
     * @return Group
     */
    public function addStudent($student)
    {
        $this->students[] = $student;

        return $this;
    }

    /**
     * Remove student
     *
     * @param \Core\Entity\User $student
     */
    public function removeStudent($student)
    {
        $this->students->removeElement($student);
    }

    /**
     * Get students
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * Set weekday
     *
     * @param string $weekday
     *
     * @return Group
     */
    public function setWeekday($weekday)
    {
        $this->weekday = $weekday;

        return $this;
    }

    /**
     * Get weekday
     *
     * @return string
     */
    public function getWeekday()
    {
        return $this->weekday;
    }
}
