<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 08.07.2018
 * Time: 13:21
 */

namespace App\Form;


use App\Functionality\TodoListFunctionality;
use App\Model\Task;
use App\Model\TodoList;
use App\Model\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette;

class TaskForm extends Control
{
	/** @var EntityManager */
	protected $em;

	/** @var TodoListFunctionality */
	protected $todoListFunctionality;

	/** @var User */
	protected $currentUser;

	/** @var TodoList */
	protected $todoList;

	/** @var Task */
	protected $task = null;

	public $onTaskSave;

	public function __construct(EntityManager $em, TodoListFunctionality $todoListFunctionality, Nette\Security\User $user)
	{
		parent::__construct();
		$this->em = $em;
		$this->todoListFunctionality = $todoListFunctionality;
		$this->currentUser = $user->getIdentity();
	}

	public function createComponentForm() : Form
	{
		$form = new Form;
		$form->addText('name', 'Název:');
		$form->addSubmit('add', 'Přidat');
		$form->onSuccess[] = [$this, 'processForm'];

		if($this->task != null) {
			$form->setDefaults([
				'name' => $this->task->getName()
			]);
		}

		return $form;
	}

	public function setTodoList(TodoList $todoList)
	{
		$this->todoList = $todoList;
	}

	public function setTask(Task $task)
	{
		$this->task = $task;
	}

	/**
	 * @param Form $form
	 * @param array $values
	 * @throws \Exception
	 */
	public function processForm(Form $form, array $values)
	{
		if($this->task) $this->todoList = $this->task->getTodoList();

		if(!$this->todoList) $this->error('TODO list s tímto ID neexistuje');

		/*
		 * Do not let users to add tasks to other users TodoList.
		 */
		if($this->todoList->getOwner()->getId() != $this->currentUser->getId())
			throw new ForbiddenRequestException('Nemáte oprávnění přidávat úkoly z TODO listu, který vám nepatří.');

		if($this->task) {
			$this->task->setName($values['name']);
			$this->em->persist($this->task);
		} else {
			$this->todoListFunctionality->addTask($this->todoList, $values['name']);
		}

		$this->em->flush();
		$this->onTaskSave($this, $this->todoList);
	}

	public function render()
	{
		$this['form']->render();
	}
}

interface ITaskFormFactory
{
	/** @return TaskForm */
	function create();
}