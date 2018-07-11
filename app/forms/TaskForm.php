<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 08.07.2018
 * Time: 13:21
 */

namespace App\Form;


use App\Functionality\TodoListFunctionality;
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

	/** @var integer */
	protected $todoListId;

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

		return $form;
	}

	public function setTodoListId(int $id)
	{
		$this->todoListId = $id;
	}

	/**
	 * @param Form $form
	 * @param array $values
	 * @throws \Exception
	 */
	public function processForm(Form $form, array $values)
	{
		$todoList = $this->todoListFunctionality->getTodoList($this->todoListId);

		if(!$todoList) $this->error('TODO list s tímto ID neexistuje');

		/*
		 * Do not let users to add tasks to other users TodoList.
		 */
		if($todoList->getOwner()->getId() != $this->currentUser->getId())
			throw new ForbiddenRequestException('Nemáte oprávnění přidávat úkoly z TODO listu, který vám nepatří.');

		$this->todoListFunctionality->addTask($todoList, $values['name']);
		$this->em->flush();
		$this->onTaskSave($this, $todoList);
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