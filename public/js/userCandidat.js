/*
 * Fichier contenant les actions possibles par un utilisateur connecté
 * ex : candidat, particulier, entreprise, freelance
 */

/*
 * classe correspondant à l'event affiché lorsqu'on survole la mallette dans la navigation
 */
class ContratEvent {
    constructor(id, nom, ville, date) {
        if (!nom.length > 0) {
            nom = 'Offre n°' + id;
        }
        if (!ville.length > 0) {
            ville = ''
        } else {
            ville = ' à ' + ville;
        }
        date = date.substr(0, 10).split('-');
        date = date[2] + '-' + date[1] + '-' + date[0];
        let url = "{{ path('entreprise_show_offre_emploi', {'id': 1}) }}".slice(0, -1) + id;

        this.contratEventTemplate = `
        <div class="event">
            <div class="label">
            </div>
            <div class="content">
                <div class="date">
                   ${date}
                </div>
                <div class="summary">
                    ${nom}${ville}
                </div>
            </div>
        </div>`;
    }
}

// Le DOM est chargé
window.addEventListener("load", function () {
    loadContratsAmount()
});

// Charge le nombre de contrats en pending
function loadContratsAmount() {
    $.ajax({
        url: '/entreprise/get/propositions',
        type: 'POST',
        dataType: 'json',
        success: function (results) {
            displayContratsFeed(results)
        }
    });
}

// Tronque les descriptions
const MAX_DESCRIPTION_LENGTH = 300;

function truncate(str, n, useWordBoundary) {
    if (str.length <= n) {
        return str;
    }
    const subString = str.substr(0, n - 1); // the original check
    return (useWordBoundary
        ? subString.substr(0, subString.lastIndexOf(" "))
        : subString) + " &hellip;";
};

// Créé une requête xmlhttp
var httpRequest, results;

function makeRequestByORSC(url, orscFunction) {
    httpRequest = new XMLHttpRequest();
    if (!httpRequest) {
        alert('Abandon :( Impossible de créer une instance de XMLHTTP');
        return false;
    }
    httpRequest.onreadystatechange = orscFunction;
    httpRequest.open('POST', url);
    httpRequest.send();
}

// Affiche les propositions de contrat dans la nav au survol de la mallette
const notificationsFeed = document.getElementById('notifications');
const contratsAmount = document.getElementById('contratsAmount');

function displayContratsFeed(results) {
    // Change la quantité de propositions de contrat dans la case
    contratsAmount.innerHTML = results.propositions.length;
    // results = httpRequest.responseText
    // Réinitialise la liste
    notificationsFeed.innerHTML = '';

    // Il n'y a pas de résultats :
    if (results.propositions === undefined || results.propositions.length == 0) {
        notificationsFeed.innerHTML = `<div>Pas de nouvelle proposition de contrat.</div>`;
    }

    // Il y a des résultats :
    // Pour chaque proposition
    results.propositions.forEach(proposition => {
        let card = new ContratEvent(proposition.identity, proposition.nom, proposition.lieu.ville, proposition.createdAt);
        notificationsFeed.innerHTML += card.contratEventTemplate;
    })
}