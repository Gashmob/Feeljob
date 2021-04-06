export class AnnonceCard {
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

export function initAnnonce() {
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