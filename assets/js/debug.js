document.addEventListener("keydown", (e) => {
    const debugPanel = document.getElementById("debug");
    if (e.key === "*") {
        if (debugPanel) {
            debugPanel.style.display = debugPanel.style.display === "none" ? "flex" : "none";
        }
    }
});