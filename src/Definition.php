<?php
namespace App;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;


class Definition {

	use reflectionTrait;

	private string $id;
	private ReflectionClass $reflectedClass;
	private bool $shared = true;
	private array $aliases = [];



	public function __construct(string $id, bool $shared = true, array $aliases = [])
	{
		$this->id = $id;
		$this->reflectedClass = new ReflectionClass($id);
		$this->shared = $shared;
		$this->aliases = $aliases;
	}


	/**
	 * Create an instance of a class, with possible constructors parameters, stored in the container
	 *
	 * @param ContainerInterface $container
	 * @return object
	 * @throws \ReflectionException
	 */
	public function newInstance(ContainerInterface $container): object
	{
		$constructor = $this->reflectedClass->getConstructor();

		if(is_null($constructor))
		{
			// Create and store the instance without parameter
			return $this->reflectedClass->newInstance();
		}

		// We're looking for the constructor arguments
		$parameters = $constructor->getParameters();

		//Create and store the instance with recursion callback to resolve all "sub-"dependencies
		return  $this->reflectedClass->newInstanceArgs(
			array_map(
				fn(ReflectionParameter $parameter) => $container->get($this->getClass($parameter)->getName()),
				$parameters
			)
		);
	}


	/**
	 * @param bool $shared
	 */
	public function setShared(bool $shared): void
	{
		$this->shared = $shared;
	}


	/**
	 * @return bool
	 */
	public function isShared(): bool
	{
		return $this->shared;
	}
}
