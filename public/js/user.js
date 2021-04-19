/*
 * Fichier contenant les actions possibles par un utilisateur connecté
 * ex : candidat, particulier, entreprise, freelance
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

class Contrat {
    constructor(id, nom, date) {
        if (!nom.length > 0) nom = 'Annonce n°' + id;
        date = date.substr(0, 10).split('-');
        date = date[2] + '-' + date[1] + '-' + date[0];
        let url = "{{ path('particulier_show_annonce', {'id': 1}) }}".slice(0, -1) + id;

        this.contratTemplate = `
        <div id="proposition{{ i }}" class="proposition ui vertical segment grid">
            <div class="ui left floated ten wide column">
                <img class="ui left floated mini circular image"
                     src="{{ asset('img/placeholders/matthew.png') }}">
                <div class="header">{{ i }} - Matt Romney</div>
                <div class="meta">
                    <span class="date">15 Février 2021</span>
                </div>
            </div>
            <div class="ui right floated three wide column">
                <a href="#" class="ui blue button btnAccept">Voir</a>
                <!--<button class="ui red button btnDecline" onclick="showModalDecline({{ i }})">Décliner</button>-->
                <button class="ui green button btnAccept" onclick="showModalAccept({{ i }})">Accepter</button>
            </div>
        </div>`;
    }
}

class Candidature {
    constructor(id, nom, date) {
        if (!nom.length > 0) nom = 'Annonce n°' + id;
        date = date.substr(0, 10).split('-');
        date = date[2] + '-' + date[1] + '-' + date[0];
        let url = "{{ path('particulier_show_annonce', {'id': 1}) }}".slice(0, -1) + id;

        this.candidatureTemplate = `
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

function loadContratsFeed() {
    makeRequest('/particulier/get/propositions', displayContratsFeed);
}

function loadContrats() {
    makeRequest('/particulier/get/propositions', displayContrats);
}

function loadCandidatures() {
    makeRequest('/particulier/get/candidatures', displayCandidatures);
}

let httpRequest, results;
// créé une requête xmlhttp
function makeRequest(url, orscFunction) {
    httpRequest = new XMLHttpRequest();
    if (!httpRequest) {
        alert('Abandon :( Impossible de créer une instance de XMLHTTP');
        return false;
    }
    httpRequest.onreadystatechange = orscFunction;
    httpRequest.open('POST', url);
    httpRequest.send();
}

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
            console.log('ok');
            // Réinitialise la liste
            notificationsFeed.innerHTML = '';


            // Il n'y a pas de résultats :
            if (results.annonces === undefined || results.annonces.length == 0) {
                notificationsFeed.innerHTML = `<div>Pas de proposition de contrat.</div>`;
            }

            // Il y a des résultats :
            // Pour chaque cv du tableau propositions
            results.annonces.forEach(a => {
                let card = new ContratEvent(a.identity, a.nom, a.adresse.rue, a.adresse.codePostal, a.adresse.ville, a.createdAt, a.date, a.description);
                notificationsFeed.innerHTML += card.contratEventTemplate;
            })
        } else {
            notificationsFeed.innerHTML = `<div>Il y a eu un problème avec la requête.</div>`;
        }
    }
}

const contratsList = document.getElementById('contratsList');
function displayContrats() {
    //En cours de chargement
    if (httpRequest.readyState === XMLHttpRequest.LOADING) {
        contratsList.innerHTML = `<i class="notched circle loading icon"></i>`;
    }
    //Chargé
    else if (httpRequest.readyState === XMLHttpRequest.DONE) {
        if (httpRequest.status === 200) {
            // stocke les résultats parsé en JSON dans une variable
            results = JSON.parse(httpRequest.responseText)
            console.log(results);
            // Réinitialise la liste
            contratsList.innerHTML = '';

            // Il n'y a pas de résultats :
            if (results.propositions === undefined || results.propositions.length == 0) {
                contratsList.innerHTML = `<div>Pas de proposition de contrat.</div>`;
            }

            // Il y a des résultats :
            // Pour chaque cv du tableau propositions
            results.propositions.forEach(a => {
                let card = new ContratEvent(a.identity, a.nom, a.adresse.rue, a.adresse.codePostal, a.adresse.ville, a.createdAt, a.date, a.description);
                contratsList.innerHTML += card.contratTemplate;
            })
        } else {
            contratsList.innerHTML = `<div>Il y a eu un problème avec la requête.</div>`;
        }
    }
}

const candidaturesList = document.getElementById('candidaturesList');
function displayCandidatures() {
    //En cours de chargement
    if (httpRequest.readyState === XMLHttpRequest.LOADING) {
        candidaturesList.innerHTML = `<i class="notched circle loading icon"></i>`;
    }
    //Chargé
    else if (httpRequest.readyState === XMLHttpRequest.DONE) {
        if (httpRequest.status === 200) {
            // stocke les résultats parsé en JSON dans une variable
            results = JSON.parse(httpRequest.responseText)
            console.log(results);
            // Réinitialise la liste
            candidaturesList.innerHTML = '';

            // Il n'y a pas de résultats :
            if (results.candidatures === undefined || results.candidatures.length == 0) {
                candidaturesList.innerHTML = `<div>Pas de candidatures.</div>`;
            }

            // Il y a des résultats :
            // Pour chaque cv du tableau propositions
            results.candidatures.forEach(a => {
                let card = new ContratEvent(a.identity, a.nom, a.adresse.rue, a.adresse.codePostal, a.adresse.ville, a.createdAt, a.date, a.description);
                candidaturesList.innerHTML += card.candidatureTemplate;
            })
        } else {
            candidaturesList.innerHTML = `<div>Il y a eu un problème avec la requête.</div>`;
        }
    }
}