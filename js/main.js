console.log("JavaScript loaded successfully.");
// Set target date for the next story drop (example: today + 6 hours)
const nextStoryTime = new Date();
nextStoryTime.setHours(nextStoryTime.getHours() + 6); // 6 hours from now

function updateCountdown() {
    const now = new Date().getTime();
    const distance = nextStoryTime - now;

    if (distance <= 0) {
        document.getElementById("timer").innerText = "00:00:00";
        return;
    }

    const hours = String(Math.floor((distance / (1000 * 60 * 60)))).padStart(2, '0');
    const minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
    const seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');

    document.getElementById("timer").innerText = `${hours}:${minutes}:${seconds}`;
}

updateCountdown(); // Run once immediately

