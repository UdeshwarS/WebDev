/*
    Udeshwar Singh Sandhu
    March 18, 2026
    JavaScript file for the Campus Cart Tycoon assignment.
    It runs the splash animation, models the game with classes,
    handles player actions, updates the view, plays audio,
    and stores only the sound preference in localStorage while preparing result submission.
*/

/**
 * Converts a numeric amount to a dollar display.
 *
 * @param {Number} amount Numeric money value.
 * @returns {String} A formatted dollar string.
 */
function formatMoney(amount) {
    return "$" + amount;
}

/**
 * Checks whether an image has loaded and is safe to draw.
 *
 * @param {HTMLImageElement} imageElement Image element to inspect.
 * @returns {Boolean} True when the image is available.
 */
function imageReady(imageElement) {
    return imageElement.complete && imageElement.naturalWidth > 0;
}

window.addEventListener("load", function () {
    const TOTAL_DAYS = 7;
    const SPLASH_DURATION = 1900;
    const dom = {
        splashScreen: document.getElementById("splash-screen"),
        gameScreen: document.getElementById("game-screen"),
        endScreen: document.getElementById("end-screen"),
        splashCanvas: document.getElementById("splash-canvas"),
        splashHint: document.getElementById("splash-hint"),
        startButton: document.getElementById("start-button"),
        soundButton: document.getElementById("sound-button"),
        helpButton: document.getElementById("help-button"),
        helpModal: document.getElementById("help-modal"),
        closeHelpButton: document.getElementById("close-help-button"),
        historyOpenButton: document.getElementById("history-open-button"),
        historyModal: document.getElementById("history-modal"),
        closeHistoryButton: document.getElementById("close-history-button"),
        cashValue: document.getElementById("cash-value"),
        suppliesValue: document.getElementById("supplies-value"),
        popularityValue: document.getElementById("popularity-value"),
        cartValue: document.getElementById("cart-value"),
        weatherValue: document.getElementById("weather-value"),
        weatherNote: document.getElementById("weather-note"),
        weekLabel: document.getElementById("week-label"),
        weekSteps: document.querySelectorAll(".week-step"),
        priceValue: document.getElementById("price-value"),
        buyDetail: document.getElementById("buy-detail"),
        upgradeCost: document.getElementById("upgrade-cost"),
        reportTextMobile: document.getElementById("report-text-mobile"),
        reportTextDesktop: document.getElementById("report-text-desktop"),
        customersValue: document.getElementById("customers-value"),
        revenueValue: document.getElementById("revenue-value"),
        buyButton: document.getElementById("buy-button"),
        advertiseButton: document.getElementById("advertise-button"),
        upgradeButton: document.getElementById("upgrade-button"),
        openButton: document.getElementById("open-button"),
        priceDownButton: document.getElementById("price-down-button"),
        priceUpButton: document.getElementById("price-up-button"),
        finalMessage: document.getElementById("final-message"),
        finalCash: document.getElementById("final-cash"),
        bestCash: document.getElementById("best-cash"),
        finalPopularity: document.getElementById("final-popularity"),
        finalCartLevel: document.getElementById("final-cart-level"),
        leaderboardForm: document.getElementById("leaderboard-form"),
        resultEmail: document.getElementById("result-email"),
        resultCash: document.getElementById("result-cash"),
        resultCartLevel: document.getElementById("result-cart-level"),
        resultPopularity: document.getElementById("result-popularity"),
        historyList: document.getElementById("history-list"),
        historyModalList: document.getElementById("history-modal-list"),
        playAgainButton: document.getElementById("play-again-button"),
        dingSound: document.getElementById("ding-sound"),
        coinSound: document.getElementById("coin-sound"),
        upgradeSound: document.getElementById("upgrade-sound"),
        errorSound: document.getElementById("error-sound"),
        dayStartSound: document.getElementById("day-start-sound"),
        dayEndSound: document.getElementById("day-end-sound"),
        ambienceSound: document.getElementById("ambience-sound"),
        cartImage: document.getElementById("canvas-cart-image"),
        coinImage: document.getElementById("canvas-coin-image")
    };
    const game = new TycoonGame(TOTAL_DAYS);
    const soundManager = new SoundManager({
        ding: dom.dingSound,
        coin: dom.coinSound,
        upgrade: dom.upgradeSound,
        error: dom.errorSound,
        runStart: dom.dayStartSound,
        runEnd: dom.dayEndSound,
        ambience: dom.ambienceSound,
        button: dom.soundButton
    });
    const splashCoins = createSplashCoins(16);

    bindEventHandlers();
    startSplashAnimation();
    renderGame();
    soundManager.updateButtonLabel();

    /**
     * Connects buttons, modal controls, and keyboard actions.
     *
     * @returns {void} Does not return a value.
     */
    function bindEventHandlers() {
        dom.startButton.addEventListener("click", function () {
            startRun(false);
        });
        dom.playAgainButton.addEventListener("click", function () {
            startRun(true);
        });
        dom.soundButton.addEventListener("click", toggleSound);
        dom.helpButton.addEventListener("click", function () {
            setModalVisibility(dom.helpModal, true);
        });
        dom.closeHelpButton.addEventListener("click", function () {
            setModalVisibility(dom.helpModal, false);
        });
        dom.helpModal.addEventListener("click", handleModalOverlayClick);
        dom.historyOpenButton.addEventListener("click", function () {
            setModalVisibility(dom.historyModal, true);
        });
        dom.historyOpenButton.addEventListener("keydown", handleHistoryKeydown);
        dom.closeHistoryButton.addEventListener("click", function () {
            setModalVisibility(dom.historyModal, false);
        });
        dom.historyModal.addEventListener("click", handleModalOverlayClick);
        dom.buyButton.addEventListener("click", function () {
            runBusinessAction("buySupplies");
        });
        dom.advertiseButton.addEventListener("click", function () {
            runBusinessAction("advertise");
        });
        dom.upgradeButton.addEventListener("click", function () {
            runBusinessAction("upgradeCart");
        });
        dom.priceDownButton.addEventListener("click", function () {
            changePrice(-1);
        });
        dom.priceUpButton.addEventListener("click", function () {
            changePrice(1);
        });
        dom.openButton.addEventListener("click", handleOpenDay);
        window.addEventListener("keydown", handleWindowKeydown);
    }

    /**
     * Creates the moving coin particles used in the splash background.
     *
     * @param {Number} amount Number of particles to create.
     * @returns {Array} Array of coin particle objects.
     */
    function createSplashCoins(amount) {
        const coins = [];
        let index;

        for (index = 0; index < amount; index += 1) {
            coins.push(new SplashCoinParticle());
        }

        return coins;
    }

    /**
     * Plays the splash animation and then reveals the start button.
     *
     * @returns {void} Does not return a value.
     */
    function startSplashAnimation() {
        const context = dom.splashCanvas.getContext("2d");
        let startTime = null;

        function animateFrame(timestamp) {
            if (startTime === null) {
                startTime = timestamp;
            }

            drawSplashFrame(context, timestamp - startTime);

            if (timestamp - startTime < SPLASH_DURATION) {
                window.requestAnimationFrame(animateFrame);
                return;
            }

            dom.splashHint.textContent = "The cart is ready. Tap Start Game.";
            dom.startButton.classList.remove("hidden");
        }

        window.requestAnimationFrame(animateFrame);
    }

    /**
     * Starts a run from the splash screen or play-again button.
     *
     * @param {Boolean} resetCampaign True when starting a fresh campaign.
     * @returns {void} Does not return a value.
     */
    function startRun(resetCampaign) {
        if (resetCampaign) {
            game.resetCampaign();
        }

        closeAllModals();
        switchScreen("game");
        renderGame();
        soundManager.play("runStart");
        soundManager.startAmbience();
    }

    /**
     * Switches the sound state and updates ambience playback.
     *
     * @returns {void} Does not return a value.
     */
    function toggleSound() {
        soundManager.toggle();

        if (!soundManager.enabled) {
            soundManager.stopAmbience();
        }
        else if (dom.gameScreen.classList.contains("active")) {
            soundManager.startAmbience();
        }
    }

    /**
     * Shows or hides one modal overlay.
     *
     * @param {HTMLElement} modalElement Modal element to update.
     * @param {Boolean} shouldShow True to show the modal.
     * @returns {void} Does not return a value.
     */
    function setModalVisibility(modalElement, shouldShow) {
        if (shouldShow) {
            modalElement.classList.remove("hidden");
            modalElement.setAttribute("aria-hidden", "false");
            return;
        }

        modalElement.classList.add("hidden");
        modalElement.setAttribute("aria-hidden", "true");
    }

    /**
     * Closes both modal overlays.
     *
     * @returns {void} Does not return a value.
     */
    function closeAllModals() {
        setModalVisibility(dom.helpModal, false);
        setModalVisibility(dom.historyModal, false);
    }

    /**
     * Opens the history modal with keyboard input.
     *
     * @param {KeyboardEvent} event Browser keyboard event.
     * @returns {void} Does not return a value.
     */
    function handleHistoryKeydown(event) {
        if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            setModalVisibility(dom.historyModal, true);
        }
    }

    /**
     * Closes a modal when its dark overlay is clicked.
     *
     * @param {MouseEvent} event Browser click event.
     * @returns {void} Does not return a value.
     */
    function handleModalOverlayClick(event) {
        if (event.target === event.currentTarget) {
            setModalVisibility(event.currentTarget, false);
        }
    }

    /**
     * Closes open modals when Escape is pressed.
     *
     * @param {KeyboardEvent} event Browser keyboard event.
     * @returns {void} Does not return a value.
     */
    function handleWindowKeydown(event) {
        if (event.key === "Escape") {
            closeAllModals();
        }
    }

    /**
     * Runs one business action such as buying or upgrading.
     *
     * @param {String} actionName Name of the BusinessState method to call.
     * @returns {void} Does not return a value.
     */
    function runBusinessAction(actionName) {
        const actionResult = game.business[actionName]();
        game.reportMessage = actionResult.message;
        soundManager.play(actionResult.sound);
        renderGame();
    }

    /**
     * Changes the drink price and updates the report.
     *
     * @param {Number} amount Dollar amount to add or subtract.
     * @returns {void} Does not return a value.
     */
    function changePrice(amount) {
        const actionResult = game.business.changePrice(amount);
        game.reportMessage = actionResult.message;
        soundManager.play(actionResult.sound);
        renderGame();
    }

    /**
     * Simulates one full selling day.
     *
     * @returns {void} Does not return a value.
     */
    function handleOpenDay() {
        const dayResult = game.openCurrentDay();

        soundManager.play("coin");

        if (!dayResult.finished) {
            renderGame();
            return;
        }

        soundManager.stopAmbience();
        window.setTimeout(function () {
            soundManager.play("runEnd");
        }, 180);
        renderEndScreen();
        switchScreen("end");
    }

    /**
     * Shows the requested screen and hides the others.
     *
     * @param {String} screenName Name of the screen to show.
     * @returns {void} Does not return a value.
     */
    function switchScreen(screenName) {
        dom.splashScreen.classList.remove("active");
        dom.gameScreen.classList.remove("active");
        dom.endScreen.classList.remove("active");

        if (screenName === "game") {
            dom.gameScreen.classList.add("active");
        }
        else if (screenName === "end") {
            dom.endScreen.classList.add("active");
        }
        else {
            dom.splashScreen.classList.add("active");
        }
    }

    /**
     * Updates the game screen using the current model values.
     *
     * @returns {void} Does not return a value.
     */
    function renderGame() {
        const supplyRoom = game.business.getSupplyCapacity() - game.business.supplies;

        dom.cashValue.textContent = formatMoney(game.business.cash);
        dom.suppliesValue.textContent = game.business.supplies +
            " / " + game.business.getSupplyCapacity();
        dom.popularityValue.textContent = String(game.business.popularity);
        dom.cartValue.textContent = "Lv. " + game.business.cartLevel;
        dom.weatherValue.textContent = game.business.currentWeather.name;
        dom.weatherNote.textContent = game.business.currentWeather.note;
        const nextSupplyGain = Math.min(8, Math.max(0, supplyRoom));
        const nextSupplyCost = nextSupplyGain * 3;

        dom.weekLabel.textContent = "Day " + game.business.day +
            " of " + game.business.totalDays;
        dom.priceValue.textContent = formatMoney(game.business.price);
        if (nextSupplyGain === 0) {
            dom.buyDetail.textContent = "Cart full • room: 0";
        }
        else {
            dom.buyDetail.textContent = "+" + nextSupplyGain +
                " supplies for " + formatMoney(nextSupplyCost) +
                " • room: " + supplyRoom;
        }
        dom.upgradeCost.textContent = "Cost: " +
            formatMoney(game.business.getUpgradeCost()) + " • +8 max supply";
        dom.reportTextMobile.textContent = buildMobileReportText();
        dom.reportTextDesktop.textContent = buildDesktopReportText();
        dom.customersValue.textContent = "Last customers: " +
            (game.business.lastCustomers === 0 ? "--" : game.business.lastCustomers);
        dom.revenueValue.textContent = "Last revenue: " +
            formatMoney(game.business.lastRevenue);

        dom.weekSteps.forEach(function (stepElement, index) {
            if (index < game.business.day) {
                stepElement.classList.add("active");
            }
            else {
                stepElement.classList.remove("active");
            }
        });
    }

    /**
     * Builds the compact manager report used on the phone layout.
     *
     * @returns {String} Short report sentence for the compact card.
     */
    function buildMobileReportText() {
        const firstPeriodIndex = game.reportMessage.indexOf(".");

        if (firstPeriodIndex === -1) {
            return game.reportMessage;
        }

        return game.reportMessage.slice(0, firstPeriodIndex + 1);
    }

    /**
     * Builds the longer manager report used on the wider layout.
     *
     * @returns {String} One- or two-sentence desktop report.
     */
    function buildDesktopReportText() {
        let detailText = "Current price is " + formatMoney(game.business.price) +
            " and popularity is " + game.business.popularity + ".";

        if (game.business.lastRevenue > 0) {
            detailText = "Weather is " +
                game.business.currentWeather.name.toLowerCase() +
                " for the next setup, and you have " +
                game.business.supplies + " supplies ready.";
        }

        return game.reportMessage + " " + detailText;
    }

    /**
     * Updates the final results screen from the saved history.
     *
     * @returns {void} Does not return a value.
     */
    function renderEndScreen() {
        dom.finalMessage.textContent = game.finalMessage;
        dom.finalCash.textContent = formatMoney(game.business.cash);
        dom.bestCash.textContent = formatMoney(game.business.cash);
        dom.finalPopularity.textContent = String(game.business.popularity);
        dom.finalCartLevel.textContent = "Lv. " + game.business.cartLevel;
        dom.resultEmail.value = dom.resultEmail.value || dom.resultEmail.dataset.email;
        dom.resultCash.value = String(game.business.cash);
        dom.resultCartLevel.value = String(game.business.cartLevel);
        dom.resultPopularity.value = String(game.business.popularity);
        if (dom.leaderboardForm) {
            dom.leaderboardForm.classList.remove("hidden");
        }
        renderHistoryList(dom.historyList, []);
        renderHistoryList(dom.historyModalList, []);
    }

    /**
     * Renders one list of run history entries.
     *
     * @param {HTMLUListElement} listElement List element to fill.
     * @param {Array} entries History entries to show.
     * @returns {void} Does not return a value.
     */
    function renderHistoryList(listElement, entries) {
        listElement.innerHTML = "";

        if (entries.length === 0) {
            listElement.innerHTML = "<li>No previous history yet.</li>";
            return;
        }

        entries.forEach(function (entry) {
            const listItem = document.createElement("li");
            listItem.textContent = entry.label + " - Cash: " +
                formatMoney(entry.cash) + ", Cart Lv. " +
                entry.cartLevel + ", Popularity " + entry.popularity;
            listElement.appendChild(listItem);
        });
    }

    /**
     * Draws one splash animation frame.
     *
     * @param {CanvasRenderingContext2D} context Canvas drawing context.
     * @param {Number} elapsed Time elapsed in milliseconds.
     * @returns {void} Does not return a value.
     */
    function drawSplashFrame(context, elapsed) {
        const width = dom.splashCanvas.width;
        const height = dom.splashCanvas.height;

        context.clearRect(0, 0, width, height);
        drawSplashBackground(context, width, height);
        drawSplashCoins(context, width, height);
        drawSplashCart(context);
        drawSplashFlag(context);
        drawSplashTitlePanel(context, width, height);
        drawSplashTitle(context, elapsed);
    }

    /**
     * Draws the bright blue splash background.
     *
     * @param {CanvasRenderingContext2D} context Canvas drawing context.
     * @param {Number} width Canvas width.
     * @param {Number} height Canvas height.
     * @returns {void} Does not return a value.
     */
    function drawSplashBackground(context, width, height) {
        const gradient = context.createLinearGradient(0, 0, 0, height);
        gradient.addColorStop(0, "#f7fbff");
        gradient.addColorStop(1, "#d9ebff");
        context.fillStyle = gradient;
        context.fillRect(0, 0, width, height);
    }

    /**
     * Moves and draws the coin particles in the splash background.
     *
     * @param {CanvasRenderingContext2D} context Canvas drawing context.
     * @param {Number} width Canvas width.
     * @param {Number} height Canvas height.
     * @returns {void} Does not return a value.
     */
    function drawSplashCoins(context, width, height) {
        splashCoins.forEach(function (coinParticle) {
            coinParticle.move(width, height);
            coinParticle.draw(context, dom.coinImage);
        });
    }

    /**
     * Draws the cart image or a taller fallback cart.
     *
     * @param {CanvasRenderingContext2D} context Canvas drawing context.
     * @returns {void} Does not return a value.
     */
    function drawSplashCart(context) {
        if (imageReady(dom.cartImage)) {
            context.drawImage(dom.cartImage, 30, 42, 136, 118);
            return;
        }

        context.fillStyle = "#996033";
        context.fillRect(42, 86, 114, 68);
        context.fillStyle = "#ffffff";
        context.fillRect(56, 103, 40, 20);
        context.fillRect(100, 103, 40, 20);
        context.fillStyle = "#724421";
        context.fillRect(36, 76, 126, 14);
        context.fillRect(54, 74, 8, 80);
        context.fillRect(134, 74, 8, 80);
    }

    /**
     * Draws a simple rectangular C.C.T. flag.
     *
     * @param {CanvasRenderingContext2D} context Canvas drawing context.
     * @returns {void} Does not return a value.
     */
    function drawSplashFlag(context) {
        const poleX = 274;

        context.strokeStyle = "#2e4d96";
        context.lineWidth = 6;
        context.beginPath();
        context.moveTo(poleX, 42);
        context.lineTo(poleX, 144);
        context.stroke();

        context.fillStyle = "#2f6df6";
        context.fillRect(poleX, 52, 62, 36);

        context.fillStyle = "#ffffff";
        context.font = 'bold 16px "Trebuchet MS", Arial';
        context.textAlign = "center";
        context.fillText("C.C.T.", poleX + 31, 75);
    }

    /**
     * Draws the lower title panel and accent line.
     *
     * @param {CanvasRenderingContext2D} context Canvas drawing context.
     * @param {Number} width Canvas width.
     * @param {Number} height Canvas height.
     * @returns {void} Does not return a value.
     */
    function drawSplashTitlePanel(context, width, height) {
        context.fillStyle = "#ffe19a";
        context.fillRect(0, 150, width, height - 150);
        context.fillStyle = "#e5b55b";
        context.fillRect(0, 146, width, 5);
    }

    /**
     * Draws the splash title and slogan.
     *
     * @param {CanvasRenderingContext2D} context Canvas drawing context.
     * @param {Number} elapsed Time elapsed in milliseconds.
     * @returns {void} Does not return a value.
     */
    function drawSplashTitle(context, elapsed) {
        const bob = Math.sin(elapsed / 240) * 1.5;

        context.fillStyle = "#503114";
        context.textAlign = "center";
        context.font = 'bold 28px "Arial Black", "Trebuchet MS", Arial';
        context.fillText("Campus Cart Tycoon", 180, 184 + bob);

        context.fillStyle = "#6f4a1c";
        context.font = 'italic bold 16px Georgia, "Times New Roman", serif';
        context.fillText("Brew smart. Price smart. Sell smart.", 180, 208);
    }
});

