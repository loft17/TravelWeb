// Script para que solo un <details> esté abierto a la vez
document.querySelectorAll('details').forEach((targetDetail) => {
targetDetail.addEventListener("toggle", (event) => {
    if (targetDetail.open) {
    document.querySelectorAll('details').forEach((detail) => {
        if (detail !== targetDetail) {
        detail.removeAttribute("open");
        }
    });
    }
});
});
