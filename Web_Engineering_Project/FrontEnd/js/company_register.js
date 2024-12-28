document.getElementById('companyForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    try {
        // Send form data to the backend
        const response = await fetch("../../BackEnd/company_register.php", {
            method: "POST",
            body: formData,
        });

        const result = await response.json();
        if (result.status === "success") {
            alert(result.message);
            window.location.href = "company_home.html"; // Redirect to the company home page
        } else {
            alert(result.message); // Display error message from server
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred during registration. Please try again.");
    }
});
