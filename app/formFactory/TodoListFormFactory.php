<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 12:32
 */

namespace App\FormFactory;

use Nette\Application\UI\Form;

class TodoListFormFactory
{
	/**
	 * @return Form
	 */
	public static function create() : Form
	{
		$form = new Form;
		$form->addText('name', 'Název:');
		$form->addSubmit('add', 'Přidat');

		return $form;
	}
}