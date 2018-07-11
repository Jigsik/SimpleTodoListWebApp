<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 12:19
 */

namespace App\Presenters;

use App\Component\TodoListsControl;
use App\Form\ITodoListFormFactory;
use App\Form\TaskFormFactory;
use App\Form\TodoListForm;
use App\Functionality\TodoListFunctionality;
use App\Model\TodoList;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

class TodoListPresenter extends BasePresenter
{
	/** @var TodoListFunctionality @inject */
	public $todoListFunctionality;

	/** @var ITodoListFormFactory @inject */
	public $todoListFormFactory;

	public function createComponentTodoListForm()
	{
		$form = $this->todoListFormFactory->create();
		$form->onTodoListSave[] = function (TodoListForm $form, TodoList $todoList) {
			$this->redirect('show', $todoList->getId());
		};

		return $form;
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

		$this->todoListFunctionality->addTask($todoList, $values['name']);
		$this->em->flush();
		$this->redirect('show', $todoList->getId());
	}

	protected function createComponentTodoLists()
	{
		$control = new TodoListsControl($this->todoListFunctionality, $this->currentUser);
		return $control;
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