document.addEventListener('DOMContentLoaded', function() {
    "use strict";

    // ========== Form-select-option ========== //
    document.querySelectorAll(".step_1").forEach(function(element) {
        element.addEventListener('click', function() {
            document.querySelectorAll(".step_1").forEach(function(el) {
                el.classList.remove("active");
            });
            this.classList.add("active");
        });
    });

    document.querySelectorAll(".step_2").forEach(function(element) {
        element.addEventListener('click', function() {
            document.querySelectorAll(".step_2").forEach(function(el) {
                el.classList.remove("active");
            });
            this.classList.add("active");
        });
    });

    document.querySelectorAll(".step_3").forEach(function(element) {
        element.addEventListener('click', function() {
            document.querySelectorAll(".step_3").forEach(function(el) {
                el.classList.remove("active");
            });
            this.classList.add("active");
        });
    });

    document.querySelectorAll(".step_4").forEach(function(element) {
        element.addEventListener('click', function() {
            document.querySelectorAll(".step_4").forEach(function(el) {
                el.classList.remove("active");
            });
            this.classList.add("active");
        });
    });

    // ================== CountDown function ================


    // ===================== Progress Increment ====================
    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'nextBtn') {
            var progressbar = document.querySelector('.count_progress');
            var currentClass = progressbar.className;
            var currentClip = parseInt(currentClass.split('-')[1]);
            if (currentClip < 4) {
                progressbar.className = currentClass.replace('clip-' + currentClip, 'clip-' + (currentClip + 1));
            }
        }
    });

    // ===================== Progress Decrement ====================
    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'prevBtn') {
            var progressbar = document.querySelector('.count_progress');
            var currentClass = progressbar.className;
            var currentClip = parseInt(currentClass.split('-')[1]);
            if (currentClip > 1) {
                progressbar.className = currentClass.replace('clip-' + currentClip, 'clip-' + (currentClip - 1));
            }
        }
    });

});





function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("multisteps_form_panel");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form... :
  if (currentTab >= x.length) {
    //...the form gets submitted:
    document.getElementById("wizard").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

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

function move() {
  var elem = document.getElementById("myBar");
  var width = parseInt(elem.innerHTML);
  var aim = width + 25;
  var id = setInterval(frame, 25);

  function frame() {
    if (width >= aim) {
      clearInterval(id);
    } else if(width >= 100) {
      width=0;
      aim = 25;
      elem.style.width = width + '%';
      elem.innerHTML = width * 1 + '%';
    } else {
      width++;
      elem.style.width = width + '%';
      elem.innerHTML = width * 1 + '%';
    }
  }
}
