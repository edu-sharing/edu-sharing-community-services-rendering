// Only register the listener once
if (typeof window.toggleMetadataListener === 'undefined') {
    window.toggleMetadataListener = function(event) {
        // Case 1: toggle button was clicked -> show/hide the respective node's metadata div
        if (event.target.classList.contains("edusharing_metadata_toggle_button")) {
            const metadataNode = event.target.parentNode.querySelector(".edusharing_metadata")
            metadataNode.classList.toggle("edusharing_metadata_inline")
            // Case 2: Outside click -> Close all metadata divs
        } else if (event.target.closest(".edusharing_metadata") === null) {
            const allMetadataNodes = document.querySelectorAll(".edusharing_metadata")
            allMetadataNodes.forEach(node => node.classList.toggle("edusharing_metadata_inline"))
        }
    }
    document.addEventListener('click', window.toggleMetadataListener)
}