/**
 * Stores the motion and size of one splash coin particle.
 */
class SplashCoinParticle {
    /**
     * Creates a moving coin particle.
     */
    constructor() {
        this.x = Math.random() * 360;
        this.y = Math.random() * 128 + 8;
        this.size = Math.random() * 18 + 12;
        this.speedX = Math.random() * 0.4 - 0.15;
        this.speedY = Math.random() * 0.28 - 0.12;
        this.alpha = Math.random() * 0.12 + 0.07;
    }

    /**
     * Moves the particle and wraps it around the canvas.
     *
     * @param {Number} width Canvas width.
     * @param {Number} height Canvas height.
     * @returns {void} Does not return a value.
     */
    move(width, height) {
        this.x += this.speedX;
        this.y += this.speedY;

        if (this.x < -26) {
            this.x = width + 20;
        }
        if (this.x > width + 26) {
            this.x = -20;
        }
        if (this.y < -26) {
            this.y = height - 92;
        }
        if (this.y > height - 82) {
            this.y = 10;
        }
    }

    /**
     * Draws the particle with an image or fallback circle.
     *
     * @param {CanvasRenderingContext2D} context Canvas drawing context.
     * @param {HTMLImageElement} coinImage Coin image element.
     * @returns {void} Does not return a value.
     */
    draw(context, coinImage) {
        context.save();
        context.globalAlpha = this.alpha;

        if (imageReady(coinImage)) {
            context.drawImage(coinImage, this.x, this.y, this.size, this.size);
        }
        else {
            context.beginPath();
            context.arc(this.x + this.size / 2, this.y + this.size / 2,
                this.size / 2, 0, Math.PI * 2);
            context.fillStyle = "#ffd24d";
            context.fill();
            context.lineWidth = 2;
            context.strokeStyle = "#f0ab00";
            context.stroke();
        }

        context.restore();
    }
}

