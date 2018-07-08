<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 12:43
 */

namespace App\Functionality;

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
	 * @param array $values
	 * @param User $user
	 * @return TodoList
	 */
	public function create(array $values, User $user) : TodoList
	{
		$todoList = new TodoList($values['name'], $user);
		$this->em->persist($todoList);
		return $todoList;
	}

	/**
	 * @param $id
	 * @return TodoList
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 */
	public function getTodoList($id) : TodoList
	{
		/** @var TodoList $todoList */
		$todoList = $this->em->find(TodoList::class, $id);
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
}