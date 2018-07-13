<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 12:43
 */

namespace App\Functionality;

use App\Model\Task;
use App\Model\TodoList;
use App\Model\User;
use Kdyby\Doctrine\EntityManager;

class TodoListFunctionality extends BaseFunctionality
{
	protected $repository;

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->repository = $em->getRepository(TodoList::class);
	}

	/**
	 * @param string $name
	 * @param User $user
	 * @return TodoList
	 */
	public function create(string $name, User $user) : TodoList
	{
		$todoList = new TodoList($name, $user);
		$this->em->persist($todoList);
		return $todoList;
	}

	/**
	 * @param $id
	 * @return TodoList
	 */
	public function getTodoList($id) : ?TodoList
	{
		/** @var TodoList $todoList */
		$todoList = $this->repository->find($id);
		return $todoList;
	}

	/**
	 * @param User $currentUser
	 * @return array|mixed
	 */
	public function getTodoListByUser(User $currentUser)
	{
		$todoLists = $this->repository->findBy([ 'owner' => $currentUser ]);
		return $todoLists;
	}

	public function addTask(TodoList $todoList, string $name)
	{
		$task = new Task($name, $todoList);
		$todoList->addTask($task);
		$this->em->persist($todoList);
	}

	public function getTask($id) : ?Task
	{
		if($id == NULL) return NULL;

		/** @var Task $task */
		$task = $this->em->getRepository(Task::class)->find($id);
		return $task;
	}

	/**
	 * @param int $todoListId
	 * @return int
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getNotCompletedTasksCount($todoListId): int
	{
		$qb = $this->em->createQueryBuilder();
		$query = $qb
			->select('count(t.id)')
			->from('App\Model\Task', 't')
			->where('t.finished = false')
			->andWhere('t.todoList = :todoList')
			->setParameter('todoList', $this->repository->getReference($todoListId))
			->getQuery();

		return $query->getSingleScalarResult();
	}
}