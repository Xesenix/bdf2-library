<?php
namespace BDF2\Avatar\Provider;

interface AvatarProviderInterface {
	function getAvatar($id, $size);
}