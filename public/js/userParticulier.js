/*
 * Fichier contenant les actions possibles par un utilisateur connecté de type 'particulier'
 */

/*
 * classe correspondant à l'event affiché lorsqu'on survole la mallette dans la navigation
 */
class ContratEvent {
    constructor(id, nom, ville, date) {
        if (!nom.length > 0) nom = 'Annonce n°' + id;
        date = date.substr(0, 10).split('-');
        date = date[2] + '-' + date[1] + '-' + date[0];
        let url = "{{ path('particulier_show_annonce', {'id': 1}) }}".slice(0, -1) + id;

        this.contratEventTemplate = `
        <div class="event">
            <div class="label">
            </div>
            <div class="content">
                <div class="date">
                    ${date}
                </div>
                <div class="summary">
                    ${nom} à ${ville}.
                </div>
            </div>
        </div>`;
    }
}

window.addEventListener("load", function () {
    loadContratsAmount()
});

// Charge le nombre de contrats en pending
function loadContratsAmount() {
    $.ajax({
        url: '/particulier/get/my/candidatures',
        type: 'POST',
        dataType: 'json',
        success: function (results) {
            displayContratsFeed(results);
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

// Affiche les propositions de contrat dans la nav au survol de la mallette
var notificationsFeed = document.getElementById('notifications');
var contratsAmount = document.getElementById('contratsAmount');

function displayContratsFeed(results) {
    console.log(results)

    // Change la quantité de candidatures de contrat dans la case
    contratsAmount.innerHTML = results.candidatures.length;

    // Réinitialise la liste
    notificationsFeed.innerHTML = '';

    // Il n'y a pas de résultats :
    if (results.candidatures === undefined || results.candidatures.length == 0) {
        notificationsFeed.innerHTML = `<div>Pas de nouvelle proposition de contrat.</div>`;
    }

    // Il y a des résultats :
    results.candidatures.forEach(candidature => {
        let card = new ContratEvent(candidature.annonce.identity, candidature.annonce.nom, candidature.annonce.adresse.ville, candidature.annonce.createdAt);
        notificationsFeed.innerHTML += card.contratEventTemplate;
    })
}