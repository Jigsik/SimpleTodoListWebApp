<?php


class TaskCest
{
    public function _before(AcceptanceTester $I)
    {
    	$I->amGoingTo('Log in');
	    $I->amOnPage('/sign/login');
	    $I->fillField('username','Martin Pohorský');
	    $I->fillField('email','martin.pohorsky@gmail.com');
	    $I->fillField('password','1234');
	    $I->click('login');
	    $I->amGoingTo('Create Todo List');
	    $I->click('Nový');
	    $I->see('Název');
	    $I->fillField('name','Example');
	    $I->click('add');
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function createTask(AcceptanceTester $I)
    {
    	$task_name = 'New Task';

	    $I->click('Nový úkol');
	    $I->fillField('name', $task_name);
	    $I->click('add');
	    $I->see($task_name);
	    $I->seeLink('Editovat');
	    $I->seeLink('Hotovo');
	    $I->seeLink('Smazat');
    }

		public function editTask(AcceptanceTester $I)
		{
			$task_name = 'Task to edit';

			$I->amGoingTo('Create Example Task');
			$I->click('Nový úkol');
			$I->fillField('name',$task_name);
			$I->click('add');
			$I->click('Editovat');
			$I->fillField('name','Edited Task');
			$I->click('add');
			$I->see('Edited Task');
			$I->dontSee($task_name);
		}

		public function deleteTask(AcceptanceTester $I)
		{
			$task_name = 'Task to delete';

			$I->amGoingTo('Create Example Task');
			$I->click('Nový úkol');
			$I->fillField('name', $task_name);
			$I->click('add');
			$I->click('Smazat');
			$I->dontSee($task_name);
			$I->dontSeeInDatabase('task', ['name' => $task_name]);
		}

		public function markTaskAsCompleted(AcceptanceTester $I)
		{
			$task_name = 'Task to complete';

			$I->amGoingTo('Create Example Task');
			$I->click('Nový úkol');
			$I->fillField('name', $task_name);
			$I->click('add');
			$I->click('Hotovo');
			$I->dontSee($task_name);
			$I->seeInDatabase('task', ['name' => $task_name, 'finished' => 1]);
		}
}
