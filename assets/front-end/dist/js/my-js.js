// Moment JS
// function displayTime() {
//   var time = moment().format("MMMM Do YYYY, h:mm:ss a");
//   $("#clock").html(time);
//   setTimeout(displayTime, 1000);
// }

// $(document).ready(function () {
//   displayTime();
// });

// Glider Menu
function myFunction() {
  document.getElementById("icon").classList.toggle("fa-times");
}

window.addEventListener("load", function () {
  new Glider(document.querySelector(".glider"), {
    slidesToScroll: 1,
    slidesToShow: 5.5,
    draggable: true,
    dots: "",
    arrows: {
      prev: ".glider-prev",
      next: ".glider-next",
    },
  });
});

$(function () {
  // ------------------------------------------------------- //
  // Multi Level dropdowns
  // ------------------------------------------------------ //
  $("ul.dropdown-menu [data-toggle='dropdown']").on("click", function (event) {
    event.preventDefault();
    event.stopPropagation();

    $(this).siblings().toggleClass("show");

    if (!$(this).next().hasClass("show")) {
      $(this)
        .parents(".dropdown-menu")
        .first()
        .find(".show")
        .removeClass("show");
    }
    $(this)
      .parents("li.nav-item.dropdown.show")
      .on("hidden.bs.dropdown", function (e) {
        $(".dropdown-submenu .show").removeClass("show");
      });
  });
});
