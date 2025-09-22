document.addEventListener("DOMContentLoaded", function() {
  let mybutton = document.getElementById("backToTopBtn");

  window.onscroll = function() { scrollFunction(); };

  function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      mybutton.style.display = "block";
    } else {
      mybutton.style.display = "none";
    }
  }

  window.topFunction = function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };
});