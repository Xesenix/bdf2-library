<?php
namespace BDF2\Resources
{
	class PathHelper {
		
		function joinPaths()
		{
			return preg_replace('#/+#', '/', join('/', func_get_args()));
		}
	}
	
}
