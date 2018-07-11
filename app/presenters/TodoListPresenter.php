<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 12:19
 */

namespace App\Presenters;

use App\Component\TasksControl;
use App\Component\TodoListsControl;
use App\Form\ITaskFormFactory;
use App\Form\ITodoListFormFactory;
use App\Form\TaskForm;
use App\Form\TodoListForm;
use App\Functionality\TodoListFunctionality;
use App\Model\TodoList;
use Nette\Application\AbortException;

class TodoListPresenter extends BasePresenter
{
	/** @var TodoListFunctionality @inject */
	public $todoListFunctionality;

	/** @var ITodoListFormFactory @inject */
	public $todoListFormFactory;

	/** @var ITaskFormFactory @inject */
	public $taskFormFactory;

	public function createComponentTodoListForm()
	{
		$form = $this->todoListFormFactory->create();
		$form->onTodoListSave[] = function (TodoListForm $form, TodoList $todoList) {
			$this->redirect('show', $todoList->getId());
		};

		return $form;
	}

	public function createComponentTaskForm()
	{
		$form = $this->taskFormFactory->create();
		$form->setTodoListId($this->getParameter('todoListId'));
		$form->onTaskSave[] = function (TaskForm $form, TodoList $todoList) {
			$this->redirect('show', $todoList->getId());
		};

		return $form;
	}

	protected function createComponentTodoLists()
	{
		$control = new TodoListsControl($this->todoListFunctionality, $this->currentUser);
		return $control;
	}

	/**
	 * @return TasksControl
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 */
	protected function createComponentTasks()
	{
		$control = new TasksControl($this->em, $this->currentUser);
		$todoList = $this->todoListFunctionality->getTodoList($this->getParameter('id'));
		$control->setTodoList($todoList);
		$control->onTaskCompletion[] = function (TasksControl $control, TodoList $todoList) {
			$this->redirect('TodoList:show', $todoList->getId());
		};
		return $control;
	}

	/**
	 * @param string $username
	 * @throws AbortException
	 */
	public function renderDefault(string $username = null)
	{
		/*
		 * Automatically get username attribute if user is logged in.
		 * In some cases it would be better to redirect user with correct parameter,
		 * but this is faster option.
		 */
		if ($username == null && $this->currentUser != null)
		{
			$username = $this->currentUser->getUsername();
		}

		/*
		 * Do not let user to access other users TodoList.
		 */
		if ($this->currentUser->getUsername() != $username)
		{
			$this->flashMessage('Nemáte oprávnění vidět TODO list jiného uživatele.', 'error');
			$this->redirect('Homepage:');
		}
	}

	public function renderNew()
	{

	}

	public function renderNewTask(int $todoListId)
	{

	}

	/**
	 * @param int $id
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 * @throws AbortException
	 */
	public function renderShow(int $id)
	{
		$todoList = $this->todoListFunctionality->getTodoList($id);

		if($this->currentUser->getId() != $todoList->getOwner()->getId())
		{
			$this->flashMessage('Nemáte oprávnění vidět tuto stránku.', 'error');
			$this->redirect('Homepage:');
		}

		$this->template->todoList = $todoList;
	}
}