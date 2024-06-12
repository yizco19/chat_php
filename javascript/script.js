// cancelButton.js
export function createCancelButtonHtml() {
    var style_cancel_button = "cursor: pointer; width: 28px; height: 28px; align-items: right;";
    return `
        <div class="swal2-custom-cancel-btn" style="display: flex; flex-direction: column; align-items: flex-end;">
            <img src="resource/marca-x.png" alt="Cancel" class="option-img" id="cancelButton" style="${style_cancel_button}">
        </div>
    `;
}
