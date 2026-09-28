// ==================================================
// Project Name  :  Quizo
// File          :  JS Base
// Version       :  1.0.0
// Author        :  jthemes (https://themeforest.net/user/jthemes)
// ==================================================
// $(function(){
//   "use strict";
//   // ========== Form-select-option ========== //
//   $(".step_1").on('click', function(){
//     $(".step_1").removeClass("active");
//     $(this).addClass("active");
//   });
//   $(".step_2").on('click', function(){
//     $(".step_2").removeClass("active");
//     $(this).addClass("active");
//   });
//   $(".step_3").on('click', function(){
//     $(".step_3").removeClass("active");
//     $(this).addClass("active");
//   });
//   $(".step_4").on('click', function(){
//     $(".step_4").removeClass("active");
//     $(this).addClass("active");
//   });
//
//   // ================== CountDown function ================
//   // $('.countdown_timer').each(function(){
//   //   $('[data-countdown]').each(function() {
//   //     var $this = $(this), finalDate = $(this).data('countdown');
//   //     $this.countdown(finalDate, function(event) {
//   //       var $this = $(this).html(event.strftime(''
//   //       + '<div class="count_hours"><h3>%H</h3><span class="text-uppercase">hrs</span></div>'
//   //       + '<div class="count_min"><h3>%M</h3><span class="text-uppercase">min</span></div>'
//   //       + '<div class="count_sec"><h3>%S</h3><span class="text-uppercase">sec</span></div>'));
//   //     });
//   //   });
//   // });
//
// });

var currentTab = 0; // Current tab is set to be the first tab (0)




function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("multisteps_form_panel");
  y = x[currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false:
      valid = false;
    }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class to the current step:
  x[n].className += " active";
}
