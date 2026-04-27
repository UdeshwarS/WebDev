<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Population Range AJAX App</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
</head>
<body>
    <main class="wrapper">
        <section class="card">
            <h1>City Population Range</h1>
            <p>Enter a minimum and maximum population to fetch cities from the world database.</p>

            <form id="city-form">
                <label for="min">Minimum Population</label>
                <input id="min" name="min" type="number" min="0" step="1" required value="1000000">

                <label for="max">Maximum Population</label>
                <input id="max" name="max" type="number" min="0" step="1" required value="1050000">

                <button type="submit" id="submit-button">Show Cities</button>
            </form>

            <p id="loading" class="loading hidden">Loading cities...</p>
            <p id="status" class="status">Enter a range and submit the form.</p>
        </section>

        <section class="card">
            <h2>Results</h2>
            <div id="results">No cities loaded yet.</div>
        </section>
    </main>
</body>
</html>
