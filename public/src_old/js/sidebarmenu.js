/*
Template Name: Admin Template
Author: Wrappixel

File: js
*/
// ==============================================================
// Auto select left navbar
// ==============================================================
// 
$(function () {
  "use strict";

  // Highlight the active menu item on page load based on URL
  var url = window.location.pathname;
  var path = url.split("/")[1]; // Get the first part of the URL

  var element = $("ul#sidebarnav a").filter(function () {
    var hrefPath = new URL(this.href, window.location.origin).pathname.split("/")[1];
    return hrefPath === path; // Match exact path
  });

  // Add 'active' class to the parent elements of the matched item
  element.parentsUntil(".sidebar-nav").each(function () {
    if ($(this).is("li") && $(this).children("a").length !== 0) {
      $(this).children("a").addClass("active");
      $(this).addClass("active");
    } else if ($(this).is("ul")) {
      $(this).addClass("in");
    }
  });

  // Add 'active' class to the matched element
  element.addClass("active");

  // Scroll the active item into view on page load
  if (element.length) {
    element[0].scrollIntoView({
      behavior: "auto",
      block: "center",
    });
  }

  // Handle menu clicks
  $("#sidebarnav a").on("click", function (e) {
    e.preventDefault(); // Prevent default action for testing navigation

    // Remove 'active' from all items and add 'bg-body-secondary' to old active items
    $("#sidebarnav a.active").removeClass("active").addClass("bg-body-secondary");

    // Remove 'bg-body-secondary' from the clicked item
    $(this).removeClass("bg-body-secondary");

    // Add 'active' class to the clicked menu item
    $(this).addClass("active");

    // Add 'active' class to parent <li> elements
    $(this).parents("li").addClass("active");

    // Scroll the clicked item into view
    this.scrollIntoView({
      behavior: "smooth",
      block: "center",
    });

    // Redirect to the clicked menu's href (simulate navigation here)
    window.location.href = this.href;
  });
});













// TEMPLATE
// $(function () {
//   "use strict";
//   var url = window.location + "";
//   var path = url.replace(
//     window.location.protocol + "//" + window.location.host + "/",
//     ""
//   );
//   var element = $("ul#sidebarnav a").filter(function () {
//     return this.href === url || this.href === path; // || url.href.indexOf(this.href) === 0;
//   });
//   element.parentsUntil(".sidebar-nav").each(function (index) {
//     if ($(this).is("li") && $(this).children("a").length !== 0) {
//       $(this).children("a").addClass("active");
//       $(this).parent("ul#sidebarnav").length === 0
//         ? $(this).addClass("active")
//         : $(this).addClass("selected");
//     } else if (!$(this).is("ul") && $(this).children("a").length === 0) {
//       $(this).addClass("selected");
//     } else if ($(this).is("ul")) {
//       $(this).addClass("in");
//     }
//   });

//   element.addClass("active");
//   $("#sidebarnav a").on("click", function (e) {
//     if (!$(this).hasClass("active")) {
//       // hide any open menus and remove all other classes
//       $("ul", $(this).parents("ul:first")).removeClass("in");
//       $("a", $(this).parents("ul:first")).removeClass("active");

//       // open our new menu and add the open class
//       $(this).next("ul").addClass("in");
//       $(this).addClass("active");
//     } else if ($(this).hasClass("active")) {
//       $(this).removeClass("active");
//       $(this).parents("ul:first").removeClass("active");
//       $(this).next("ul").removeClass("in");
//     }
//   });
//   $("#sidebarnav >li >a.has-arrow").on("click", function (e) {
//     e.preventDefault();
//   });
// });
