// document.querySelector(".show-all")

document.querySelector(".inquiry-btn-aside") &&
  document.querySelector(".inquiry-btn-aside").addEventListener("click", () => {
    document.querySelector(".modal-dialog.inquiry-dialog").style.display =
      "flex";
    document.body.style.overflow = "hidden";
  });
document.querySelector(".inquiry-btn") &&
  document.querySelector(".inquiry-btn").addEventListener("click", () => {
    document.querySelector(".modal-dialog.inquiry-dialog").style.display =
      "flex";
    document.body.style.overflow = "hidden";
  });
document.querySelector(".inquiry-dialog .burger.open") &&
  document
    .querySelector(".inquiry-dialog .burger.open")
    .addEventListener("click", () => {
      document.querySelector(".modal-dialog.inquiry-dialog").style.display =
        "none";
      document.body.removeAttribute("style");
    });
document.addEventListener("click", (e) => {
  if (String(e.target.className).split(" ").includes("modal-dialog")) {
    document.querySelector(".modal-dialog.inquiry-dialog").style.display =
      "none";
    document.querySelector(".modal-dialog.gallery-dialog").style.display =
      "none";
    document.body.removeAttribute("style");
  }
});

document.querySelector(".show-all") &&
  document.querySelector(".show-all").addEventListener("click", () => {
    document.querySelector(".modal-dialog.gallery-dialog").style.display =
      "flex";
    document.body.style.overflow = "hidden";
  });
document.querySelector(".swiper-gallery .burger.open") &&
  document
    .querySelector(".swiper-gallery .burger.open")
    .addEventListener("click", () => {
      document.querySelector(".modal-dialog.gallery-dialog").style.display =
        "none";
      document.body.removeAttribute("style");
    });

let swiper2 =
  document.querySelector(".modal-dialog.gallery-dialog .thumb") &&
  new Swiper(".modal-dialog.gallery-dialog .thumb", {
    spaceBetween: 10,
    freeMode: true,
    grubCursor: true,
    slidesPerView: "auto",
    watchSlidesProgress: true,
    slideActiveClass: "swiper-slide-thumb-active",
  });
let swiper =
  document.querySelector(".modal-dialog.gallery-dialog .swiper-main") &&
  new Swiper(".modal-dialog.gallery-dialog .swiper-main", {
    spaceBetween: 10,
    slidesPerView: 1,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    thumbs: {
      swiper: swiper2,
    },
  });

mapboxgl.accessToken = mapLicense;
const map = new mapboxgl.Map({
  container: "map", // container ID
  style: "https://osm.axcapital.ae/styles/streets/style.json", // style URL
  center: [longitude, latitude], // starting position [lng, lat]
  maxzoom: 17,
  minzoom: 5,
  "cooperative-gestures": true,
  "attribution-control": true,
  "auto-resize": true,
  zoom: 13,
});


const el = document.createElement("div");
el.innerHTML = `<div class="map-marker"><img src="https://dt1creuksls0v.cloudfront.net/static/assets/img/svg/location.svg"><img src="https://dt1creuksls0v.cloudfront.net/static/assets/img/svg/location.svg"></div>`;
// make a marker for each feature and add to the map
new mapboxgl.Marker(el).setLngLat([longitude, latitude]).addTo(map);
// map for hidden pdf section
const maps = new mapboxgl.Map({
    container: "maps", // container ID
    style: "https://osm.axcapital.ae/styles/streets/style.json", // style URL
    center: [longitude, latitude], // starting position [lng, lat]
    maxzoom: 17,
    minzoom: 5,
    "cooperative-gestures": true,
    "attribution-control": true,
    "auto-resize": false,
    zoom: 13,
});
new mapboxgl.Marker(el).setLngLat([longitude, latitude]).addTo(maps);

document.querySelector("aside") &&
  window.addEventListener("scroll", () => {
    const top = document.querySelector("aside").getBoundingClientRect().top;
    if (top < 0) {
      document.querySelector("aside .sticky").style.top = "20px";
    } else {
      document.querySelector("aside .sticky").removeAttribute("style");
    }
  });

const generatePDF = () => {
  const loading_element = document.querySelector(".loading");
  loading_element.style.display = "flex";
  document.body.style.overflow = "hidden";

  document.querySelectorAll(".features-item span").forEach((el) => {
    el.style.marginTop = "-18px";
  });
  document.querySelectorAll(".data-item span").forEach((el) => {
    el.style.marginTop = "-14px";
  });
  document.querySelectorAll(".table span").forEach((el) => {
    el.style.marginTop = "-14px";
  });
  document.querySelectorAll(".contact-item span").forEach((el) => {
    el.style.marginTop = "-14px";
  });

  const element = document.querySelector(".print-page");
  element.style.display = "block";
  const padding = 0;
  setTimeout(() => {
    const htmlWidth = element.clientWidth;
    const htmlHeight = element.clientHeight;
    const pdfWidth = htmlWidth + padding * 2;
    const pdfHeight = pdfWidth * 1.5 + padding * 2;

    const doc = new jsPDF("p", "pt", [pdfWidth, pdfHeight]);

    const pageCount = Math.ceil(htmlHeight / pdfHeight) - 1;

    html2canvas(document.querySelector(".print-page"), {
      allowTaint: false,
      useCORS: false,
    }).then(function (canvas) {
      canvas.getContext("2d");

      const image = canvas.toDataURL("image/png", 1.0);
      image.crossOrigin = "anonymous";
        doc.addImage(image, "JPEG", padding, padding, htmlWidth, htmlHeight,'someAlias', 'FAST');

        for (var i = 1; i <= pageCount; i++) {
            doc.addPage(pdfWidth, pdfHeight);
            doc.addImage(
                image,
                "JPEG",
                padding,
                -(pdfHeight * i) + padding,
                htmlWidth,
                htmlHeight,
                'someAlias',
                'FAST'
            );
        }

      element.removeAttribute("style");
      loading_element.removeAttribute("style");
      document.body.removeAttribute("style");
      doc.save(property_code+".pdf");
      // document.body.appendChild(canvas);
    });
  }, 200);
};

document.querySelector(".generate-pdf") &&
  document.querySelector(".generate-pdf").addEventListener("click", () => {
    generatePDF();
  });
