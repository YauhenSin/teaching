<?php

namespace Core\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as AT;

/**
 * @ORM\Entity
 * @ORM\Table(name="homeworks")
 * @ORM\HasLifecycleCallbacks()
 * @AT\Name("homework")
 */
class Homework
{
    const STATE_ACTIVE = 1;
    const STATE_PAST = 2;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @AT\Exclude
     */
    protected $id;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="Core\Entity\Group", inversedBy="homeworks")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     **/
    protected $group;

    /**
     * @var string
     * @ORM\Column(name="content", type="text")
     * @AT\Type("Zend\Form\Element\Textarea")
     * @AT\Filter({"name":"StringTrim"})
     * @AT\Filter({"name":"StripTags"})
     * @AT\Validator({"name":"StringLength", "options":{"max":"65000"}})
     */
    protected $content;

    /**
     * @var int
     * @ORM\Column(name="state", type="integer")
     * @AT\Exclude
     */
    protected $state;

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
     * Set content
     *
     * @param string $content
     *
     * @return Homework
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Homework
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Homework
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
     * @return Homework
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
     * Set group
     *
     * @param \Core\Entity\Group $group
     *
     * @return Homework
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
}
