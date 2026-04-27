// Names: Ayesha Hasan and Udeshwar Sandhu
// Date: February 06, 2025
// JS Pair Assignment JavaScript Code

window.addEventListener("load", function (event) {

    // Game State Variables 
    let per_click_value = 1; //Number of coins gained per click
    let total_coins = 0; //Total Coins Collected thus far 
    let idle = 0; //Auto-Click interval (in seconds)
    let total_upgrades = 0; // Number of upgrades purchased 


    // Reward Trackers 
    let first_hundred = false;
    let first_upgrade = false;
    let unlocked_autoClick = false;
    let bought_twentyFive_Upgrades = false;
    let reached_hundredThousand = false;
    let playRewardSound = false;

    // Timers 
    let autoClickTimerId = null; //Will store ID for auto-click interval
    let rewardTimeout = null; //Will store ID for reward popup timeout

    // Upgrade Values and Costs 
    let upgrade1_value = 1;
    let upgrade1_cost = 10;

    let upgrade2_value = 3;
    let upgrade2_cost = 50;

    let upgrade3_value = 10;
    let upgrade3_cost = 250;

    let upgrade4_value = 10;
    let upgrade4_cost = 200;

    // DOM Elements 
    const help_button = document.getElementById("help");
    const how_to_play = document.getElementById("instructions");
    const click_image = document.getElementById("click_box");
    const total_coins_collected = document.getElementById("score");
    const click = document.getElementById("per_click");
    const time = document.getElementById("time_count");
    const upgrade = document.getElementById("upgrade_count");

    const button1 = document.getElementById("buy_button1");
    const cost1 = document.getElementById("cost1");

    const button2 = document.getElementById("buy_button2");
    const cost2 = document.getElementById("cost2");

    const button3 = document.getElementById("buy_button3");
    const cost3 = document.getElementById("cost3");

    const button4 = document.getElementById("buy_button4");
    const cost4 = document.getElementById("cost4");
    const val4 = document.getElementById("val4");
    const div = document.getElementById("auto");

    const trophy1 = document.getElementById("trophy1");
    const trophy2 = document.getElementById("trophy2");
    const trophy3 = document.getElementById("trophy3");
    const trophy4 = document.getElementById("trophy4");
    const trophy5 = document.getElementById("trophy5");
    const congrats_message1 = document.getElementById("msg1");
    const congrats_message2 = document.getElementById("msg2");
    const congrats_message3 = document.getElementById("msg3");
    const congrats_message4 = document.getElementById("msg4");
    const congrats_message5 = document.getElementById("msg5");
    const reward_description1 = document.getElementById("reward1");
    const reward_description2 = document.getElementById("reward2");
    const reward_description3 = document.getElementById("reward3");
    const reward_description4 = document.getElementById("reward4");
    const reward_description5 = document.getElementById("reward5");

    // Audio
    const coinAudio = new Audio("audio/coin2.mp3");
    const rewardAudio = new Audio("audio/reward.mp3");

    let coinAudioReady = false;
    coinAudio.addEventListener("canplaythrough", function (event) {
        coinAudioReady = true;
    });

    let rewardAudioReady = false;
    rewardAudio.addEventListener("canplaythrough", function (event) {
        rewardAudioReady = true;
    });

    how_to_play.style.display = "none"; // Hide the Help Intructions initially 

    /**
  * Checks all reward conditions and displays popups when milestones are reached.
  * Updates trophy images and reward descriptions accordingly.
  *
  * @param {void}
  * @returns {void}
  */
    function check_rewards() {
        // 100 coins milestone
        if (total_coins >= 100 && !(first_hundred)) {
            first_hundred = true;
            congrats_message2.style.display = "block";
            trophy2.src = "images/trophy.png";
            reward_description2.style.color = "#cc5b0f";

            setTimeout(function () {
                congrats_message2.style.display = "none";
            }, 5000);
            playRewardSound = true;
        }

        // First upgrade milestone
        if (total_upgrades == 1 && !(first_upgrade)) {
            first_upgrade = true;
            congrats_message1.style.display = "block";
            trophy1.src = "images/trophy.png";
            reward_description1.style.color = "#cc5b0f";

            setTimeout(function () {
                congrats_message1.style.display = "none";
            }, 5000);
            playRewardSound = true;
        }

        // Auto-click unlocked milestone
        if (idle == 10 && !(unlocked_autoClick)) {
            unlocked_autoClick = true;
            congrats_message3.style.display = "block";
            trophy3.src = "images/trophy.png";
            reward_description3.style.color = "#cc5b0f";

            setTimeout(function () {
                congrats_message3.style.display = "none";
            }, 5000);
            playRewardSound = true;
        }

        // 25 upgrades milestone
        if (total_upgrades == 25 && !(bought_twentyFive_Upgrades)) {
            bought_twentyFive_Upgrades = true;
            congrats_message4.style.display = "block";
            trophy4.src = "images/trophy.png";
            reward_description4.style.color = "#cc5b0f";

            setTimeout(function () {
                congrats_message4.style.display = "none";
            }, 5000);
            playRewardSound = true;
        }

        // 100,000 coins milestone
        if (total_coins >= 100000 && !(reached_hundredThousand)) {
            reached_hundredThousand = true;
            congrats_message5.style.display = "block";
            trophy5.src = "images/trophy.png";
            reward_description5.style.color = "#cc5b0f";

            setTimeout(function () {
                congrats_message5.style.display = "none";
            }, 5000);
            playRewardSound = true;
        }

        // Play reward sound once
        if (playRewardSound) {
            playRewardSoundFunc();
            playRewardSound = false;
        }
    };

    /**
    * Plays the reward sound effect if the audio is ready.
    *
    * @param {void}
    * @returns {void}
    */
    function playRewardSoundFunc() {
        if (rewardAudioReady) {
            const sound = rewardAudio.cloneNode();
            sound.play();
        }
    }

    /**
     * Enables or disables upgrade buttons depending on whether
     * the player has enough coins to afford them.
     *
     * @param {void}
     * @returns {void}
     */
    function check_what_i_can_buy() {
        if (total_coins >= upgrade1_cost) {
            button1.disabled = false;
        } else { button1.disabled = true };

        if (total_coins >= upgrade2_cost) {
            button2.disabled = false;
        } else { button2.disabled = true };

        if (total_coins >= upgrade3_cost) {
            button3.disabled = false;
        } else { button3.disabled = true };

        if (total_coins >= upgrade4_cost) {
            button4.disabled = false;
        } else { button4.disabled = true };
    };

    /**
     * Plays the coin sound effect when the player clicks.
     * Uses a cloned audio node to prevent console new load request errors.
     *
     * @param {void}
     * @returns {void}
     */
    function playCoinSound() {
        if (coinAudioReady) {
            const sound = coinAudio.cloneNode();
            sound.play();
        }
    }

    /**
     * Handles when the user clicks the box.
     * Increases total coins, updates the display,
     * checks upgrades and rewards, and plays sound.
     *
     * @param {void}
     * @returns {void}
     */
    function boxClicked() {
        total_coins += per_click_value;
        total_coins_collected.innerHTML = total_coins;
        check_what_i_can_buy();
        check_rewards();
        playCoinSound();
    }

    /**
     * Hides the auto-click upgrade section when it is no longer available.
     *
     * @param {void}
     * @returns {void}
     */
    function hide_upgrade() {
        div.style.display = "none";
    }

    /**
     * Cancels previous auto-click timers if they exist.
     * Starts a new automatic clicking interval.
     * Repeatedly calls boxClicked() every given time interval.
     *
     * @param {Number} amount - This is the Time interval in milliseconds between clicks
     * @returns {void}
     */
    function auto_click(amount) {
        if (autoClickTimerId != null) {
            clearTimeout(autoClickTimerId)
        }

        autoClickTimerId = setInterval(boxClicked, amount);
    };


    //EVENT LISTENERS:
    /**
     * Help button shows and hides instructions when clicked 
     */
    help_button.addEventListener("click", function (event) {
        if (how_to_play.style.display == "none") {
            how_to_play.style.display = "block";
        }
        else {
            how_to_play.style.display = "none";
        }
    });

    // When the main image is clicked, add coins and update game state
    click_image.addEventListener("click", function (event) {
        boxClicked();
    });


// Purchased Upgrade 1: increase click power, spend coins, update game view and model 
    button1.addEventListener("click", function (event) {
        per_click_value += upgrade1_value;
        total_coins = total_coins - upgrade1_cost;
        total_upgrades++;
        upgrade.innerHTML = total_upgrades;
        total_coins_collected.innerHTML = total_coins;
        click.innerHTML = per_click_value;
        upgrade1_cost += (Math.floor(Math.random() * (100 - 20 + 1)) + 20) + Math.floor(total_coins * 0.01) + (per_click_value);  // Increase cost with randomness and scale 
        cost1.innerHTML = upgrade1_cost;
        button1.disabled = true;
        check_what_i_can_buy();
        check_rewards();
    });

    // Purchased Upgrade 2: increase click power, spend coins, update game view and model 
    button2.addEventListener("click", function (event) {
        per_click_value += upgrade2_value;
        total_coins = total_coins - upgrade2_cost;
        total_upgrades++;
        upgrade.innerHTML = total_upgrades;
        total_coins_collected.innerHTML = total_coins;
        click.innerHTML = per_click_value;
        upgrade2_cost += (Math.floor(Math.random() * (300 - 50 + 1)) + 50) + Math.floor(total_coins * 0.02) + (per_click_value);
        cost2.innerHTML = upgrade2_cost;
        button2.disabled = true;
        check_what_i_can_buy();
        check_rewards();
    });

    // Purchased Upgrade 3: increase click power, spend coins, update game view and model 
    button3.addEventListener("click", function (event) {
        per_click_value += upgrade3_value;
        total_coins = total_coins - upgrade3_cost;
        total_upgrades++;
        upgrade.innerHTML = total_upgrades;
        total_coins_collected.innerHTML = total_coins;
        click.innerHTML = per_click_value;
        upgrade3_cost += (Math.floor(Math.random() * (900 - 100 + 1)) + 100) + Math.floor(total_coins * 0.02) + (per_click_value);
        cost3.innerHTML = upgrade3_cost;
        button3.disabled = true;
        check_what_i_can_buy();
        check_rewards();
    });

    // Purchased Upgrade 4: enable auto-click, spend coins, update game view and model 
    button4.addEventListener("click", function (event) {
        idle = upgrade4_value;
        total_coins = total_coins - upgrade4_cost;
        total_upgrades++;
        upgrade.innerHTML = total_upgrades;
        total_coins_collected.innerHTML = total_coins;
        time.innerHTML = idle;
        upgrade4_cost += (Math.floor(Math.random() * (2000 - 500 + 1)) + 500) + Math.floor(total_coins * 0.03) + (per_click_value);
        cost4.innerHTML = upgrade4_cost;
        button4.disabled = true;
        check_what_i_can_buy();
        check_rewards();
        auto_click(upgrade4_value * 1000);
        upgrade4_value -= 1;
        if (upgrade4_value == 0) {
            hide_upgrade();
        }
        else {
            val4.innerHTML = upgrade4_value;
        }
    });

    function auto_click(amount) {
        if (autoClickTimerId != null) {
            clearInterval(autoClickTimerId)
        }

        autoClickTimerId = setInterval(boxClicked, amount);
    };

});