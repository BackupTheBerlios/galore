<?
function list_dir($dirname)
{
	if($dirname[strlen($dirname)-1]!='\\')
		$dirname.='\\';
	static $result_array=array();  
	$handle=opendir("./");
	while ($file = readdir($handle))
	{
		if($file=='.'||$file=='..')
			continue;
		if(is_dir($dirname.$file))
			list_dir($dirname.$file.'\\'); 
		else
			$result_array[]=$file;
	}	
	closedir($handle);
	return $result_array;
}

foreach (list_dir("./") as $v) {
	print "<a href=\"$v\">$v</a><br>";
}

?>
