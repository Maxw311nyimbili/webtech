// Function to load countries from the API
async function loadCountries() {
    try {
        const response = await fetch('https://restcountries.com/v3.1/all');
        const countries = await response.json();
        const select = document.getElementById('countrySelect');

        // Loop through each country and create an option element
        countries.forEach(country => {
            const option = document.createElement('option');
            option.value = country.cca2; // 2-letter country code
            option.textContent = country.name.common; // Common name of the country
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error fetching country data:', error);
    }
}

// Call the function to load countries when the page is loaded
window.onload = loadCountries;
