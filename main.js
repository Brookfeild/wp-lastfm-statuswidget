document.addEventListener("DOMContentLoaded", function () {
  const scrollers = document.querySelectorAll(".lastfm-scroll");

  scrollers.forEach((el) => {
    const speed = parseInt(el.dataset.speed || "5", 10); // default 5s

    const text = el.innerHTML;
    const span = document.createElement("span");
    span.textContent = text;
    el.innerHTML = "";
    el.appendChild(span);

    // Reset width & animation
    const distance = span.offsetWidth + el.offsetWidth;
    const duration = speed * distance * 0.01; // adjust speed scaling

    span.style.animation = `lastfm-marquee ${duration}s linear infinite`;
  });
});

// Inject keyframes once
if (!document.querySelector("#lastfm-marquee-style")) {
  const style = document.createElement("style");
  style.id = "lastfm-marquee-style";
  style.innerHTML = `
    @keyframes lastfm-marquee {
      0% { transform: translateX(0); }
      100% { transform: translateX(-100%); }
    }
  `;
  document.head.appendChild(style);
}
