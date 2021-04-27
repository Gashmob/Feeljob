/*
 * Fichier contenant les actions possibles par un utilisateur connecté
 * ex : candidat, particulier, entreprise, freelance
 */

/*
 * classe correspondant à l'event affiché lorsqu'on survole la mallette dans la navigation
 */
class ContratEvent {
    constructor(id, nom, rue, codeP, ville, createdAt, date, description) {
        if (!nom.length > 0) nom = 'Annonce n°' + id;
        date = date.substr(0, 10).split('-');
        date = date[2] + '-' + date[1] + '-' + date[0];
        let url = "{{ path('particulier_show_annonce', {'id': 1}) }}".slice(0, -1) + id;

        this.contratEventTemplate = `
        <div class="event">
            <div class="label">
                <img src="{{ asset('img/placeholders/matt.jpg') }}">
            </div>
            <div class="content">
                <div class="date">
                    Il y a 3 jours
                </div>
                <div class="summary">
                    Matt vous a envoyé un message.
                </div>
            </div>
        </div>`;
    }
}

// Le DOM est chargé
window.addEventListener("DOMContentLoaded", function () {
    loadContratsAmount()
});

// Charge le nombre de contrats en pending
function loadContratsAmount() {
    makeRequestByORSC('/particulier/get/propositions', changeContratsAmount);
}

// Update le compteur du nombre de contrats
const contratsAmount = document.querySelector("#contratsAmount")
function changeContratsAmount() {
    if (httpRequest.readyState === XMLHttpRequest.DONE) {
        if (httpRequest.status === 200) {
            // stocke les résultats parsé en JSON dans une variable
            results = JSON.parse(httpRequest.responseText)
            contratsAmount.innerHTML = results.propositions.length;
        }
    }
}

// Charge les propositions de contrat au survol de l'icone de mallette dans la nav
function loadContratsFeed() {
    makeRequestByORSC('/particulier/get/propositions', displayContratsFeed);
    contratsAmount.innerHTML = '0';
}

// Tronque les descriptions
const MAX_DESCRIPTION_LENGTH = 300;
function truncate(str, n, useWordBoundary
) {
    if (str.length <= n) {
        return str;
    }
    const subString = str.substr(0, n - 1); // the original check
    return (useWordBoundary
        ? subString.substr(0, subString.lastIndexOf(" "))
        : subString) + " &hellip;";
};

// Créé une requête xmlhttp
let httpRequest, results;
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
function displayContratsFeed() {
    //En cours de chargement
    if (httpRequest.readyState === XMLHttpRequest.LOADING) {
        notificationsFeed.innerHTML = `<i class="notched circle loading icon"></i>`;
    }
    //Chargé
    else if (httpRequest.readyState === XMLHttpRequest.DONE) {
        if (httpRequest.status === 200) {
            // stocke les résultats parsé en JSON dans une variable
            results = JSON.parse(httpRequest.responseText)
            // results = httpRequest.responseText
            // Réinitialise la liste
            notificationsFeed.innerHTML = '';

            // Il n'y a pas de résultats :
            if (results.propositions === undefined || results.propositions.length == 0) {
                notificationsFeed.innerHTML = `<div>Pas de nouvelle proposition de contrat.</div>`;
            }

            // Il y a des résultats :
            // Pour chaque cv du tableau propositions
            results.propositions.forEach(a => {
                let card = new ContratEvent(a.identity, a.nom, a.adresse.rue, a.adresse.codePostal, a.adresse.ville, a.createdAt, a.date, a.description);
                notificationsFeed.innerHTML += card.contratEventTemplate;
            })
        } else {
            notificationsFeed.innerHTML = `<div>Il y a eu un problème avec la requête.</div>`;
        }
    }
}