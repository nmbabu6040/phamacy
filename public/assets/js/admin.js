$(function () {
    // Preloader
    $(window).on("load", function () { $("#preloader").addClass("hide"); });
    setTimeout(() => $("#preloader").addClass("hide"), 1200);

    // Sidebar toggle
    $("#sidebarToggle").on("click", function () { $("#sidebar").toggleClass("collapsed"); });

    // Dark mode (persists for the session via a simple cookie flag)
    const html = document.documentElement;
    if (localStorage.getItem("admin-theme") === "dark") html.setAttribute("data-bs-theme", "dark");
    $("#darkModeToggle").on("click", function () {
        const isDark = html.getAttribute("data-bs-theme") === "dark";
        html.setAttribute("data-bs-theme", isDark ? "light" : "dark");
        localStorage.setItem("admin-theme", isDark ? "light" : "dark");
    });

    // Auto-dismiss alerts
    setTimeout(() => $(".alert").alert("close"), 5000);
});
