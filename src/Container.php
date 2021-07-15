<?php
namespace App;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;


class Container implements ContainerInterface {

	private array $instances = [];
	private array $aliases = [];

	public function get(string $id): object
	{
		// Instance not present in our container?
		if(!$this->has($id))
		{
			$reflectedClass = new ReflectionClass($id);

			if($reflectedClass->isInterface())
			{
				return $this->get($this->aliases[$id]);  //Need to verify => isset($this->aliases[$id])
			}

			$constructor = $reflectedClass->getConstructor();
			// Is constructor defined?
			if(is_null($constructor))
			{
				// Create and store the instance without parameter
				$this->instances[$id] = $reflectedClass->newInstance();
			}else
			{
				// We're looking for the constructor arguments
				$parameters = $constructor->getParameters();

				//Create and store the instance with recursion callback to resolve all "sub-"dependencies
				$this->instances[$id] = $reflectedClass->newInstanceArgs(
					array_map(
						fn(ReflectionParameter $parameter) => $this->get($this->getClass($parameter)->getName()),
						$parameters
					)
				);
			}
		}
		// Finally returning the previously created instance
		return $this->instances[$id];
	}


	/**
	 * Check if the passed instance is present in our container
	 *
	 * @param string $id
	 * @return bool
	 */
	public function has(string $id): bool
	{
		return isset($this->instances[$id]);
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


	/**
	 * Add aliases in our container to match between interface <=> class
	 *
	 * @param string $id
	 * @param string $targetClass
	 * @return $this
	 */
	public function addAlias(string $id, string $targetClass): self
	{
		$this->aliases[$id] = $targetClass;

		return $this;
	}
}