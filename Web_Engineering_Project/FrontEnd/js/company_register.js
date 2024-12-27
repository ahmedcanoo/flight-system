document.getElementById('companyForm').addEventListener('submit', (e) => {
    e.preventDefault();

    const companyName = document.getElementById('companyName').value;
    const companyBio = document.getElementById('companyBio').value;
    const companyAddress = document.getElementById('companyAddress').value;

    console.log("Submitting company details:", { companyName, companyBio, companyAddress });

    // Add server-side submission logic here

    alert('Company Registration Complete!');
    window.location.href = 'company_home.html'; // Redirect to the company home page
});