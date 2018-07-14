<?php


class TodoListCest
{
    public function _before(AcceptanceTester $I)
    {
	    $I->amOnPage('/sign/login');
	    $I->fillField('username','Martin Pohorský');
	    $I->fillField('email','martin.pohorsky@gmail.com');
	    $I->fillField('password','1234');
	    $I->click('login');
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function createTodoList(AcceptanceTester $I)
    {
    	$I->click('Nový');
    	$I->see('Název');
	    $I->fillField('name','Example');
	    $I->click('add');
	    $I->seeInDatabase('todo_list', ['name' => 'Example']);
    }

    public function createTodoListWithSameName(AcceptanceTester $I)
    {
    	$I->amGoingTo('Create one Todo List');
	    $I->click('Nový');
	    $I->see('Název');
	    $I->fillField('name','Example');
	    $I->click('add');
	    $I->seeInDatabase('todo_list', ['name' => 'Example']);
	    $I->amGoingTo('Create another Todo List with same name');
	    $I->amOnPage('/?username=Martin+Pohorský');
	    $I->click('Nový');
	    $I->see('Název');
	    $I->fillField('name','Example');
	    $I->click('add');
	    $I->seeNumRecords(2, 'todo_list', ['name' => 'Example']);
    }

    public function restrictOtherUsersTodoList(AcceptanceTester $I)
    {
	    $I->haveInDatabase('user', [ 'username' => 'example_user', 'email' => 'example.user@gmail.com', 'password' => 'aaa' ]);
	    $I->amOnPage('/?username=example_user');
	    $I->seeInCurrentUrl('/');
    	$I->see('Nemáte oprávnění vidět TODO list jiného uživatele.');
    }
}
