var httpRequest, searchInput;
searchInput = document.getElementById("searchInput");
document.getElementById("searchBtn").addEventListener('click', makeRequest);

function makeRequest() {
    httpRequest = new XMLHttpRequest();

    if (!httpRequest) {
        alert('Abandon :( Impossible de créer une instance de XMLHTTP');
        return false;
    }
    httpRequest.onreadystatechange = alertContents;
    httpRequest.open('GET', 'test.html');
    httpRequest.send();
}

function alertContents() {
    if (httpRequest.readyState === XMLHttpRequest.DONE) {
        if (httpRequest.status === 200) {
            alert(httpRequest.responseText);
        } else {
            alert('Il y a eu un problème avec la requête.');
        }
    }
}