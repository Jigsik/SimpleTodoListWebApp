<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 12:32
 */

namespace App\Form;

use App\Functionality\TodoListFunctionality;
use App\Model\TodoList;
use App\Model\User;
use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette;

class TodoListForm extends Control
{
	/** @var EntityManager */
	protected $em;

	/** @var TodoListFunctionality */
	protected $todoListFunctionality;

	/** @var User */
	protected $currentUser;

	public $onTodoListSave;

	public function __construct(EntityManager $em, TodoListFunctionality $todoListFunctionality, Nette\Security\User $user)
	{
		parent::__construct();
		$this->em = $em;
		$this->todoListFunctionality = $todoListFunctionality;
		$this->currentUser = $user->getIdentity();
	}

	protected function createComponentForm()
	{
		$form = new Form;
		$form->addText('name', 'Název:');
		$form->addSubmit('add', 'Přidat');
		$form->onSuccess[] = [$this, 'processForm'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @param array $values
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function processForm(Form $form, array $values)
	{
		/** @var TodoList $todoList */
		$todoList = $this->todoListFunctionality->create($values['name'], $this->currentUser);
		$this->em->flush($todoList);
		$this->onTodoListSave($this, $todoList);
	}

	public function render()
	{
		$this['form']->render();
	}
}

interface ITodoListFormFactory
{
	/** @return TodoListForm */
	function create();
}