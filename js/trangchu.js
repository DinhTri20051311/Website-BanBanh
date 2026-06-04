document.addEventListener("DOMContentLoaded", function () {
    const anhchinh = document.getElementById("anhchinh");
    const anhphu = document.querySelectorAll(".tc-anh");

    anhphu.forEach(function (doi) {
      doi.addEventListener("click", function () {
        anhchinh.style.opacity = 0.5;

        setTimeout(() => {
          anhchinh.src = doi.src;
          anhchinh.style.opacity = 1;
        }, 150);
      });
    });
  });