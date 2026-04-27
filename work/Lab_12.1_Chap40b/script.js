document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('slot-form');
    const betInput = document.getElementById('bet');
    const spinButton = document.getElementById('spin-button');
    const creditsSpan = document.getElementById('credits');
    const result = document.getElementById('result');
    const loading = document.getElementById('loading');
    const reelImages = document.querySelectorAll('.reel img');

    function setLoading(isLoading) {
        if (isLoading) {
            loading.classList.remove('hidden');
            spinButton.disabled = true;
            betInput.disabled = true;
        } else {
            loading.classList.add('hidden');
            spinButton.disabled = false;
            betInput.disabled = false;
        }
    }

    function updateReels(reels) {
        for (let i = 0; i < reelImages.length; i += 1) {
            reelImages[i].src = 'images/' + reels[i] + '.png';
            reelImages[i].alt = 'Fruit ' + reels[i];
        }
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        setLoading(true);

        fetch('spin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: new URLSearchParams({ bet: betInput.value })
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data.success) {
                    result.textContent = data.message;
                    if (typeof data.credits !== 'undefined') {
                        creditsSpan.textContent = data.credits;
                        betInput.max = data.credits;
                    }
                    return;
                }

                updateReels(data.reels);
                creditsSpan.textContent = data.credits;
                result.textContent = data.message + ' Winnings: ' + data.winnings + ' credit(s).';
                betInput.max = data.credits;
            })
            .catch(function () {
                result.textContent = 'An error occurred while spinning the reels.';
            })
            .finally(function () {
                setLoading(false);

                const currentCredits = parseInt(creditsSpan.textContent, 10);
                if (currentCredits <= 0) {
                    result.textContent += ' Game over. Use Reset Game to start again.';
                    spinButton.disabled = true;
                    betInput.disabled = true;
                }
            });
    });

    betInput.max = window.initialCredits;
});