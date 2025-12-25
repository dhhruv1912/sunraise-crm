// Acceptable serial format (adjust if needed):
// A-Z0-9, 12-20 characters long
const serialRegex = /[A-Z0-9]{10,20}/g;

// Character correction rules
function fixSerial(str) {
    return str
        .replace(/O/g, "0")
        .replace(/I/g, "1")
        .replace(/B/g, "8")
        .replace(/S/g, "5");
}

async function runOCR(fileInputId, textareaId, previewId) {

    const file = document.getElementById(fileInputId).files[0];
    if (!file) {
        alert("Please choose a file first.");
        return;
    }

    // File preview
    if (previewId) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Convert file to base64 for Tesseract
    const imageData = await fileToBase64(file);

    // Show loading message
    document.querySelector(`#${textareaId}`).value = "Processing OCR... Please wait...";

    // OCR Processing
    const result = await Tesseract.recognize(imageData, "eng", {
        logger: m => console.log(m),
    });

    const text = result.data.text;
    console.log("OCR RESULT:", text);

    // Extract serial-like strings
    let serials = text.match(serialRegex) || [];

    // Clean & fix values
    serials = serials.map(s => fixSerial(s));

    // Remove duplicates
    serials = [...new Set(serials)];

    // Show extracted serials
    document.querySelector(`#${textareaId}`).value = serials.join("\n");
}

function fileToBase64(file) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.readAsDataURL(file);
    });
}
