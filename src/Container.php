<?php
namespace App;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;


class Container implements ContainerInterface {

	use reflectionTrait;

	private array $instances = [];
	private array $aliases = [];
	private array $definitions = [];


	public function get(string $id)
	{
		// Is instance present in container?
		if(!$this->has($id))
		{
			$instance = $this->getDefinition($id)->newInstance($this);;

			if(!$this->getDefinition($id)->isShared())
			{
				return $instance;
			}

			$this->instances[$id] = $instance;
		}

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


	private function register(string $id): self
	{
		$reflectedClass = new ReflectionClass($id);

		if($reflectedClass->isInterface())
		{
			$this->register($this->aliases[$id]);
			$this->definitions[$id] = &$this->definitions[$this->aliases[$id]];

			return $this;
		}

		$constructor = $reflectedClass->getConstructor();

		$dependencies = [];

		if($constructor)
		{
			$dependencies = array_map(
				fn(ReflectionParameter $parameter) => $this->getDefinition($this->getClass($parameter)->getName()),
				$constructor->getParameters()
			);
		}

		$aliases = array_filter($this->aliases, fn(string $alias) => $alias === $id);

		$definition = new Definition($id, true, $aliases, $dependencies);
		$this->definitions[$id] = $definition;

		return $this;
	}


	/**
	 * Get definition set in container, if the definition is not present, we register it.
	 *
	 * @param $id
	 * @return Definition
	 */
	public function getDefinition($id): Definition
	{
		if(!isset($this->definitions[$id]))
		{
			$this->register($id);
		}

		return $this->definitions[$id];
	}


	/**
	 * Set an alias to match between interface <=> class
	 *
	 * @param string $interfaceName
	 * @param string $className
	 * @return $this
	 */
	public function setAlias(string $interfaceName, string $className): self
	{
		$this->aliases[$interfaceName] = $className;

		return $this;
	}
}
