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

	public function createComponentTodoListForm()
	{
		$form = $this->todoListFormFactory->create();
		$form->onTodoListSave[] = function (TodoListForm $form, TodoList $todoList) {
			$this->redirect('TodoListTasks:', $todoList->getId());
		};

		return $form;
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
			$this->redirect('TodoList:');
		}
	}

	public function renderNew()
	{

	}
}