<?php
namespace BDF2\Avatar\Provider;

interface AvatarCompositProviderInterface extends AvatarProviderInterface {
	function register(AvatarProviderInterface $provider);
}