const previousButton = document.getElementById("previous")
const nextButton = document.getElementById("next")
const submitButton = document.getElementById('validate')
const lightboxDisclaimer = document.getElementById('lightbox_disclaimer')
const forms = document.getElementById('reserve_my_spot')
const dots = document.getElementsByClassName('progress-bar__dot')
const numberOfSteps = 4
let currentStep = 1

for (let i = 0; i < dots.length; ++i) {
      dots[i].addEventListener('click', () => {
            goToStep(i + 1)
      })
}

previousButton.onclick = goPrevious
nextButton.onclick = validateStep
//nextButton.onclick = goNext

function validateStep(e) {
      //console.log(currentStep);
      $("div.lightbox_error").remove();
      // Get the current step's input fields
      var selector      = document.getElementById('step' + currentStep);
      var inputs        = selector.querySelectorAll('[required]');

      //console.log(inputs);
      // Check if all required fields are filled

      var isValid = Array.from(inputs).every(function(input) {
          if (input.type === 'radio' || input.type === 'checkbox') {
              return document.querySelector('input[name="' + input.name + '"]:checked');
          } else {
              return input.value.trim() !== '';
          }
      });

      if (isValid) {
            goNext(e)
      } else {
          selector.innerHTML += '<div class="lightbox_error">Please fill all required fields before proceeding.</div>';
      }
  }


function goNext(e) {
      e.preventDefault()
      currentStep += 1
      goToStep(currentStep)
}

function goPrevious(e) {
      e.preventDefault()
      currentStep -= 1
      goToStep(currentStep)
}

function goToStep(stepNumber) {
      currentStep = stepNumber

      let inputsToHide = document.getElementsByClassName('step')
      let inputs = document.getElementsByClassName(`step${currentStep}`)
      let indicators = document.getElementsByClassName('progress-bar__dot')

      for (let i = indicators.length - 1; i >= currentStep; --i) {
            indicators[i].classList.remove('full')
      }

      for (let i = 0; i < currentStep; ++i) {
            indicators[i].classList.add('full')
      }

      //hide all input
      for (let i = 0; i < inputsToHide.length; ++i) {
            hide(inputsToHide[i])
      }

      //only show the right one
      for (let i = 0; i < inputs.length; ++i) {
            show(inputs[i])
      }

      //if we reached final step
      if (currentStep === numberOfSteps) {
            enable(previousButton)
            disable(nextButton)
            show(submitButton)
            show(lightboxDisclaimer)
      }

      //else if first step
      else if (currentStep === 1) {
            disable(previousButton)
            enable(next)
            hide(submitButton)
            hide(lightboxDisclaimer)
      }

      else {
            enable(previousButton)
            enable(next)
            hide(submitButton)
            hide(lightboxDisclaimer)
      }
}

function enable(elem) {
      elem.classList.remove("disabled");
      elem.disabled = false;
}

function disable(elem) {
      elem.classList.add("disabled");
      elem.disabled = true;
}

function show(elem) {
      elem.classList.remove('hidden')
}

function hide(elem) {
      elem.classList.add('hidden')
}
// for popup

// select the open-btn button
let openBtn = document.getElementById('open-btn');
// select the modal-background
let modalBackground = document.getElementById('modal-background');
// select the close-btn 
let closeBtn = document.getElementById('close-btn');

// shows the modal when the user clicks open-btn
openBtn.addEventListener('click', function () {
      modalBackground.style.display = 'block';
});

// hides the modal when the user clicks close-btn
closeBtn.addEventListener('click', function () {
      modalBackground.style.display = 'none';
});

// hides the modal when the user clicks outside the modal
window.addEventListener('click', function (event) {
      // check if the event happened on the modal-background
      if (event.target === modalBackground) {
            // hides the modal
            modalBackground.style.display = 'none';
      }
});