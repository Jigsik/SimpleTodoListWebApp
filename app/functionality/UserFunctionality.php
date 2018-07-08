<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 13:07
 */

namespace App\Functionality;

use App\Model\User;
use Kdyby\Doctrine\EntityManager;

class UserFunctionality extends BaseFunctionality
{
	protected $userRepository;

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->userRepository = $em->getRepository(User::class);
	}

	/**
	 * @param $id
	 * @return null|User
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 */
	public function getUser($id) : User
	{
		/** @var User $user */
		$user = $this->em->find(User::class, $id);
		return $user;
	}

	/**
	 * @param string $email
	 * @return User|null
	 */
	public function findByEmail(string $email) : ?User
	{
		/** @var User $user */
		$user = $this->userRepository->findOneBy([ 'email' => $email ]);
		return $user;
	}

	/**
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @return User
	 */
	public function register(string $username, string $email, string $password) : User
	{
		$user = new User($username, $email, $password);
		$this->em->persist($user);
		return $user;
	}
}