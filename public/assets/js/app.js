$(function () {
    /* ---------------- Preloader ---------------- */
    $(window).on("load", function () {
        $("#preloader").addClass("hide");
    });
    setTimeout(() => $("#preloader").addClass("hide"), 1500);

    /* ---------------- AOS (scroll animations) ---------------- */
    if (window.AOS) AOS.init({ duration: 800, once: true, offset: 80 });

    /* ---------------- Venobox (modal image lightbox / quick view) ---------------- */
    if ($.fn.venobox)
        $(".venobox").venobox({ numeratio: true, spinner: "wave" });

    /* ---------------- Isotope (category grid filtering) ---------------- */
    const $grid = $(".isotope-grid");
    if ($grid.length && window.Isotope) {
        $grid.imagesLoaded(function () {
            new Isotope($grid[0], {
                itemSelector: ".isotope-item",
                layoutMode: "fitRows",
            });
        });
    }

    /* ---------------- Counters (count-up animation) ---------------- */
    function animateCounters() {
        $(".counter").each(function () {
            const $el = $(this);
            if ($el.data("counted")) return;
            const target = parseInt($el.data("count"), 10) || 0;
            const top = $el.offset().top;
            if (top < $(window).scrollTop() + $(window).height() - 50) {
                $el.data("counted", true);
                $({ val: 0 }).animate(
                    { val: target },
                    {
                        duration: 1600,
                        step: function () {
                            $el.text(Math.ceil(this.val).toLocaleString());
                        },
                        complete: function () {
                            $el.text(target.toLocaleString());
                        },
                    },
                );
            }
        });
    }
    animateCounters();
    $(window).on("scroll", animateCounters);

    /* ---------------- Dark theme toggle (persists via localStorage) ---------------- */
    const html = document.documentElement;
    if (localStorage.getItem("site-theme") === "dark")
        html.setAttribute("data-bs-theme", "dark");
    $("#darkToggle").on("click", function () {
        const isDark = html.getAttribute("data-bs-theme") === "dark";
        html.setAttribute("data-bs-theme", isDark ? "light" : "dark");
        localStorage.setItem("site-theme", isDark ? "light" : "dark");
        $(this).find("i").toggleClass("bi-moon-stars bi-sun");
    });

    /* ---------------- Back to top ---------------- */
    $(window).on("scroll", function () {
        $("#backToTop").toggleClass("show", $(this).scrollTop() > 300);
    });
    $("#backToTop").on("click", function (e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, 500);
    });

    /* ---------------- Sticky navbar shrink on scroll ---------------- */
    $(window).on("scroll", function () {
        $(".main-navbar").toggleClass("shrink", $(this).scrollTop() > 60);
    });
});
