function myfunction() {
    'use strict';
    window.addEventListener('load', function() {
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var input= document.getElementsByClassName('needs-validation');
      // Loop over them and prevent submission
      var validation = Array.prototype.filter.call(input, function(form) {
        form.addEventListener('submit', function(event) {
          if (input.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          input.classList.add('was-validated');
        }, false);
      });
    }, false);
  };