
let progressBar = document.getElementById("es_progressbar_progress")
if (progressBar !== null) {
    let progress = progressBar.getAttribute("data-progress")
    progressBar.style.width = progress + "%"
}
