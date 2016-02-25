<?php
require_once 'index.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');

$display = "";
$displayNone = "";
//IMPORT CSV
if(isset($_FILES['doccsv']))
{
	$matrix = "";
	$textLabel = "";
	$elmCSV = array();
	$resultLabel = "";
	$resultMatrice = "";
	$row = 0;
	$displayNone = "display: none";
	$display = "display: block";
	$fname = $_FILES ["doccsv"] ["name"];
	$chk_ext = explode(".", $fname);
	//IF CSV FILE
	if(strtolower(end($chk_ext)) == "csv")
	{
		$filename = $_FILES ["doccsv"] ["tmp_name"];
		$handle = fopen($filename, "r");
		//BROWSE THE FILE LINE BY LINE
		while(($data = fgetcsv($handle, 1000, ",")) !== false)
		{
			array_push($elmCSV, $data);
			$row++;
		}
		fclose($handle);
		//FILLED THE TEXTLABEL AND TEXTAREA WITH THE CSV DATA
		//FOR A LINE
		for($i = 0; $i < $row; $i++)
		{	//FOR AN ELEMENT OF THE LINE
			for($j = 0; $j < $row; $j++)
			{	//IF FIRST LINE, FILL THE TEXTLABEL
				if($i == 0)
				{	//IF $resultLabel EMPTY ADD NEW ELEMENT
					if($resultLabel == "")
					{
						$resultLabel = $elmCSV[$i][$j];
					}//ELSE ADD SPACE AND NEW ELEMENT
					else
					{	//IF NOT THE LAST ELEMENT ADD SPACE AND NEW ELEMENT
						if($j !== $row - 1)
						{
							$resultLabel = $resultLabel. ' ' .$elmCSV[$i][$j];
						}
					}
				}//ELSE, FILL THE TEXTAREA
				else
				{	//IF LAST ELEMENT OF THE LINE, MAKE A NEW LINE
					if($j == $row - 1)
					{
						$resultMatrice = $resultMatrice.PHP_EOL;
					}//ELSE ADD SPACE AND NEW ELEMENT
					else
					{
						$resultMatrice = $resultMatrice. ' ' .$elmCSV[$i][$j];
					}
				}
			}
		}
		$class = "success";
		$erreur = "Upload réussie !";
	}
	else
	{
		$class = "alert";
		$erreur = "Fichier invalide !";
	}
}
?>