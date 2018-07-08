<?php
/**
 * Created by PhpStorm.
 * User: OXIT
 * Date: 07.07.2018
 * Time: 13:08
 */

namespace App\Functionality;

use Kdyby\Doctrine\EntityManager;

class BaseFunctionality
{
	protected $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
}