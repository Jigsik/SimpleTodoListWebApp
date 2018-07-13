<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 11.07.2018
 * Time: 13:28
 */

namespace App\Component;


use App\Functionality\TodoListFunctionality;
use App\Model\Task;
use App\Model\TodoList;
use App\Model\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use Nette;

class TasksControl extends \Nette\Application\UI\Control
{
	/** @var EntityManager */
	protected $em;

	/** @var TodoListFunctionality */
	protected $todoListFunctionality;

	/** @var User */
	protected $currentUser;

	/** @var TodoList */
	protected $todoList;

	/** @var int @persistent */
	public $page = 1;

	public $onTaskChange;

	public function __construct(EntityManager $em, TodoListFunctionality $todoListFunctionality, Nette\Security\User $user,
															int $todoListId)
	{
		parent::__construct();
		$this->em = $em;
		$this->currentUser = $user->getIdentity();
		$this->todoListFunctionality = $todoListFunctionality;
		$this->todoList = $this->todoListFunctionality->getTodoList($todoListId);
	}


	/**
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/tasks.latte');

		$tasksCount = $this->todoListFunctionality->getNotCompletedTasksCount($this->todoList->getId());
		$paginator = new Nette\Utils\Paginator;
		$paginator->setItemCount($tasksCount);
		$paginator->setItemsPerPage(5);
		$paginator->setPage($this->page);

		// Get tasks according to pagination;
		$tasks = $this->todoList->getTasks();

		$template->tasks = $tasks->slice($paginator->offset, $paginator->length);
		$template->paginator = $paginator;

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

	/**
	 * @param int $page
	 * @throws AbortException
	 */
	public function handleChangePage(int $page)
	{
		$this->page = $page;
		$this->redirect('this');
	}
}

interface ITasksControlFactory
{
	/**
	 * @param int $todoListId
	 * @return TasksControl
	 */
	function create(int $todoListId);
}