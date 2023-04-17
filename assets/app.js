/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";

//import bootstrap's js
import "bootstrap/dist/js/bootstrap.min.js";

//import fontawesome's js
import "../node_modules/@fortawesome/fontawesome-free/js/all.min.js";

// start the Stimulus application
import "./bootstrap";

const hamburger = document.getElementById("collapse_toggler_header");
hamburger.addEventListener("click", function () {
  hamburger.classList.toggle("is-active");
});

const zipInput = document.getElementById("registration_form_codeZIP");
const cityInput = document.getElementById("registration_form_city");
const flashMessage = document.getElementById('errorZIP')
zipInput.addEventListener("input", async () => {
  flashMessage.classList.add('d-none')
  if (zipInput.value.length === 5) {
    try {
      const response = await fetch(
        `https://geo.api.gouv.fr/communes?codePostal=${zipInput.value}&fields=nom,departement`
      );
      const data = await response.json();
      // console.log(data)
      if (data.length !== 0) {
        const city = data[0].nom;
        const departement = data[0].departement.nom;
        cityInput.value = city + ", " + departement;
        // console.log(cityInput.value)
      } else {
        cityInput.value = "";
        flashMessage.classList.remove('d-none')
      }
    } catch (error) {
      console.error(
        `Error fetching city for zip code ${zipInput.value}:`,
        error
      );
    }
  } else {
    cityInput.value = "";
  }
});
