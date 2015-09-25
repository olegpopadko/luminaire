<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Issue
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={
 *      "code", "project_id"
 *  })})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\IssueRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity({"code", "project"})
 */
class Issue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \AppBundle\Entity\IssueStatus
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IssueStatus")
     * @ORM\JoinColumn(name="issue_status_id", referencedColumnName="id", nullable = false)
     * @Assert\NotBlank()
     **/
    private $status;

    /**
     * @var \AppBundle\Entity\IssuePriority
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IssuePriority")
     * @ORM\JoinColumn(name="issue_priority_id", referencedColumnName="id", nullable = false)
     * @Assert\NotBlank()
     **/
    private $priority;

    /**
     * @var \AppBundle\Entity\IssueResolution
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IssueResolution")
     * @ORM\JoinColumn(name="issue_resolution_id", referencedColumnName="id")
     **/
    private $resolution;

    /**
     * @var \AppBundle\Entity\IssueType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IssueType")
     * @ORM\JoinColumn(name="issue_type_id", referencedColumnName="id", nullable = false)
     * @Assert\NotBlank()
     **/
    private $type;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id", nullable = false)
     * @Assert\NotBlank()
     **/
    private $reporter;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id")
     **/
    private $assignee;

    /**
     * @var \AppBundle\Entity\Project
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable = false)
     * @Assert\NotBlank()
     **/
    private $project;

    /**
     * @var \AppBundle\Entity\Issue
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Issue", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Issue", mappedBy="parent")
     **/
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="projects")
     * @ORM\JoinTable(name="user_to_issue")
     */
    private $collaborators;

    /**
     *
     */
    public function __construct()
    {
        $this->children      = new \Doctrine\Common\Collections\ArrayCollection();
        $this->collaborators = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getProject()->getCode() . '-' . $this->getCode(). ' ' . $this->getSummary();
    }

    /**
     * @ORM\PrePersist
     */
    public function initDateTimeFieldOnPrePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = clone $this->createdAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateUpdateAtOnPreUpdate()
    {
        $this->updatedAt = new \DateTime();
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
     * Set summary
     *
     * @param string $summary
     *
     * @return Issue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Issue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Issue
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
     * @return Issue
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
     * Set status
     *
     * @param \AppBundle\Entity\IssueStatus $status
     *
     * @return Issue
     */
    public function setStatus(\AppBundle\Entity\IssueStatus $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \AppBundle\Entity\IssueStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set resolution
     *
     * @param \AppBundle\Entity\IssueResolution $resolution
     *
     * @return Issue
     */
    public function setResolution(\AppBundle\Entity\IssueResolution $resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get resolution
     *
     * @return \AppBundle\Entity\IssueResolution
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\IssueType $type
     *
     * @return Issue
     */
    public function setType(\AppBundle\Entity\IssueType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\IssueType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set reporter
     *
     * @param \AppBundle\Entity\User $reporter
     *
     * @return Issue
     */
    public function setReporter(\AppBundle\Entity\User $reporter)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return \AppBundle\Entity\User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set assignee
     *
     * @param \AppBundle\Entity\User $assignee
     *
     * @return Issue
     */
    public function setAssignee(\AppBundle\Entity\User $assignee)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \AppBundle\Entity\User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set project
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return Issue
     */
    public function setProject(\AppBundle\Entity\Project $project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Issue $parent
     *
     * @return Issue
     */
    public function setParent(\AppBundle\Entity\Issue $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Issue
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Issue $child
     *
     * @return Issue
     */
    public function addChild(\AppBundle\Entity\Issue $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Issue $child
     */
    public function removeChild(\AppBundle\Entity\Issue $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add collaborator
     *
     * @param \AppBundle\Entity\User $collaborator
     *
     * @return Issue
     */
    public function addCollaborator(\AppBundle\Entity\User $collaborator)
    {
        $this->collaborators[] = $collaborator;

        return $this;
    }

    /**
     * Remove collaborator
     *
     * @param \AppBundle\Entity\User $collaborator
     */
    public function removeCollaborator(\AppBundle\Entity\User $collaborator)
    {
        $this->collaborators->removeElement($collaborator);
    }

    /**
     * Get collaborators
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * Set priority
     *
     * @param \AppBundle\Entity\IssuePriority $priority
     *
     * @return Issue
     */
    public function setPriority(\AppBundle\Entity\IssuePriority $priority = null)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return \AppBundle\Entity\IssuePriority
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
