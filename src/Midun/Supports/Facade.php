<?php

namespace Midun\Supports;

use Midun\Http\Exceptions\AppException;

abstract class Facade
{
	/**
	 * Get facade of entity
	 *
	 * @return string
	 *
	 * @throws AppException
	 */
	protected static function getFacadeAccessor(): string
	{
		throw new AppException("Method " . __METHOD__ . " is not override.");
	}

	/**
	 * Call static handler
	 *
	 * @param string $method
	 * @param array $arguments
	 *
	 * @return mixed
	 *
	 * @throws AppException
	 */
	public static function __callStatic(string $method, array $arguments)
	{
		return app()->make(static::getFacadeAccessor())->$method(...$arguments);
	}

	/**
	 * Call handler
	 *
	 * @param string $method
	 * @param array $arguments
	 *
	 * @return mixed
	 * 
	 * @throws AppException
	 */
	public function __call(string $method, array $arguments)
	{
		return app()->make(static::getFacadeAccessor())->$method(...$arguments);
	}
}
