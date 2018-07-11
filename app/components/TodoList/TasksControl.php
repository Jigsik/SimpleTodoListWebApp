<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 11.07.2018
 * Time: 13:28
 */

namespace App\Component;


use App\Model\Task;
use App\Model\TodoList;
use App\Model\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\ForbiddenRequestException;

class TasksControl extends \Nette\Application\UI\Control
{
	/** @var EntityManager */
	protected $em;

	/** @var User */
	protected $currentUser;

	/** @var TodoList */
	protected $todoList;

	public $onTaskChange;

	public function __construct(EntityManager $em, User $user)
	{
		parent::__construct();
		$this->em = $em;
		$this->currentUser = $user;
	}

	public function setTodoList(TodoList $todoList)
	{
		$this->todoList = $todoList;
	}

	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/tasks.latte');
		$template->tasks = $this->todoList->getTasks();
		$template->render();
	}

	/**
	 * @param int $taskId
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 * @throws \Exception
	 */
	public function handleMarkAsCompleted(int $taskId)
	{
		/** @var Task $task */
		$task = $this->em->find(Task::class, $taskId);

		if($task->getTodoList()->getOwner()->getId() != $this->currentUser->getId())
			throw new ForbiddenRequestException('Nemáte oprávnění pracovat s úkoly, které vám nepatří.');

		$task->markAsCompleted();
		$this->em->persist($task);
		$this->em->flush($task);

		$this->onTaskChange($this, $task->getTodoList());
	}

	/**
	 * @param int $taskId
	 * @throws ForbiddenRequestException
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 * @throws \Exception
	 */
	public function handleDelete(int $taskId)
	{
		/** @var Task $task */
		$task = $this->em->find(Task::class, $taskId);

		if($task->getTodoList()->getOwner()->getId() != $this->currentUser->getId())
			throw new ForbiddenRequestException('Nemáte oprávnění pracovat s úkoly, které vám nepatří.');

		$this->em->remove($task);
		$this->em->flush($task);

		$this->onTaskChange($this, $task->getTodoList());
	}
}