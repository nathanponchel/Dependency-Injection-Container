<?php
namespace App;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;


trait reflectionTrait {

	/**
	 * In replacement of the native PHP getClass() method of the Reflection API witch is now deprecated
	 *
	 * @param ReflectionParameter $parameter
	 * @return ReflectionClass|null
	 * @throws ReflectionException
	 */
	public function getClass(ReflectionParameter $parameter): ReflectionClass | null
	{
		return $parameter->getType() && ! $parameter->getType()->isBuiltin()
			? new ReflectionClass($parameter->getType()->getName()) : null;
	}
}