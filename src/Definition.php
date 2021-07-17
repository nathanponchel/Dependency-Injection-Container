<?php
namespace App;

use ReflectionClass;
use ReflectionParameter;


class Definition {

	private string $id;
	private bool $isShared;
	private ReflectionClass $reflectedClass;
	private array $parameters = [];


	public function __construct(string $id, bool $isShared = true)
	{
		$this->id = $id;
		$this->isShared = $isShared;
		$this->reflectedClass = new ReflectionClass($id);
	}


	public function isShared(): bool
	{
		return $this->isShared;
	}


	/**
	 * Get all the constructor's parameters, null return if at least one of parameters is not a class
	 *
	 * @return array|null
	 * @throws \ReflectionException
	 */
	public function getParameters(): array | null
	{
		$constructor = $this->reflectedClass->getConstructor();

		if(isset($constructor))
		{
			foreach($constructor->getParameters() as $parameter)
			{
				if(is_null($this->getClass($parameter)))
				{
					return null;
				}
				$this->parameters[] = $this->getClass($parameter)->getName();
			}
			return $this->parameters;
		}
	}


	/**
	 * In replacement of the native PHP getClass() method of the Reflection API witch is now deprecated
	 *
	 * @param ReflectionParameter $parameter
	 * @return ReflectionClass|null
	 * @throws \ReflectionException
	 */
	private function getClass(ReflectionParameter $parameter): ReflectionClass | null
	{
		return $parameter->getType() && ! $parameter->getType()->isBuiltin()
			? new ReflectionClass($parameter->getType()->getName()) : null;
	}
}