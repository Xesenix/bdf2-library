<?php
namespace BDF2\Resources\Loader;

use Symfony\Component\Config\Loader\DelegatingLoader as BaseDelegatingLoader;
use Symfony\Component\Config\Exception\FileLoaderLoadException;

class DelegatingLoader extends BaseDelegatingLoader
{

	public function unload($resource, $type = null) {
		if (false === $loader = $this->resolver->resolve($resource, $type))
		{
			throw new FileLoaderLoadException($resource);
		}

		if (method_exists($loader, 'unload'))
		{
			return $loader->unload($resource, $type);
		}

		return false;
	}

}
