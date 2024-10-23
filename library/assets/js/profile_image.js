window.onload = function() {
    var fileImg = document.getElementById("fileImg");
    var image = document.getElementById("image");
    var upload = document.getElementById("upload");

    // Ensure elements exist to avoid errors
    if (fileImg && image && upload) {
        var userImage = image.src;  // Save the original image

        fileImg.onchange = function() {
            image.src = URL.createObjectURL(fileImg.files[0]); // Preview new image
        };
    }
};
