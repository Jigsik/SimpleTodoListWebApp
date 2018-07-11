<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 08.07.2018
 * Time: 13:21
 */

namespace App\Form;


use Nette\Application\UI\Form;

class TaskFormFactory
{
	public static function create() : Form
	{
		$form = new Form;
		$form->addText('name', 'Název:');
		$form->addSubmit('add', 'Přidat');

		return $form;
	}
}