
// let larg = document.getElementbyId("largeur");
// let haut = document.getElementbyId("hauteur");
// let button = document.getElementbyId("valider");

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