/**
 * Manages sound effects and ambience playback.
 */
class SoundManager {
    /**
     * Creates the sound manager.
     *
     * @param {Object} options Sound elements and the toggle button.
     */
    constructor(options) {
        this.sounds = {
            ding: options.ding,
            coin: options.coin,
            upgrade: options.upgrade,
            error: options.error,
            runStart: options.runStart,
            runEnd: options.runEnd,
            ambience: options.ambience
        };
        this.buttonElement = options.button;
        this.storageKey = "campus_cart_tycoon_sound_enabled";
        this.enabled = this.loadState();
        this.sounds.runStart.volume = 0.03;
        this.sounds.runEnd.volume = 0.05;
        this.sounds.ambience.volume = 0.12;
    }

    /**
     * Reads the saved sound preference from localStorage.
     *
     * @returns {Boolean} True when sound is enabled.
     */
    loadState() {
        const savedValue = window.localStorage.getItem(this.storageKey);

        if (savedValue === null) {
            return true;
        }

        return savedValue === "true";
    }

    /**
     * Saves the sound state and refreshes the button label.
     *
     * @returns {void} Does not return a value.
     */
    saveState() {
        window.localStorage.setItem(this.storageKey, String(this.enabled));
        this.updateButtonLabel();
    }

    /**
     * Switches the sound state.
     *
     * @returns {void} Does not return a value.
     */
    toggle() {
        this.enabled = !this.enabled;
        this.saveState();
    }

