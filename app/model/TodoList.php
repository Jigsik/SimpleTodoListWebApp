<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 11:43
 */

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class TodoList
 * @package App\Model
 *
 * @ORM\Entity
 */
class TodoList
{
	use \Kdyby\Doctrine\Entities\Attributes\Identifier;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $name;

	/**
	 * @ORM\OneToMany(  targetEntity="Task",
	 *                  mappedBy="todoList",
	 *                  cascade={"persist", "remove"},
	 *                  orphanRemoval=true)
	 * @var Collection<Task>
	 */
	protected $tasks;

	/**
	 * Entity creation timestamp.
	 *
	 * @ORM\Column(type="datetime", nullable=false)
	 * @var \DateTime
	 */
	protected $created;

	/**
	 * @ORM\ManyToOne(
	 *     targetEntity="User",
	 *     inversedBy="todoLists"
	 * )
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
	 * @var User
	 */
	protected $owner;

	public function __construct(string $name, User $owner)
	{
		$this->name = $name;
		$this->owner = $owner;
		$this->created = new \DateTime();
		$this->tasks = new ArrayCollection();
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return Collection
	 */
	public function getTasks(): Collection
	{
		return $this->tasks;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated(): \DateTime
	{
		return $this->created;
	}

	/**
	 * @return User
	 */
	public function getOwner(): User
	{
		return $this->owner;
	}
}