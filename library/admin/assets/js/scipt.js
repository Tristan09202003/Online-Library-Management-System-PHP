// script.js
window.onload = function() {
    var modal = document.getElementById("user-details-modal");
    var closeBtn = document.getElementsByClassName("close")[0];
    var userDetailsContent = document.getElementById("user-details-content");

    // Open modal when button is clicked
    document.querySelectorAll('.open-details').forEach(function(button) {
        button.onclick = function() {
            var userId = this.getAttribute("data-user-id");

            // Fetch user details using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "get_user_details.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (this.status == 200) {
                    userDetailsContent.innerHTML = this.responseText; // Show details inside modal
                    modal.style.display = "block";  // Display the modal
                } else {
                    console.error("Failed to fetch user details.");
                }
            };
            xhr.send("id=" + userId);
        };
    });

    // Close the modal
    closeBtn.onclick = function() {
        modal.style.display = "none";
    };

    // Close modal when clicking outside of the modal content
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
};
