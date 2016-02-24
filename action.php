<?php
/* Arborling : Analayse arborée
 * 
 * Copyright 2016
 * Authors : Laurent VANNI, Alexandre CIARAFONI
 * License: GNU-GPL Version 3 or greater
 * 
 *  Arborling is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Arborling is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Arborling.  If not, see <http://www.gnu.org/licenses/>.
 */
 ?>

<?php
require_once 'index.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');

$resultMatrice = "";
$resultLabel = "";

// GET DATA
if (isset($_POST["matrix"])) 
{
	$matrix = $_POST["matrix"];
	$textLabel = $_POST["textLabel"];

	// START CLASSIFICATION

	$request_id = uniqid();

	// $distanceMatrix = file_get_contents($request_id . ".res");
	// $log = file_get_contents($request_id . ".res");
	$distanceMatrix = $matrix;

	file_put_contents($request_id . ".txt", $distanceMatrix);
	exec('python ./engine/arboree.py ' . $request_id . ".txt > " . $request_id . ".res");
	$log = file_get_contents($request_id . ".res");
	$log = str_replace("\n", "<br />", $log);

	//$tree = file_get_contents($request_id . ".res");
	$tree = file_get_contents("test_data.json");
	echo "<script>var coordonnee = " . $tree . "</script>";
	exec('rm ' . $request_id . ".*");

}
else
{
	//DEFAULT DATA
	$matrix = "0 1 1 3 2 1 3
1 0 2 4 1 2 4
1 2 0 2 3 5 2
3 4 2 0 1 3 1
2 1 3 1 0 2 6
1 2 5 3 2 0 2
3 4 2 1 6 2 0";
	$textLabel = "A B C D E F G H I";
	$tree = file_get_contents("test_data.json");
	$log = file_get_contents("test_data.res");
	$log = str_replace("\n", "<br />", $log);
	echo "<script>var coordonnee = " . $tree . "</script>";
}

?>