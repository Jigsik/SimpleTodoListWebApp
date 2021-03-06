<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 12:24
 */

namespace App\Model;

use Nette\Security\IIdentity;
use Doctrine\ORM\Mapping as ORM;
use Nette\Security\Passwords;

/**
 * Class User
 * @package App\Model
 *
 * @ORM\Entity
 */
class User implements IIdentity
{
	use \Kdyby\Doctrine\Entities\Attributes\Identifier;

	/**
	 * @ORM\Column(type="string", unique=true)
	 * @var string
	 */
	protected $username;

	/**
	 * @ORM\Column(type="string", unique=true)
	 * @var string
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $password;

	public function __construct(string $username, string $email, string $password)
	{
		$this->username = $username;
		$this->email = $email;
		$this->password = Passwords::hash($password);
	}

	/**
	 * Returns a list of roles that the user is a member of.
	 * @return array
	 */
	function getRoles()
	{
		return [ 'user' ];
	}

	/**
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}
}