    /**
     * Updates the sound button label.
     *
     * @returns {void} Does not return a value.
     */
    updateButtonLabel() {
        this.buttonElement.textContent = this.enabled ? "Sound On" : "Sound Off";
    }

    /**
     * Plays one named sound effect if audio is enabled.
     *
     * @param {String} soundName Name of the sound effect.
     * @returns {void} Does not return a value.
     */
    play(soundName) {
        const soundElement = this.sounds[soundName];

        if (!this.enabled || !soundElement) {
            return;
        }

        this.playElement(soundElement, true);
    }

    /**
     * Starts the low-volume store ambience loop.
     *
     * @returns {void} Does not return a value.
     */
    startAmbience() {
        if (!this.enabled || !this.sounds.ambience) {
            return;
        }

        this.sounds.ambience.loop = true;
        this.playElement(this.sounds.ambience, false);
    }

    /**
     * Stops the ambience track.
     *
     * @returns {void} Does not return a value.
     */
    stopAmbience() {
        if (!this.sounds.ambience) {
            return;
        }

        try {
            this.sounds.ambience.pause();
            this.sounds.ambience.currentTime = 0;
        }
        catch (error) {
            return;
        }
    }

    /**
     * Safely plays one audio element.
     *
     * @param {HTMLAudioElement} soundElement Sound element to play.
     * @param {Boolean} resetTime True when playback should restart from 0.
     * @returns {void} Does not return a value.
     */
    playElement(soundElement, resetTime) {
        try {
            if (resetTime) {
                soundElement.currentTime = 0;
            }
            soundElement.play().catch(function () {
                return null;
            });
        }
        catch (error) {
            return;
        }
    }
}

