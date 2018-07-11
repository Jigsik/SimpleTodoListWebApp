<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 11:48
 */

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Task
 * @package App\Model
 *
 * @ORM\Entity
 */
class Task
{
	use \Kdyby\Doctrine\Entities\Attributes\Identifier;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $name;

	/**
	 * @ORM\ManyToOne(
	 *     targetEntity="TodoList",
	 *     inversedBy="tasks")
	 * @ORM\JoinColumn(name="todo_list_id", referencedColumnName="id", nullable=false)
	 * @var TodoList
	 */
	protected $todoList;

	/**
	 * Entity creation timestamp.
	 *
	 * @ORM\Column(type="datetime", nullable=false)
	 * @var \DateTime
	 */
	protected $created;

	/**
	 * @ORM\Column(type="boolean", options={"default": false})
	 * @var bool
	 */
	protected $finished = false;

	public function __construct(string $name, TodoList $todoList)
	{
		$this->name = $name;
		$this->created = new \DateTime();
		$this->todoList = $todoList;
	}

	/**
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 * @return Task
	 */
	public function setName(string $name) : Task
	{
		$this->name = $name;
		return $this;
	}

	public function markAsCompleted() : Task
	{
		$this->finished = true;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated(): \DateTime
	{
		return $this->created;
	}

	/**
	 * @return bool
	 */
	public function isFinished(): bool
	{
		return $this->finished;
	}

	/**
	 * @return TodoList
	 */
	public function getTodoList(): TodoList
	{
		return $this->todoList;
	}
}