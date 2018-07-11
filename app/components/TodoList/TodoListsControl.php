<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 10.07.2018
 * Time: 15:00
 */

namespace App\Component;

use App\Functionality\TodoListFunctionality;
use App\Model\User;

class TodoListsControl extends \Nette\Application\UI\Control
{
	/** @var TodoListFunctionality */
	protected $todoListFunctionality;

	/** @var User */
	protected $currentUser;

	public function __construct(TodoListFunctionality $todoListFunctionality, User $user)
	{
		parent::__construct();
		$this->todoListFunctionality = $todoListFunctionality;
		$this->currentUser = $user;
	}

	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/todoLists.latte');
		$template->todoLists = $this->todoListFunctionality->getTodoListByUser($this->currentUser);
		$template->render();
	}
}