/**
 * Stores the name and sales effect of one weather condition.
 */
class WeatherDay {
    /**
     * Creates one weather object.
     *
     * @param {String} name Display name for the weather.
     * @param {Number} demandBonus Amount added to demand.
     * @param {String} note Small explanation shown to the player.
     */
    constructor(name, demandBonus, note) {
        this.name = name;
        this.demandBonus = demandBonus;
        this.note = note;
    }
}

/**
 * Represents the player's business state across one campaign.
 */
class BusinessState {
    /**
     * Creates the business model with starting values.
     *
     * @param {Number} totalDays Number of rounds in the campaign.
     */
    constructor(totalDays) {
        this.totalDays = totalDays;
        this.day = 1;
        this.cash = 120;
        this.supplies = 18;
        this.popularity = 1;
        this.cartLevel = 1;
        this.price = 6;
        this.lastCustomers = 0;
        this.lastRevenue = 0;
        this.currentWeather = this.generateWeather();
    }

    /**
     * Creates a random weather condition for the day.
     *
     * @returns {WeatherDay} A weather object for the current round.
     */
    generateWeather() {
        const weatherOptions = [
            new WeatherDay("Sunny", 6, "Warm weather brings out more students."),
            new WeatherDay("Cloudy", 2, "A normal day with steady campus traffic."),
            new WeatherDay("Windy", -1, "Some students rush by without stopping."),
            new WeatherDay("Rainy", -5, "Bad weather lowers foot traffic a lot."),
            new WeatherDay("Event Day", 9, "A campus event boosts demand big time.")
        ];
        const randomIndex = Math.floor(Math.random() * weatherOptions.length);
        return weatherOptions[randomIndex];
    }

