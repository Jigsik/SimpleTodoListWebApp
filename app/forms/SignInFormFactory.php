<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 13:43
 */

namespace App\Form;


use Nette\Application\UI\Form;

class SignInFormFactory
{
	public static function create() : Form
	{
		$form = new Form;
		$form->addText('name', 'Jméno:');
		$form->addEmail('email', 'Email:');
		$form->addPassword('password', 'Heslo:');
		return $form;
	}
}