<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 12:20
 */

namespace App\Presenters;

use App\Functionality\UserFunctionality;
use App\Model\User;
use Kdyby\Doctrine\EntityManager;
use Nette;

class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @var User */
	protected $currentUser;

	/** @var UserFunctionality @inject */
	public $userFunctionality;

	/** @var EntityManager @inject */
	public $em;

	/**
	 * @param bool $skipAuthorization
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 * @throws Nette\Application\AbortException
	 */
	public function startup(bool $skipAuthorization = false)
	{
		parent::startup();
		if (!$skipAuthorization)
		{
			if ($this->getUser()->isLoggedIn()) {
				$this->currentUser = $this->userFunctionality->getUser($this->getUser()->getId());
				$this->template->currentUser = $this->currentUser;
			} else {
				$this->redirect('Sign:login');
			}
		}
	}
}