    /**
     * Returns the total supply capacity of the cart.
     *
     * @returns {Number} Maximum supply storage for the cart.
     */
    getSupplyCapacity() {
        return 24 + ((this.cartLevel - 1) * 8);
    }

    /**
     * Returns the current cart upgrade cost.
     *
     * @returns {Number} Dollar cost of the next cart upgrade.
     */
    getUpgradeCost() {
        return 60 + ((this.cartLevel - 1) * 40);
    }

    /**
     * Buys as many supplies as possible, up to 8, without going over capacity.
     *
     * @returns {Object} Message and sound feedback for the action.
     */
    buySupplies() {
        const pricePerSupply = 3;
        const maxSupplyBuy = 8;
        const remainingRoom = this.getSupplyCapacity() - this.supplies;
        const supplyGain = Math.min(maxSupplyBuy, remainingRoom);
        const supplyCost = supplyGain * pricePerSupply;

        if (remainingRoom <= 0) {
            return {
                message: "No room in the cart. Upgrade first or use up supplies.",
                sound: "error"
            };
        }

        if (this.cash < supplyCost) {
            return {
                message: "Not enough cash to buy more supplies right now.",
                sound: "error"
            };
        }

        this.cash -= supplyCost;
        this.supplies += supplyGain;
        return {
            message: "You bought " + supplyGain +
                " more supplies for " + formatMoney(supplyCost) + ".",
            sound: "ding"
        };
    }

