<?PHP
	//should be cleaned up and moved to config file
//error codes
	$errormessage=array(" ","Url not found","Error");
	$hostname = "localhost";
	$username = "";
	$password = "";
	
	
	$commandArray = explode("/",$_SERVER['REQUEST_URI']);
	array_shift($commandArray);
	$theCommand = array_shift($commandArray);


	try{
		$db = new PDO("mysql:host=$hostname;dbname=dvlp_se",$username,$password);
		if(ctype_xdigit($theCommand))
		{
			//det här är en url som vi måste ta hand om
			$sql = "select * from url where id = ".hexdec($theCommand);
			
			foreach($db->query($sql) as $row)
			{
				if(dechex($row['id']) == $theCommand)
				{
					$db->query("update url set visits = visits+1 where id = ".$row['id']);
					header("location: http://".$row['long']);

				}
			}
			$error = 1;

		}
		elseif($theCommand == "new")
		{
			if($_GET['url'] != "http://" && $_GET['url'] != "")
			{
				$old = 0;
				$api = 0;
				if(isset($_GET['api']))
					$api =  $_GET['api'];
				if(isset($_GET['callback']))
					$callback = $_GET['callback'];
				//först kolla om den redan finns
				$_GET['url'] = str_replace("http://","",$_GET['url']);
				$_GET['url'] = str_replace("https://","",$_GET['url']);
				$_GET['url'] = str_replace(" ","",$_GET['url']);
				$theUrl = $_GET['url'];
			
				$sql = "select * from url where `long` ='$theUrl'";
				foreach($db->query($sql) as $currentUrl)
				{
					if($currentUrl['long'] == $_GET['url'])
					{
						$newUrl = dechex($currentUrl['id']);
						$old = 1;
					}	
				}
				if(!$old)
				{	
					//lägg till ny länk
					$db->exec("INSERT INTO url VALUES(null,'${_GET['url']}',0)");
					$newUrl = dechex($db->lastInsertId());
				}
			}
		}
		elseif($theCommand =="stats")
		{
			//kolla stats för en länk
			$short = array_shift($commandArray);
			$sql = "select * from url where id=".hexdec($short);
			foreach($db->query($sql) as $link)
			{
				echo "you have ".$link['visits']." visits to this link";
			}
		}
		else{
		//ladda huvudsidan
			$db = null;
	
			echo file_get_contents("design.html");

		}
	}
	catch(PDOException $e)
	{
	echo $e->getMessage();
	}

	if($error)
		echo $errormessage[$error];

	
	if($newUrl && $api == 0)
		echo "Din nya url är: <a href=\"http://dvlp.se/$newUrl\">http://dvlp.se/$newUrl</a><br><br>Dela den på: <a href=\"http://twitter.com/home?status=http://dvlp.se/$newUrl\">twitter</a> | <a href=\"http://www.new.facebook.com/sharer.php?u=http://dvlp.se/$newUrl\">Facebook</a>";
	elseif($newUrl && $api == 1)
		echo "http://dvlp.se/$newUrl";

	elseif($newUrl && $api == 2)
		echo "{\"URI\":\"http://dvlp.se/$newUrl\"}";
	elseif($newUrl && $api == 3)
		echo $callback."({\"URI\":\"http://dvlp.se/$newUrl\"})";
?>
