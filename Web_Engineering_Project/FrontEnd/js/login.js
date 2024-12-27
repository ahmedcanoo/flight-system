document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const errorMessage = document.getElementById("errorMessage");

    loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        try {
            const response = await fetch("login.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, password }),
            });

            const result = await response.json();
            if (result.status === "success") {
                window.location.href = "dashboard.html";
            } else {
                errorMessage.textContent = result.message || "Login failed.";
            }
        } catch (error) {
            console.error("Error:", error);
            errorMessage.textContent = "An unexpected error occurred.";
        }
    });
});