    /**
     * Pays for a simple advertising push.
     *
     * @returns {Object} Message and sound feedback for the action.
     */
    advertise() {
        const adCost = 18;

        if (this.cash < adCost) {
            return {
                message: "You need more cash before you can advertise.",
                sound: "error"
            };
        }

        this.cash -= adCost;
        this.popularity += 1;
        return {
            message: "Flyers went up around campus. Popularity increased by 1.",
            sound: "ding"
        };
    }

    /**
     * Improves the cart so it can hold more supplies and draw more demand.
     *
     * @returns {Object} Message and sound feedback for the action.
     */
    upgradeCart() {
        const upgradeCost = this.getUpgradeCost();

        if (this.cash < upgradeCost) {
            return {
                message: "Upgrade blocked. You need " + formatMoney(upgradeCost) + ".",
                sound: "error"
            };
        }

        this.cash -= upgradeCost;
        this.cartLevel += 1;
        return {
            message: "Nice. Your cart reached level " + this.cartLevel +
                " and now holds 8 more supplies.",
            sound: "upgrade"
        };
    }

    /**
     * Changes the drink price.
     *
     * @param {Number} amount Price change amount, usually 1 or -1.
     * @returns {Object} Message and sound feedback for the action.
     */
    changePrice(amount) {
        const newPrice = this.price + amount;

        if (newPrice < 4 || newPrice > 10) {
            return {
                message: "Keep the price between 4 and 10 dollars.",
                sound: "error"
            };
        }

        this.price = newPrice;
        return {
            message: "You set the drink price to " + formatMoney(this.price) + ".",
            sound: "coin"
        };
    }

