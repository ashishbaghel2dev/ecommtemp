document.addEventListener("DOMContentLoaded", () => {
    const wrapper = document.getElementById("notifWrapper");
    const dropdown = document.getElementById("notifDropdown");

    if (!wrapper || !dropdown) return;

    wrapper.addEventListener("click", (e) => {
        e.stopPropagation();
        dropdown.classList.toggle("show");
    });

    document.addEventListener("click", () => {
        dropdown.classList.remove("show");
    });
});