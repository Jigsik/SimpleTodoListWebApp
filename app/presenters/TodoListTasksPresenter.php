<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 11.07.2018
 * Time: 17:33
 */

namespace App\Presenters;

use App\Component\ITasksControlFactory;
use App\Component\TasksControl;
use App\Form\ITaskFormFactory;
use App\Form\TaskForm;
use App\Functionality\TodoListFunctionality;
use App\Model\Task;
use App\Model\TodoList;
use Nette\Application\AbortException;

class TodoListTasksPresenter extends BasePresenter
{
	/** @var TodoListFunctionality @inject */
	public $todoListFunctionality;

	/** @var ITasksControlFactory @inject */
	public $tasksControlFactory;

	/** @var ITaskFormFactory @inject */
	public $taskFormFactory;

	/** @var TodoList */
	protected $todoList;

	/**
	 * @return TasksControl
	 */
	protected function createComponentTasks()
	{
		$control = $this->tasksControlFactory->create($this->getParameter('id'));
		$control->onTaskChange[] = function (TasksControl $control, TodoList $todoList) {
			$this->redirect('default', $todoList->getId());
		};

		return $control;
	}

	/**
	 * @return TaskForm
	 */
	public function createComponentTaskForm()
	{
		$form = $this->taskFormFactory->create();
		$todoListId = $this->getParameter('todoListId');
		if($todoListId) $form->setTodoList($this->todoListFunctionality->getTodoList($todoListId));
		$task = $this->todoListFunctionality->getTask($this->getParameter('id'));
		if($task) $form->setTask($task);
		$form->onTaskSave[] = function (TaskForm $form, TodoList $todoList) {
			$this->redirect('default', $todoList->getId());
		};

		return $form;
	}

	/**
	 * @param int $id
	 * @throws AbortException
	 */
	public function actionDefault(int $id)
	{
		$this->todoList = $this->todoListFunctionality->getTodoList($id);

		if($this->currentUser->getId() != $this->todoList->getOwner()->getId())
		{
			$this->flashMessage('Nemáte oprávnění vidět tuto stránku.', 'error');
			$this->redirect('Homepage:');
		}
	}

	public function renderDefault()
	{
		$this->template->todoList = $this->todoList;
	}

	public function actionNewTask(int $todoListId)
	{

	}

	public function actionEditTask(int $id)
	{

	}
}