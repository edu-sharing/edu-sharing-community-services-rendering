const page = window.location.hash.substring(1);
if (page !== null && page.includes("page=")) {
    const docFrame = document.getElementById("docFrame");
    docFrame.src = docFrame.src + "#" + page;
}