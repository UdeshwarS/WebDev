function randomInt0To100() {
  return Math.floor(Math.random() * 101);
}

function randomColorRedOrWhite() {
  return Math.random() < 0.5 ? "red" : "white";
}

function createBall() {
  return {
    color: randomColorRedOrWhite(),
    points: randomInt0To100()
  };
}

// Exercise 3 behavior (show a single ball each load) is still satisfied here
// because we create balls and show them when drawn.
// But we also do Exercise 4: array of 100, draw by index, scoring, stop on red.

const balls = [];
for (let i = 0; i < 100; i++) {
  balls.push(createBall());
}

const drawn = [];
for (let i = 0; i < 100; i++) {
  drawn.push(false);
}

let score = 0;

alert("Welcome to the Lottery Ball Game!\nYou can draw balls by index (0 to 99).");

while (true) {
  const keepGoing = confirm("Draw a ball? (OK = yes, Cancel = quit)");
  if (!keepGoing) {
    alert("You quit. Your total score is: " + score);
    break;
  }

  const input = prompt("Enter a ball index from 0 to 99:");
  if (input === null) {
    alert("Cancelled input. Your total score is: " + score);
    break;
  }

  const index = Number(input);

  if (!Number.isInteger(index)) {
    alert("Please enter a whole number (integer).");
    continue;
  }

  if (index < 0 || index > 99) {
    alert("Invalid index. Must be between 0 and 99.");
    continue;
  }

  if (drawn[index]) {
    alert("You already drew ball " + index + ". Try a different one.");
    continue;
  }

  drawn[index] = true;
  const ball = balls[index];

  alert("Ball " + index + ":\nColor: " + ball.color + "\nPoints: " + ball.points);

  if (ball.color === "red") {
    score = score - ball.points;
    alert("RED ball! You lose " + ball.points + " points.\nFinal score: " + score);
    break;
  } else {
    score = score + ball.points;
    alert("WHITE ball! You gain " + ball.points + " points.\nCurrent score: " + score);
  }
}
