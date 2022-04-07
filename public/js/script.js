function afficher(){
    document.getElementById("btnAjout").style.display ="none";
    document.getElementById("lieuAjout").style.display ="block";
}
function afficherAjout(){
    document.getElementById("divSelVille").style.display ="none";
    document.getElementById("divCreaVille").style.display ="block";
}
function masquerAjout(){
    document.getElementById("divSelVille").style.display ="block";
    document.getElementById("divCreaVille").style.display ="none";
    document.getElementById("cp").value = "";
    document.getElementById("nomV").value = "";
}