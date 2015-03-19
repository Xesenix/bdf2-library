<?php
namespace BDF2\Avatar\Provider;

class AvatarPriorityProvider implements AvatarCompositProviderInterface
{
	protected $providers = array();
	
	public function register(AvatarProviderInterface $provider)
	{
		$this->providers[] = $provider;
	}
	
	public function getAvatar($id, $size)
	{
		// TODO: sort by priority
		foreach ($this->providers as $provider)
		{
			if (($result = $provider->getAvatar($id, $size)) !== null)
			{
				return $result;
			}
		}
		
		return null;
	}
}