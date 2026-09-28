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

let property_code = "axc-3961932";
let longitude = "55.14847";
let latitude = "25.104115";

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

  document.querySelectorAll(".data-item span").forEach((el) => {
    el.style.marginTop = "-12px";
  });
  document.querySelector('.contact-block').style.marginTop = "12px";
  document.querySelectorAll(".contact-item span").forEach((el) => {
    el.style.marginTop = "-12px";
  });
  document.querySelectorAll(".contact-item-wp span").forEach((el) => {
    el.style.marginTop = "-10px";
  });
  document.querySelectorAll(".features-item span").forEach((el) => {
    el.style.marginTop = "-10px";
  });

  const element = document.querySelector(".print-page");
  element.style.display = "block";
  const padding = 0;
  setTimeout(() => {
    const htmlWidth = element.clientWidth;
    const htmlHeight = element.clientHeight+12.5;
    const pdfWidth = htmlWidth + padding * 2;
    const pdfHeight = pdfWidth * 1.5 + padding * 2;

    const doc = new jsPDF("p", "pt", [pdfWidth, pdfHeight]);

    const pageCount = Math.ceil(htmlHeight / pdfHeight) - 1;

    html2canvas(document.querySelector(".print-page"), {
      scale: 4,
      allowTaint: false,
      useCORS: true,
    }).then(function (canvas) {
      canvas.getContext("2d");

      const image = canvas.toDataURL("image/png", 1.0);
      image.crossOrigin = "anonymous";
      doc.addImage(
        image,
        "PNG",
        padding,
        padding,
        htmlWidth,
        htmlHeight,
        "someAlias",
        "FAST"
      );

      for (var i = 1; i <= pageCount; i++) {
        doc.addPage(pdfWidth, pdfHeight);
        doc.setFillColor(17, 16, 0);
        doc.rect(0, 0, pdfWidth, pdfHeight, "F");
        doc.addImage(
          image,
          "PNG",
          padding,
          -(pdfHeight * i) + padding,
          htmlWidth,
          htmlHeight,
          "someAlias",
          "FAST"
        );
      }
      // doc.save("multi-page.pdf");
      window.open(doc.output('bloburl'));

      element.removeAttribute("style");
      loading_element.removeAttribute("style");
      document.body.removeAttribute("style");

      // const link = document.createElement("a");
      // link.href = canvas.toDataURL();
      // link.download = "image.png";
      // link.click();
      // document.body.removeChild(link);

      // document.body.appendChild(canvas);
    });
  }, 400);
};

const generateSinglePost = (el, title) => {
  html2canvas(document.querySelector(el), {
    allowTaint: false,
    useCORS: true,
  }).then(function (canvas) {
    canvas.getContext("2d");

    const image = canvas.toDataURL("image/png", 1.0);
    image.crossOrigin = "anonymous";

    const link = document.createElement("a");
    link.setAttribute("id", "instagram-post-link");
    link.href = canvas.toDataURL();
    link.download = title;
    link.click();
    document.getElementById("instagram-post-link") &&
      document.getElementById("instagram-post-link").remove();

    // document.body.appendChild(canvas);
  });
};

const generateInstagramPosts = () => {
  const loading_element = document.querySelector(".loading");
  loading_element.style.display = "flex";
  document.body.style.overflow = "hidden";

  const element = document.querySelector(".instagram-post");
  element.style.display = "block";

  document.querySelectorAll(".inst-post-price").forEach((el) => {
    el.style.marginTop = "-50px";
  });
  document.querySelectorAll(".inst-post-agent").forEach((el) => {
    el.style.marginTop = "-24px";
  });
  document.querySelectorAll(".inst-post-text").forEach((el) => {
    el.classList.add("position");
  });

  setTimeout(() => {
    generateSinglePost(".instagram-post__item.sold > div", "just_sold.png");
    generateSinglePost(".instagram-post__item.sale > div", "for_sale.png");
    generateSinglePost(".instagram-post__item.rented > div", "just_rented.png");
    generateSinglePost(".instagram-post__item.rent > div", "for_rent.png");

    element.removeAttribute("style");
    loading_element.removeAttribute("style");
    document.body.removeAttribute("style");
  }, 400);
};

document.querySelector(".generate-pdf") &&
  document.querySelector(".generate-pdf").addEventListener("click", () => {
    generatePDF();
  });
document.querySelector(".generate-inst-images") &&
  document
    .querySelector(".generate-inst-images")
    .addEventListener("click", () => {
      generateInstagramPosts();
    });
