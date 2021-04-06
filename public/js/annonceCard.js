var card = `
                <div class="card annonceCard">
                    <div class="content">
                        <div class="header">` + results.annonces[i].nom + `</div>
                        <div class="meta">
                            <span>
                                <i class="icon map pin"></i>
                                ` + results.annonces[i].adresse.ville + `
                            </span>
                            <span>Pour le 15/05</span>
                        </div>
                        <div class="description">
                            <p>` + truncate(results.annonces[i].description, MAX_DESCRIPTION_LENGTH, true) + `</p>
                        </div>
                        <div class="extra">
                            <div class="ui label">CDD</div>
                            <div class="ui label">CDI</div>
                            <div class="ui label"><i class="globe icon"></i>Anglais</div>
                            <a href="{{ path('particulier_show_annonce', {'id': 1}) }}`.slice(0, -1) + results.annonces[i].identity + `"
                               class="ui right floated green button">
                                Voir l'annonce
                                <i class="right chevron icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `

class AnnonceCard {
    constructor(price, title, image, description) {
        this.cardTemplate = `
           <div class="col-lg-3 col-md-6 mb-4">
               <div class="card">
                  <a href="#">
                     <img class="mx-auto-d-block-card-img-top-fixed-top"
                          src="${image}" width="335" height="340" alt="${image}"
                          style="border: solid 1px #999999;"></a>
      <div class="card-body">
        <h4>${title}</h4>
        <h5>${price}</h5>
        <h5>\
          <a href="#">Kup teraz!</a>
        </h5>
        <p class="card-text">${description}</p>
      </div>
      <div class="card-footer">
        <small class="text-muted">&#9733; &#9733; &#9733; &#9733; &#9733;</small>\
      </div>
    </div>
  </div>
  `;
    }
}

function initAnnonce() {
    var params = {
        title: "Chów i hodowla trzody chlewnej",
        image: "swinie.jpg",
        price: "135zł",
        description: "Podręcznik, który wyjaśnia jak chodować świnie."
    };

    const book = document.getElementById("newAnnonce");
    const elements = new Annonce(params.price,
        params.title, params.image, params.description);
    console.log(elements);
    book.innerHTML = `${elements.bookTemplate}`;
}

initAnnonce();