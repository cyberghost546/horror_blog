document.querySelectorAll('.chat-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.dataset.id;
    const box = document.getElementById('chat-box');
    box.style.display = 'block';
    box.innerHTML = `
      <div class="chat-window">
        <div id="messages"></div>
        <input type="text" id="chat-input" placeholder="Say something scary...">
        <button onclick="sendMessage(${id})">Send</button>
      </div>
    `;
    loadMessages(id);
    setInterval(() => loadMessages(id), 2000);
  });
});

function loadMessages(id) {
  fetch('load-chat.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `receiver_id=${id}`
  })
  .then(res => res.text())
  .then(data => document.getElementById('messages').innerHTML = data);
}

function sendMessage(id) {
  const input = document.getElementById('chat-input');
  const message = input.value.trim();
  if (message === '') return;
  fetch('chat.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `receiver_id=${id}&message=${encodeURIComponent(message)}`
  }).then(() => {
    input.value = '';
    loadMessages(id);
  });
}
