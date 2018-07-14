<?php


class UserCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
		public function redirectToLoginFormIfNotLoggedIn(AcceptanceTester $I)
		{
			$I->amOnPage('/');
			$I->see('Přihlásit');
		}

		public function registration(AcceptanceTester $I)
		{
			$I->amOnPage('/sign/login');
			$I->fillField('username','Example User');
			$I->fillField('email','example.user@gmail.com');
			$I->fillField('password','1234');
			$I->click('register');
			$I->see('Moje Todo Listy');
		}

		public function login(AcceptanceTester $I)
		{
			$I->amOnPage('/sign/login');
			$I->fillField('email','martin.pohorsky@gmail.com');
			$I->fillField('password','1234');
			$I->click('login');
			$I->see('Moje Todo Listy');
		}

		public function logout(AcceptanceTester $I)
		{
			$I->amGoingTo('Log in');
			$I->amOnPage('/sign/login');
			$I->fillField('email','martin.pohorsky@gmail.com');
			$I->fillField('password','1234');
			$I->click('login');
			$I->click('Odhlásit');
			$I->seeInCurrentUrl('/sign/login');
		}

		public function emailUniqueness(AcceptanceTester $I)
		{
			$I->haveInDatabase('user', [ 'username' => 'Example User', 'email' => 'example.user@gmail.com', 'password' => 'aaa' ]);
			$I->amOnPage('/sign/login');
			$I->fillField('username','Example User');
			$I->fillField('email','unique@gmail.com');
			$I->fillField('password','1234');
			$I->click('register');
			$I->see('User with same username exists');
		}

		public function usernameUniqueness(AcceptanceTester $I)
		{
			$I->haveInDatabase('user', [ 'username' => 'Example User', 'email' => 'example.user@gmail.com', 'password' => 'aaa' ]);
			$I->amOnPage('/sign/login');
			$I->fillField('username','Unique name');
			$I->fillField('email','example.user@gmail.com');
			$I->fillField('password','1234');
			$I->click('register');
			$I->see('User with this email already exists.');
		}
}
