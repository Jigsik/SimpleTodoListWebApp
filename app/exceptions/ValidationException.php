<?php
/**
 * Created by PhpStorm.
 * User: Martin Pohorský
 * Date: 30.01.2018
 * Time: 14:21
 */

namespace App\Exceptions;

use Exception;

class ValidationException extends \Exception
{
	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}
