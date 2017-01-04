<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>PhpFiddle Initial Code</title>

<script type="text/javascript" src="/js/jquery/1.7.2/jquery.min.js"></script>

	


</head>

<body>
<form action="" method="GET">
<input type="text" name="query" value='
<?php
if(isset($_GET['query']))
	echo $_GET['query'];
?>'/>
<button type="submit">Wyslij</button>
<br/>


</body>
	<?php
	if(!file_exists('data.txt'))
		{
			$fp = fopen('data.txt','w+');
			fclose($fp);
		}//Tworzenie pliku bazy

	
if(isset($_GET['query']))
{	
	
	
	$url = "http://api.giphy.com/v1/gifs/search?q=".urlencode($_GET['query'])."&api_key=dc6zaTOxFJmzC";
	$a=file_get_contents($url);
	$b=json_decode($a, true); //Przetwarzanie oraz dekodowanie danych z aplikacji
	//print_r($b['data'][0]['id']);

if(!empty($b['data']) && $b['meta']['status']==200)
{	
	$it=0;
	$file = file_get_contents('data.txt');
	$data = explode(",", $file);
	$temp=NULL;
	do
	{

		if(!array_search($b['data'][$it]['id'],$data))
		{
			$temp.=','.$b['data'][$it]['id'].',0';
		}
	$it++;
	}while(!empty($b['data'][$it])); //dodawanie do pliku nie istniejacych rekordow
	
	if(!empty($temp))
	{	
		file_put_contents('data.txt', $temp . $file);
	}
	$file = file_get_contents('data.txt');
	$data = explode(",", $file);
	$i=0;
	do
	{
		if(isset($_GET[$b['data'][$i]['id']]))
		{
			$value = explode(',',$_GET[$b['data'][$i]['id']]);
			if($value[1]=='-1')
			{
				$data[array_search($b['data'][$i]['id'],$data)+1]++;
			}
			else if($value[1]=='-0')
			{
				$data[array_search($b['data'][$i]['id'],$data)+1]--;
			}
			else
			{	
				
				header("Location: http://niemam.000webhostapp.com/");
				
			}
			header("Location: http://niemam.000webhostapp.com/?query=".urlencode($_GET['query']));
			file_put_contents('data.txt',implode(',',$data));
		}// ocenianie dodatnie, ujemne, oraz zabezpieczenie przed proba zepsucia
		echo '<img src="'.$b['data'][$i]['images']['original']['url'].'"/><br/>';
		echo '<button type="submit"  name="'.$b['data'][$i]['id'].'" value="'.$b['data'][$i]['id'].',-1"">Lubie to!</button>';
		echo $data[array_search($b['data'][$i]['id'],$data)+1];
		echo '<button type="submit"  name="'.$b['data'][$i]['id']. '"value="'.$b['data'][$i]['id'].',-0""/>Nie lubie</button>';
		echo '<br/><br/><br/><br/><br/><br/>';
		$i++;

	}while(!empty($b['data'][$i]));//Wyswietlanie

}
else if($b['meta']['status']==403)
{
	echo '<p>Blad polaczenia z baza danych, sprobuj ponownie pozniej</p>';
}// Komunikat bledu polaczenia z baza danych
else
{
	echo '<p>Nie mznaleziono podanej frazy</p>';
}//Komunikat o braku gifow z podanym wyrazeniem
}
	
?>
</form>
</html>
