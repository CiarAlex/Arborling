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
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Analyse Arborée</title>
		<link rel="stylesheet" href="lib/foundation-6/css/foundation.min.css" />
		<link rel="stylesheet" href="lib/foundation-icons/foundation-icons.css">
    	<link rel="stylesheet" href="lib/foundation-6/css/app.css" />
		<link rel="stylesheet" type="text/css" href="arborling.css">
		<link rel="stylesheet" type="text/css" href="tree.css">
		<link rel="stylesheet" href="lib/joyride-master/css/joyride-2.1.css" />		
		<?php include 'action.php' ?>
		<?php include 'uploadCSV.php'?>
		
		<!-- Google analystics -->
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', 'UA-73386595-1', 'auto');
		  ga('send', 'pageview');
		</script>
	</head>
	<body>
		<!-- HEADER -->
		<div class="top-bar topbar">
			<div class="top-bar-left">
				<span data-responsive-toggle="responsive-menu" data-hide-for="medium"></span>
				<h4 class="title">Arborling</h4>
			</div>
			<div id="responsive-menu" class="top-bar-right">
				<a target="blank" title="Laboratoire: Bases, Corpus, Langage" href="http://bcl.cnrs.fr">
					<img alt="bcl" src="img/logo_BCL.png">
				</a>
				<a target="blank" title="CNRS" href="http://www.cnrs.fr">
					<img alt="cnrs" src="img/logo_cnrs_fil.png">
				</a>
				<a target="blank" title="Université Nice Sophia-Antipolis" href="http://www.unice.fr">
					<img alt="unice" src="img/logo_unice_fil.png">
				</a>
		   	</div>
		</div>
		<!-- CONTENT -->
		<div id="container" class="container row medium-uncollapse large-collapse">
			<br>
			<div class="columns large-6 leftColumn" >
				<p class="littleTitle">
					<b>DONNÉES</b>
				</p>
				<div id="textLabelMatrice">
					<form id="matriceform" action="#" method="POST" enctype="multipart/form-data">
						<input id="textLabel" name="textLabel" type="text" class="textLabel" placeholder="Étiquettes de l'arbre" value="<?php echo $textLabel; echo $resultLabel;?>">
						<textarea id="textareaMatrice" name="matrix" class="matrice" placeholder="Écrivez vos données ici"><?php echo $matrix; echo $resultMatrice; ?></textarea>
					</form>
				</div>
				<div class="row">
					<div class="columns small-6">
						<form id="uploadCSV" action="#" method="POST" enctype="multipart/form-data">
							<div class="button-group">
								<input id="fichier" type="file" name="doccsv" onchange="document.getElementById('uploadCSV').submit();" style="display: none;">
								<input id="buttonFile" class="button" type="button" value="Importer CSV" onclick="document.getElementById('fichier').click();">
								<span id="labelError" class="<?php echo $class?> hollow button error" style="<?php echo $display;?>;"><?php echo $erreur;?></span>
							</div>
						</form>
					</div>
					<div class="columns small-6">
						<div class="expanded button-group" style="text-align: right;">
							<input id="buttonSend" type="button" class="button" name="buttonSend" onclick="document.getElementById('matriceform').submit();" value="Envoyer">
							<input id="buttonLog" type="button" class="button" value="Log">
							<input id="buttonHelp" type="button" class="button" value="Aide">
						</div>
					</div>
				</div>
			</div>
			<div class="columns large-6 rightColumn">
				<p class="littleTitle">
					<b>RÉSULTATS</b>
				</p>
				<div class="columns small-12" style="<?php echo $displayNone; ?>;">
					<div id="littleTree">
						<a href="#" id="buttonBigTree" type="button" class="button fi-arrows-out" title="Agrandir l'arbre" data-open="modalBigTree"></a>
					</div>
					<div id="log" class="log">
						<div id="topLog" class="topLog">
							<button id="buttonExitLog" class="close-button">
								<span>&times;</span>
							</button>
						</div>
						<div class="botLog">
							<?php echo $log; ?>
						</div>
					</div>
				</div>
				<div id="modalBigTree" class="full reveal bigTree row" data-reveal>
					<div id="options" class="columns small-2 options">
						<ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
					        <li class="accordion-item" data-accordion-item>
					          <a class="accordion-title">Couleurs</a>
					          <div class="accordion-content" data-tab-content>
					          	<label>Branches :</label>
					          	<input id="newColorBranch" value="#8B4513" type="color">
					          	<label>Feuilles :</label>
					          	<input id="newColorLeaf" value="#008000" type="color">
					          	<label>Texte :</label>
							    <input id="newColorText" value="#000000" type="color">
							    <label>Cercles :</label>
							    <input id="newColorCircle" value="#000000" type="color">
							    <label>Fond :</label>
							    <input id="newColorBackground" value="#FFFFFF" type="color">
					          </div>
					        </li>
					        <li class="accordion-item" data-accordion-item>
					        	<a class="accordion-title">Taille du texte</a>
					        	<div class="accordion-content" data-tab-content>
						        	<select id="textSize">
						        		<option value="small">Petit</option>
						        	  	<option value="xx-small">Très petit</option>
							          	<option value="medium">Moyen</option>
							          	<option value="large">Grand</option>
							          	<option value="x-large">Très grand</option>
							        </select>
					        	</div>
					        </li>
					        <li class="accordion-item" data-accordion-item>
					        	<a class="accordion-title">Largeur des traits</a>
					        	<div class="accordion-content" data-tab-content>
					        		<input id="newLineSize" class="rangeslider" type="range" min="0" max="10" value="1.5"  step="0.5" onchange="document.getElementById('valueLineSize').value = document.getElementById('newLineSize').value;"></input>
									<input id="valueLineSize" type="text" readonly="readonly" value="1.5">
					        	</div>
					        </li>
					        <li class="accordion-item" data-accordion-item>
					        	<a class="accordion-title">Diamètre des cercles</a>
					        	<div class="accordion-content" data-tab-content>
									<input id="newCircleSize" class="rangeslider" type="range" min="0" max="10" value="2" step="0.5" onchange="document.getElementById('valueCircleSize').value = document.getElementById('newCircleSize').value;"></input>
									<input id="valueCircleSize" type="text" readonly="readonly" value="2">
					        	</div>
					        </li>
					     </ul>
					</div>
					<div class="columns small-2 buttonSaveTree">
						<a id="buttonSaveTree" type="button" class="button" title="Sauvegarder votre arbre sous forme d'image">Enregistrer</a>
					</div>
					<button id="buttonExitBigTree" class="close-button buttonExitBigTree" data-close aria-label="Close modal" type="button">
						<span aria-hidden="true">&times;</span>
			    	</button>
				</div>
			</div>
			<span class="fi-arrow-right arrowMid"></span>
			<!-- BUTTON HELP - JOYRIDE -->
			<div style="display: none">
				<ol id="joyrideHelp" >
					<li data-id="textLabel" data-button="Suivant">
	    				<h2>Étiquettes</h2>
	    				<p>Vous pouvez choisir les valeurs des feuilles de votre arbre</p>
					</li>
					<li data-id="textareaMatrice" data-button="Suivant" data-options="tipLocation:right;">
				    	<h2>Matrice</h2>
					    <p>Entrez vos données pour construire votre arbre</p>
					</li>
					<li data-id="buttonFile" data-button="Suivant" data-options="tipLocation:top;">
						<h2>Importer</h2>
						<p>Vous avez la possibilité d'importer votre fichier csv.<br> Attention ! La première ligne de votre fichier sera considérée comme les étiquettes de l'arbre</p>
					</li>
					<li data-id="buttonSend" data-button="Suivant" data-options="tipLocation:top;">
						<h2>Calculs</h2>
						<p>Traitement des données et construction de l'arbre</p>
					</li>
					<li data-id="buttonLog" data-button="Suivant" data-options="tipLocation:top;">
						<h2>Trace</h2>
					    <p>Vous pouvez aussi afficher les détails des calculs effectués à partir de votre matrice. Vous pouvez déplacer ce contenu en restant cliqué sur le haut de celui-ci</p>
					</li>
					<li data-id="littleTree" data-button="Suivant" data-options="tipLocation:left;">
						<h2>Votre arbre !</h2>
					    <p>Vous pouvez déplacer les branches en cliquant sur les points ou les mots, et agrandir l'arbre en cliquant sur l'icone en haut à droite</p>
					</li>
					<li data-button="Fin">
					  	<p>Vous savez maintenant comment utiliser Arborling !</p>
					</li>
		  		</ol>
			</div>
		</div>
		<!-- FOOTER -->
		<footer>
			<div class="row">
				<div class="liens">
					<a target="blank" href="#">Arborling</a> -
					<a target="blank" href="http://logometrie.unice.fr/">Logométrie</a> -
					<a target="blank" href="#">Mentions légales</a> -
					<a target="blank" href="http://bcl.cnrs.fr/?redirected_from=www.unice.fr/&lang=en">UMR 7320 : Bases, Corpus, Langage</a> -
					<a href="mailto:laurent.vanni@unice.fr">Contact</a>
				</div>
			</div>
		</footer>
		<script src="lib/foundation-6/js/vendor/jquery.min.js"></script>
		<script src="lib/foundation-6/js/vendor/what-input.min.js"></script>
		<script src="lib/foundation-6/js/jquery-ui.min.js"></script>
		<script src="lib/foundation-6/js/foundation.min.js"></script>
		<script src="lib/foundation-6/js/app.js"></script> 
		<script src="lib/d3/d3.min.js"></script>
		<script src="lib/joyride-master/js/jquery.joyride-2.1.js"></script>
		<script src="arborling.js"></script>
		<script> arboree(); </script>
	</body>
</html>