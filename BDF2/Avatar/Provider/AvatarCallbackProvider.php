<?php
namespace BDF2\Avatar\Provider;

class AvatarCallbackProvider implements AvatarProviderInterface
{
	protected $priority = 0;
	
	protected $callback = null;
	
	public function __construct($callback, $priority = 0)
	{
		$this->callback = $callback;
		$this->priority = $priority;
	}
	
	public function getAvatar($id, $size)
	{
		return call_user_func_array($this->callback, array($id, $size));
	}
}