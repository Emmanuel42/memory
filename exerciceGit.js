

$(function(){

    function perimetre(l,h){
        return (l+h)*2;
    }

    function aire(l,h){
        return l*h;
    }
    
    $('#calculer').click(function(){
        let largeur=parseInt($('#largeur').val());
        let longueur=parseInt($('#longueur').val());
        $('p').text("Périmètre : " + perimetre(largeur,longueur)+" / Aire : "+aire(largeur,longueur));
    })
})


function conversion (jours, heures, minutes){

	return (jours*24*60*60) + (heures*60*60) + (minutes*60);

}

	$('#conversion').click(function(){
		let jours = parseInt($('#jours').val());
		let heures = parseInt($('#heures').val());
		let minutes = parseInt($('#minutes').val());
		$('#p2').text("Temps en secondes : " + conversion(jours, heures, minutes));

})
