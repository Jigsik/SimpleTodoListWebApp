<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 12:19
 */

namespace App\Presenters;

use App\FormFactory\TaskFormFactory;
use App\FormFactory\TodoListFormFactory;
use App\Functionality\TodoListFunctionality;
use App\Model\Task;
use App\Model\TodoList;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

class TodoListPresenter extends BasePresenter
{
	/** @var TodoListFunctionality @inject */
	public $todoListFunctionality;

	/**
	 * @return Form
	 */
	public function createComponentTodoListForm(): Form
	{
		$form = TodoListFormFactory::create();
		$form->onSuccess[] = [$this, 'todoListFormSucceeded'];
		return $form;
	}

	/**
	 * @param Form $form
	 * @param array $values
	 * @throws \Exception
	 */
	public function todoListFormSucceeded(Form $form, array $values)
	{
		/** @var TodoList $todoList */
		$todoList = $this->todoListFunctionality->create($values, $this->currentUser);
		$this->em->flush(TodoList::class);
		$this->redirect('show', $todoList->getId());
	}

	public function createComponentTaskForm() : Form
	{
		$form = TaskFormFactory::create();
		$form->onSuccess[] = [$this, 'taskFormSucceeded'];
		return $form;
	}

	/**
	 * @param Form $form
	 * @param array $values
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 * @throws \Exception
	 */
	public function taskFormSucceeded(Form $form, array $values)
	{
		$todoList = $this->todoListFunctionality->getTodoList($this->getParameters()['todoListId']);

		if($todoList->getOwner()->getId() != $this->currentUser->getId())
		{
			$this->flashMessage('Nemáte oprávnění vidět tuto stránku.', 'error');
			$this->redirect('Homepage:');
		}

		$task = new Task($values['name'], $todoList);
		$this->em->persist($task);
		$this->em->flush();
		$this->redirect('show', $todoList->getId());
	}

	/**
	 * @param string $username
	 * @throws AbortException
	 */
	public function renderDefault(string $username)
	{
		if ($this->currentUser->getUsername() != $username)
		{
			$this->flashMessage('Nemáte oprávnění vidět tuto stránku.', 'error');
			$this->redirect('Homepage:');
		}

		$this->template->todoLists = $this->todoListFunctionality->getTodoListByUser($this->currentUser);
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