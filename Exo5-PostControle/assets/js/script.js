/*///////////////////////////////////////////////////////////////////////////*/
/*                                                                           */
/*  Cirill Ferrier                   /\       | |            /\        | |   */
/*                                  /  \   ___| |__   ___   /  \   _ __| |_  */
/*  05/02/2018                     / /\ \ / __| '_ \ / _ \ / /\ \ | '__| __| */
/*  Cirill@asheart.fr             / ____ \\__ \ | | |  __// ____ \| |  | |_  */
/*  www.AsheArt.fr               /_/    \_\___/_| |_|\___/_/    \_\_|   \__| */
/*                                                                           */
/*///////////////////////////////////////////////////////////////////////////*/


//Var globale 

var ligne = 0;


/*Pour effiter les probleme de childNode dans le code; à la création de la page
 *la fonction ajoutlign mis à la fin de Index.php envoie dans le code sous forme
 *compate dans L'obj id="boncom". Cela permet ansi de modifier l'intérieur des Td 
 *plus facilement que si le code HTML etais rendu compacte manuellement */

//Ajoute une ligne au formulaire
function ajouteLigne() {
    obj = document.getElementById('boncom');
    nvLigne = obj.insertRow(-1);
    nvLigne.innerHTML = '<tr>' +
        '<td>' + generationDataListe() +'</td>' +
        '<td><input class="prx"         type="text"     name="puht[]"       value="0"                                                                                                                      disabled required /></td>' +
        '<td><input class="qte"         type="text"     name="qte[]"        value="1"           onblur= "calculeQqt(this.parentNode.parentNode) ; this.value = parseFloat(this.value); calcule();"                  required /></td>' +

        '<td><input class="bpx"         type="button"                       value="-"           onclick="decQte(this.parentNode.parentNode)     ; calculeQqt(this.parentNode.parentNode); calcule();"               required /></td>' +
        '<td><input class="bpx"         type="button"                       value="+"           onclick="incQte(this.parentNode.parentNode)     ; calculeQqt(this.parentNode.parentNode); calcule();"               required /></td>' +

        '<td><input class="ttL"         type="text"                         value="0"                                                                                                                      disabled required /></td>' +

        '<td><input class="bpx rouge"   type="button"                       value="X"           onclick="suppLigne(this.parentNode.parentNode);"                                                                             /></td>' +
    '</tr>';
    //tmp = "tr" + ligne;
    //nvLigne.setAttribute("id", tmp);
    ligne++;
}

//Incrémente la qté de produit dont le numéro de ligne est passé en paramètre ++
function incQte(objTR) {
    objTD = objTR.childNodes[2];
    objinput = objTD.childNodes[0];
    value = parseInt(objinput.value) + 1;
    objinput.value = value;

}

//Décrémente la qté de produit dont le numéro de ligne est passé en paramètre --
function decQte(objTR) {
    objTD = objTR.childNodes[2];
    objinput = objTD.childNodes[0];
    value = parseInt(objinput.value) - 1;
    if (!value == 0) {
        objinput.value = value;
    }
}

//Calcule les différent TVA / TTC etc.....
function calcule() {
    var TotalHT = 0;
    var TotalTTC = 0;
    var TTligne = 0;

    var tabLignes = document.getElementById("boncom").rows;
    var longueur = tabLignes.length;

    for (i = 1; i < longueur; i++) {
        var objTD = tabLignes[i].childNodes[5];
        var objinput = objTD.childNodes[0];
        var TTligne = TTligne + parseFloat(objinput.value);
    }

    var objHT = document.getElementById("ht");
    objHT.value = precisionRound(TTligne, 2);

    var objTTC = document.getElementById("ttc");
    objTTC.value = precisionRound(TTligne * 1.20, 2);

    var objpht = document.getElementById("pht");
    objpht.value = precisionRound(objTTC.value - TTligne , 2);
}

function calculeQqt(objTr) {
    //alert("I am a Drawf");
    var objTD = objTr.childNodes[2];
    var objinput = objTD.childNodes[0];
    var qqt = parseFloat(objinput.value);

    var objTD = objTr.childNodes[1];
    var objinput = objTD.childNodes[0];
    var ppU = parseFloat(objinput.value);

    var ttTr = precisionRound(qqt * ppU, 2);

    var objTD = objTr.childNodes[5];
    var objinput = objTD.childNodes[0];
    objinput.value = ttTr;




}
// --------------------------------//

