const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const server = http.createServer(app);
const io = new Server(server, {
  cors: { origin: '*' }
});

// When a socket connects, let it join story rooms
io.on('connection', (socket) => {
  socket.on('join_story', (storyId) => {
    socket.join('story_' + storyId);
  });
});

// REST endpoint so your PHP can notify server a comment was added
app.post('/notify', (req, res) => {
  const { storyId, username, comment, created_at } = req.body;
  if (!storyId) return res.status(400).send('missing storyId');

  // Emit to room for this story
  io.to('story_' + storyId).emit('new_comment', {
    username,
    comment,
    created_at
  });

  res.sendStatus(200);
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
  console.log('Socket server listening on port', PORT);
});
