const form = document.getElementById('city-form');
const minInput = document.getElementById('min');
const maxInput = document.getElementById('max');
const submitButton = document.getElementById('submit-button');
const loading = document.getElementById('loading');
const status = document.getElementById('status');
const results = document.getElementById('results');

function setBusy(isBusy) {
    minInput.disabled = isBusy;
    maxInput.disabled = isBusy;
    submitButton.disabled = isBusy;
    loading.classList.toggle('hidden', !isBusy);
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function renderCities(cities) {
    if (cities.length === 0) {
        results.innerHTML = '<p>No cities were found in that range.</p>';
        return;
    }

    let html = '<div class="table-scroll"><table><thead><tr><th>City Name</th><th>Country Code</th><th>Population</th></tr></thead><tbody>';

    for (const city of cities) {
        html += '<tr>' +
            '<td>' + escapeHtml(city.city_name) + '</td>' +
            '<td>' + escapeHtml(city.country_code) + '</td>' +
            '<td>' + escapeHtml(city.population) + '</td>' +
            '</tr>';
    }

    html += '</tbody></table></div>';
    results.innerHTML = html;
}

form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const min = minInput.value.trim();
    const max = maxInput.value.trim();

    setBusy(true);
    status.textContent = 'Waiting for the server...';
    results.textContent = 'Loading...';

    try {
        const params = new URLSearchParams({ min, max });
        const response = await fetch('cities.php?' + params.toString());
        const data = await response.json();

        if (!data.ok) {
            status.textContent = data.error;
            results.textContent = 'No results to display.';
            return;
        }

        status.textContent = 'Loaded ' + data.count + ' cities.';
        renderCities(data.cities);
    } catch (error) {
        status.textContent = 'The request failed.';
        results.textContent = 'No results to display.';
    } finally {
        setBusy(false);
    }
});
