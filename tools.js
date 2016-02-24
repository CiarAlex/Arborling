/**
 * Vérification matrice carrée
 */

var mon_tab = [],
	tabbool = [];
var coef = 1;
var mon_bool = false;
function ValideMatrice(){
	//Récupération du contenu du textarea
    var matrice = $("#divTextarea").find("textarea").val().trim();
    var res = matrice.replace(/\n/g, " ").split(/\s+/);
    mon_tab = res;
    //Lecture et comparaison entre lignes et colonnes
    for(var i = Math.sqrt(mon_tab.length) + 1; i < mon_tab.length; i += Math.sqrt(mon_tab.length) + 1)
    {
    	for(var j = 1; j <= coef; j++)
    	{
    		if(typeof mon_tab[i - 1*j] === 'undefined' || typeof mon_tab[i - Math.sqrt(mon_tab.length)*j] === 'undefined')
        	{
        		alert("Vérifiez qu'il s'agisse bien d'une matrice carrée");
        	}
        	else
    		{
        		if(mon_tab[i - 1*j] == mon_tab[i - Math.sqrt(mon_tab.length)*j])
        		{
        			mon_bool = true;
        		}
        		else
    			{
    				mon_bool = false;
    			}
        		tabbool.push(mon_bool);
    		}
    	}
    	coef++;
    }
    if(jQuery.inArray(false, tabbool) == -1)
	{
    	//arboree();
    	$('#matriceform').submit();
	}
    else
    {
    	alert("Votre matrice n'est pas carrée");
    }
    mon_tab = [];
	tabbool = [];
	coef = 1;

//Récupération des étiquettes
	var tab_label = [];
	var label = $("#divTextarea").find("input", "textLabel").val();
	var etiquette = label.split(/\s+/);
	tab_label = etiquette;
	for(var i = 0; i < tab_label.length; i++)
	{
		coordonnee.nodes[i].label = tab_label[i];
	}
});
