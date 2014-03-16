<?php

//	if ($_GET['k'] != 'w6GSgy3x-2inxzDun-5rwvMXPE-SfHpxyJU')
		die ('wrong key');
		$rootdir = '../../';

if (is_dir($rootdir . 'includes')) {
	// Drupal 7
	require_once($rootdir . 'sites/default/settings.php');
	$db = $databases['default']['default'];
	$db_host = $db['host'];
	$db_username = $db['username'];
	$db_password = $db['password'];
	$db_db = $db['database'];
}
if (is_dir($rootdir . 'typo3')) {
	// TYPO3 pre 5.0
	$conffile = $rootdir . 'typo3conf/localconf.php';
	if (is_file($conffile)) {
		include($conffile); 
		$db_host = $typo_db_host;
		$db_username = $typo_db_username;
		$db_password = $typo_db_password;
		$db_db = $typo_db;	
	} else {
		// TYPO3 6.0 and post
		$conffile = $rootdir . 'typo3conf/LocalConfiguration.php';
		if (is_file($conffile)) {
			$c= (include($conffile)); 
			$db_host = $c['DB']['host'];
			$db_username = $c['DB']['username'];
			$db_password = $c['DB']['password'];
			$db_db = $c['DB']['database'];	
		}	
	}	
}
// ---

	$command = isset($_GET['a']) ? $_GET['a'] : '';
	$path = isset($_GET['p']) ? $_GET['p'] : '';
	
	$root = '../../';
	
	if (substr($path, 0, 1) == '/')
		$path = substr($path, 1);

  // this MAY cause problems, but for now, better safe than sorry
  if (preg_match('/deploy/i', $path))
  	die ('not allowed');

	$fullpath = $root . $path;
		
	switch ($command) {
		case 'down' :
			if (is_file($fullpath))
				readfile($fullpath);

			if (is_dir($fullpath)) {
				if (substr($fullpath, -1) != '/')
					$fullpath .= '/';
				$dh = opendir($fullpath);
				$out = array();
				while ($file = readdir($dh)) {
					if ($file == '.' || $file == '..')
						continue;
					if ($file == 'deploy')
						continue;
						
					if (is_file($fullpath . $file))
						$out[] = 'f;' . $file;
					if (is_dir($fullpath . $file))
						$out[] = 'd;' . $file;
				}
				die(implode("\n", $out));
			}
			
			break;
		case "up" :
		
			break;
			
		case "dumpdb" :
			backup_tables($db_host, $db_username, $db_password, $db_db, '*');

			break;
			
		  
	}
	
	
	
	
	/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*')
{
	
	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($name,$link);
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	$handle = fopen('db.sql', 'w+');
	fwrite($handle, $return);
	fclose($handle);

	copy('db.sql', strtolower(strftime('db-%a.sql', time())));

	echo 'OK';
}
