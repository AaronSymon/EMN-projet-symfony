function modifier(n) {
    var label = document.getElementById("labelSite"+n);
    label.innerHTML="(ENTER to valid)";
    let cell = document.getElementById("colNom"+n);
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("id", "formModif");
    var change = document.createElement("input");
    change.setAttribute("type", "text");
    change.setAttribute("name", "newNom");
    change.setAttribute("value", document.getElementById("SiteNom"+n).value);
    var hidIn = document.createElement("input");
    hidIn.setAttribute("type","hidden");
    hidIn.setAttribute("name","siteNom");
    hidIn.setAttribute("value",document.getElementById("SiteNom"+n).value);
    cell.append(form);
    form.append(change);
    form.append(hidIn);
    change.onkeyup = function (e) {
        if (e.key == 'Enter') {
            document.getElementById('formModif').form.submit();
        }
    }
    document.getElementById("btnModif"+n).style.display = "none";
}

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