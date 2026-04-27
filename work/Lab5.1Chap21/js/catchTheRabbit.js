window.addEventListener("load", function () {
    const rabbits = [
        document.getElementById("rabbit1"),
        document.getElementById("rabbit2"),
        document.getElementById("rabbit3"),
        document.getElementById("rabbit4")
    ];
    const noEggs = document.getElementById("noeggs");
    const slow = document.getElementById("slow");

    let currentRabbit = 0;
    let attempts = 0;

    function showRabbit(index) {
        let i;
        for (i = 0; i < rabbits.length; i++) {
            rabbits[i].style.visibility = "hidden";
        }
        if (index >= 0 && index < rabbits.length) {
            rabbits[index].style.visibility = "visible";
        }
    }

    function moveRabbit() {
        attempts = attempts + 1;

        if (attempts >= 20) {
            showRabbit(-1);
            noEggs.style.visibility = "hidden";
            slow.style.visibility = "visible";
            return;
        }

        if (attempts >= 4) {
            showRabbit(-1);
            noEggs.style.visibility = "visible";
            slow.style.visibility = "hidden";
            return;
        }

        currentRabbit = currentRabbit + 1;
        showRabbit(currentRabbit);
    }

    let i;
    for (i = 0; i < rabbits.length; i++) {
        rabbits[i].addEventListener("mouseover", moveRabbit);
    }
});