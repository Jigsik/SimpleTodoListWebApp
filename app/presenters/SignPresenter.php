<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 21:57
 */

namespace App\Presenters;


use App\Exceptions\ValidationException;
use App\Form\SignInFormFactory;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Security\AuthenticationException;

class SignPresenter extends BasePresenter
{
	public function startup(bool $skipAuthorization = true)
	{
		parent::startup($skipAuthorization);
	}

	protected function createComponentSignInForm() : Form
	{
		$form = SignInFormFactory::create();
		$form->addSubmit('login', 'Přihlásit')
			->onClick[] = [$this, 'loginFormSucceeded'];
		$form->addSubmit('register', 'Registrovat')
			->onClick[] = [$this, 'registrationFormSucceeded'];
		return $form;
	}

	/**
	 * @param SubmitButton $button
	 * @throws AbortException
	 */
	public function loginFormSucceeded(SubmitButton $button)
	{
		$form = $button->getForm();
		$values = $form->getValues(true);

		try {
			$this->login($values['email'], $values['password']);
		} catch (AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

	/**
	 * @param SubmitButton $button
	 * @throws AbortException
	 * @throws \Exception
	 */
	public function registrationFormSucceeded(SubmitButton $button)
	{
		$form = $button->getForm();
		$values = $form->getValues(true);

		try {
			$this->userFunctionality->register($values['username'] ?? 'Random Username', $values['email'], $values['password']);

			$this->em->flush();

			try {
				$this->login($values['email'], $values['password']);
			} catch (AuthenticationException $e) {
				$form->addError($e->getMessage());
			}
		} catch (ValidationException $e) {
			$form->addError($e->getMessage());
		}
	}

	/**
	 * @param string $email
	 * @param string $password
	 * @throws AbortException
	 * @throws AuthenticationException
	 */
	protected function login(string $email, string $password)
	{
		$this->getUser()->login($email, $password);
		$user = $this->userFunctionality->findByEmail($email);
		$this->redirect('TodoList:default', $user->getUsername());
	}

	/**
	 * @throws AbortException
	 */
	public function actionLogout()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jste úspěšně odhlášen.');
		$this->redirect('login');
	}

	public function renderLogin()
	{

	}
}