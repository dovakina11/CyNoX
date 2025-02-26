/* admin/js/admin-scripts.js */

// Wait for the DOM to fully load before executing scripts
document.addEventListener("DOMContentLoaded", () => {
    // Initialize WYSIWYG Editor
    const textAreas = document.querySelectorAll(".wysiwyg-editor");
    textAreas.forEach((textArea) => {
        const editor = document.createElement("div");
        editor.contentEditable = true;
        editor.classList.add("wysiwyg-container");
        editor.innerHTML = textArea.value;
        textArea.style.display = "none";
        textArea.parentNode.insertBefore(editor, textArea);

        editor.addEventListener("input", () => {
            textArea.value = editor.innerHTML;
        });
    });

    // Media Upload Functionality
    const mediaUploadForm = document.querySelector("#media-upload-form");
    const mediaPreviewContainer = document.querySelector("#media-preview-container");

    if (mediaUploadForm) {
        mediaUploadForm.addEventListener("submit", (event) => {
            event.preventDefault();
            const fileInput = document.querySelector("#media-upload-input");
            const files = fileInput.files;

            if (files.length > 0) {
                Array.from(files).forEach((file) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const preview = document.createElement("div");
                        preview.classList.add("media-preview-item");
                        if (file.type.startsWith("image/")) {
                            const img = document.createElement("img");
                            img.src = e.target.result;
                            img.alt = file.name;
                            preview.appendChild(img);
                        } else {
                            const span = document.createElement("span");
                            span.textContent = file.name;
                            preview.appendChild(span);
                        }
                        mediaPreviewContainer.appendChild(preview);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    }

    // Dashboard Interactions
    const overviewCards = document.querySelectorAll(".overview-cards .card");
    overviewCards.forEach((card) => {
        card.addEventListener("click", () => {
            alert(`You clicked on ${card.querySelector("h3").textContent}`);
        });
    });

    // Confirmation Dialogs for Delete Actions
    const deleteButtons = document.querySelectorAll(".btn-delete");
    deleteButtons.forEach((button) => {
        button.addEventListener("click", (event) => {
            const confirmDelete = confirm("Are you sure you want to delete this item?");
            if (!confirmDelete) {
                event.preventDefault();
            }
        });
    });
});
