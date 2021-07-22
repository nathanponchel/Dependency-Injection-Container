<?php
namespace App;

use Psr\Container\ContainerInterface;
use ReflectionClass;


class Container implements ContainerInterface {

	use reflectionTrait;

	private array $instances = [];
	private array $aliases = [];
	private array $definitions = [];


	/**
	 * @param string $id
	 * @return mixed|object
	 * @throws \ReflectionException
	 */
	public function get(string $id)
	{
		if(!$this->has($id))
		{
			$instance = $this->getDefinition($id)->newInstance($this);;

			if(!$this->getDefinition($id)->isShared())
			{
				// isShared = false => never store instance in container => always new instance
				return $instance;
			}

			$this->instances[$id] = $instance;
		}

		return $this->instances[$id];
	}


	/**
	 * Is instance present in our container?
	 *
	 * @param string $id
	 * @return bool
	 */
	public function has(string $id): bool
	{
		return isset($this->instances[$id]);
	}


	/**
	 * Create a new Definition, with some controls
	 *
	 * @param string $id
	 * @return $this
	 * @throws \ReflectionException
	 */
	private function register(string $id): self
	{
		$reflectedClass = new ReflectionClass($id);

		if($reflectedClass->isInterface())
		{
			// The passed class is an interface, we register it with the corresponding alias.
			$this->register($this->aliases[$id]);
			$this->definitions[$id] = &$this->definitions[$this->aliases[$id]];

			return $this;
		}

		$aliases = array_filter($this->aliases, fn(string $alias) => $alias === $id);

		$this->definitions[$id] = new Definition($id, true, $aliases);

		return $this;
	}


	/**
	 * Get Definition previously set in container, if the Definition is not present, we create it.
	 *
	 * @param $id
	 * @return Definition
	 * @throws \ReflectionException
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