    /**
     * Estimates how many customers want to buy today.
     *
     * @returns {Number} Estimated number of interested customers.
     */
    calculateDemand() {
        const demand = 10 +
            (this.popularity * 4) +
            (this.cartLevel * 2) +
            this.currentWeather.demandBonus -
            ((this.price - 6) * 3) +
            (Math.floor(Math.random() * 7) - 2);

        return Math.max(3, demand);
    }

    /**
     * Runs one full sales day and moves the game forward.
     *
     * @returns {Object} Summary information about the completed day.
     */
    openForDay() {
        const demand = this.calculateDemand();
        const sold = Math.min(demand, this.supplies);
        const revenue = sold * this.price;
        const missedCustomers = Math.max(0, demand - sold);
        let summary = "Day " + this.day + ": " + sold +
            " drinks sold for " + formatMoney(revenue) + ".";
        const finished = this.day >= this.totalDays;

        this.cash += revenue;
        this.supplies -= sold;
        this.lastCustomers = demand;
        this.lastRevenue = revenue;

        if (missedCustomers > 0) {
            summary += " You ran out and missed " + missedCustomers + " customers.";
        }
        else {
            summary += " You met all customer demand today.";
        }

        if (!finished) {
            this.day += 1;
            this.currentWeather = this.generateWeather();
        }

        return {
            summary: summary,
            finished: finished
        };
    }
}

/**
 * Manages the business model, saved history, and final results.
 */
class TycoonGame {
    /**
     * Creates the overall game manager.
     *
     * @param {Number} totalDays Number of days in one campaign.
     */
    constructor(totalDays) {
        this.totalDays = totalDays;
        this.initialReport = "Welcome to campus. Set your price and prepare your first day.";
        this.freshStartReport = "Fresh start. Build the best cart on campus this week.";
        this.business = new BusinessState(totalDays);
        this.history = [];
        this.reportMessage = this.initialReport;
        this.finalMessage = "";
    }

    /**
     * Resets the game manager for a fresh run.
     *
     * @returns {void} Does not return a value.
     */
    resetCampaign() {
        this.business = new BusinessState(this.totalDays);
        this.history = [];
        this.reportMessage = this.freshStartReport;
        this.finalMessage = "";
    }

    /**
     * Simulates the current day and ends the campaign if needed.
     *
     * @returns {Object} Object containing whether the campaign has ended.
     */
    openCurrentDay() {
        const result = this.business.openForDay();
        this.reportMessage = result.summary;

        if (result.finished) {
            this.finalMessage = this.buildFinalMessage();
        }

        return {
            finished: result.finished
        };
    }

    /**
     * Builds the text shown on the final screen.
     *
     * @returns {String} Final summary for the player.
     */
    buildFinalMessage() {
        let performanceText = "Solid week. Your cart survived campus life.";

        if (this.business.cash >= 300) {
            performanceText = "Huge week. Your cart became a campus favourite.";
        }
        else if (this.business.cash >= 220) {
            performanceText = "Nice work. You built a strong little business.";
        }
        else if (this.business.cash <= 120) {
            performanceText = "Tough week. Lower prices or buy more supplies next time.";
        }

        return performanceText + " Final cash: " + formatMoney(this.business.cash) + ".";
    }

    /**
     * Loads previous campaign history from localStorage.
     *
     * @returns {Array} Array of saved campaign entries.
     */
    loadHistory() {
        return [];
    }

    /**
     * Loads the persistent run counter from localStorage.
     *
     * @returns {Number} Number of saved completed runs.
     */
    loadRunCount() {
        return 0;
    }

    /**
     * Saves the current campaign to localStorage history.
     *
     * @returns {void} Does not return a value.
     */
    saveHistoryEntry() {
        return;
    }

    /**
     * Returns the best cash score from saved history.
     *
     * @returns {Number} Highest saved cash total.
     */
    getBestCash() {
        return this.business.cash;
    }

    /**
     * Returns a short recent slice of the saved history.
     *
     * @param {Number} amount Maximum number of entries to return.
     * @returns {Array} A limited recent history array.
     */
    getRecentHistory(amount) {
        return [];
    }
}
