<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 11.07.2018
 * Time: 13:28
 */

namespace App\Component;


use App\Model\TodoList;

class TasksControl extends \Nette\Application\UI\Control
{
	/** @var TodoList */
	protected $todoList;

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
}