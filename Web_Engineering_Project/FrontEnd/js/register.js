document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registerForm");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const response = await fetch("http://localhost/BackEnd/register.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();

            if (result.status === "success") {
                alert(result.message);
                window.location.href = result.redirect; // Redirect to the appropriate page
            } else {
                alert(result.message); // Display error message
            }
        } catch (error) {
            console.error("Error:", error);
            alert("An error occurred. Please try again.");
        }
    });
});