//Supprime la ligne
function suppLigne(Ligne) {
    //champ = "tr"+ numLigne;
    //console.log(champ);
    //obj = document.getElementById(champ);
    if (ligne != 1) {
        Ligne.remove();
        ligne--;
        calcule();
    }
    else {
        alert("Interdi de supprimer cette ligne");
    }
}


// --------------  developer.mozilla.org  ----------------- //
// Arondi la "number au "Precision" nombre après la vigule //
function precisionRound(number, precision) {
    var factor = Math.pow(10, precision);
    return Math.round(number * factor) / factor;
}



////////////////////////////////////////////////////////////////////////////
//**                         M I W  2 0 1 9                              **/
////////////////////////////////////////////////////////////////////////////

// ZONE CC PRODUIT
var produit = [
    {'libProd' : 'PC'           , 'prxProd' : 1000  },
    {'libProd' : 'Imprimante'   , 'prxProd' : 80    },
    {'libProd' : 'Moniteur'     , 'prxProd' : 150   },
    {'libProd' : 'Cable'        , 'prxProd' : 20    }
];

function generationDataListe() {
    var dataliste = '<select onchange = "appliquePrix(this.parentNode.parentNode,this.value); calculeQqt(this.parentNode.parentNode) ;calcule()" required>';
    dataliste += '<option value="" disabled selected>---</option>';
    produit.forEach(element => {
        dataliste += '<option value="' + element['prxProd'] + '">' + element['libProd'] +'</option>';
    });
    return dataliste + '</select>';
}


function appliquePrix(objTr, prxProd) {
    console.log(prxProd);
    var objTD          = objTr.childNodes[1];
    console.log(objTD);
    var objinput       = objTD.childNodes[0];
    console.log(objinput);
        objinput.value = prxProd;
}

// ZONE VERIF PRODUIT

function verif() {
    VerifNoVide();
    VerifCodePostal();
    VerifNumeroTel();
    VerifEmail();
}

function VerifNoVide() {
    var idd = document.getElementById("idd");
    var allInput = idd.getElementsByTagName("input");
    let err = '';

    for (let i = 0; i < allInput.length; i++) { 
        if (allInput[i].value != '') {
            allInput[i].style.background = '#4CAF50';
        } 
        else {
            allInput[i].style.background = '#ff0000';
            err = 'err';
        }
    }
    if (err != '') { alert('Un Champ n\'est pas rempli');  }
}

function VerifCodePostal() {
    var idd = document.getElementById("idd");
    var allInput = idd.getElementsByTagName("input");
    let err = '';
    let reg = RegExp('^[0-9]{5}$');

    if (reg.test(allInput['CP'].value) ) {
        allInput['CP'].style.background = '#4CAF50';
    }
    else {
        allInput['CP'].style.background = '#ff0000';
        err = 'err';
    }

    if(err != '') { alert('Le code postal n\'est pas valide'); }
}

function VerifNumeroTel() {
    var idd = document.getElementById("idd");
    var allInput = idd.getElementsByTagName("input");
    let err = '';
    let reg = RegExp('(^[0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2}$|^[0-9]{2}\-[0-9]{2}\-[0-9]{2}\-[0-9]{2}\-[0-9]{2}$)');

    if (reg.test(allInput['tel'].value)) {
        allInput['tel'].style.background = '#4CAF50';
    }
    else {
        allInput['tel'].style.background = '#ff0000';
        err = 'err';
    }

    if (err != '') { alert('Le numero de telephone n\'est pas valide'); }
}

function VerifEmail() {
    var idd = document.getElementById("idd");
    var allInput = idd.getElementsByTagName("input");
    let err = '';
    let reg = RegExp('^[\\w-_.]+\\@\\w+\.[\\w]{2,4}$');

    if (reg.test(allInput['mail'].value)) {
        allInput['mail'].style.background = '#4CAF50';
    }
    else {
        allInput['mail'].style.background = '#ff0000';
        err = 'err';
    }

    if (err != '') { alert('L\' adresse mail n\'est pas valide'); }
}