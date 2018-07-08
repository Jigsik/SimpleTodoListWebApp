<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 23:24
 */

namespace App\Authenticator;

use App\Functionality\UserFunctionality;
use App\Model\User;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

class Authenticator implements IAuthenticator
{
	/** @var UserFunctionality */
	protected $userFunctionality;

	public function __construct(UserFunctionality $userFunctionality)
	{
		$this->userFunctionality = $userFunctionality;
	}

	/**
	 * Performs an authentication against e.g. database.
	 * and returns IIdentity on success or throws AuthenticationException
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	function authenticate(array $credentials)
	{
		list($email, $password) = $credentials;

		/** @var User $user */
		$user = $this->userFunctionality->findByEmail($email);

		if (!$user || !Passwords::verify($password, $user->getPassword())) {
			throw new AuthenticationException('Nesprávné přístupové údaje.', self::INVALID_CREDENTIAL);
		}

		return $user;
	}
}