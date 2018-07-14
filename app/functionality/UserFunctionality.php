<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 13:07
 */

namespace App\Functionality;

use App\Exceptions\ValidationException;
use App\Model\User;
use Kdyby\Doctrine\EntityManager;

class UserFunctionality extends BaseFunctionality
{
	/** @var \Kdyby\Doctrine\EntityRepository  */
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
	 * @throws ValidationException
	 */
	public function register(string $username, string $email, string $password) : User
	{
		$q = $this->userRepository->createQueryBuilder()
			->select('u')
			->from('App\Model\User', 'u')
			->where('u.username = :username')
			->orWhere('u.email = :email')
			->setParameter('username', $username)
			->setParameter('email', $email)
			->getQuery();

		/** @var array<User> $duplicates */
		$duplicates = $q->getResult();

		if(!empty($duplicates)) {
			/** @var User $duplicate_user */
			$duplicate_user = $duplicates[0];
			if($duplicate_user->getUsername() == $username) {
				throw new ValidationException('User with same username exists.');
			} else {
				throw new ValidationException('User with this email already exists.');
			}
		}

		$user = new User($username, $email, $password);
		$this->em->persist($user);
		return $user;
	}
}