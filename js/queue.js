const queue = document.getElementById('queue');
const row_height = 100; // row height + gap, in pixels


// Sample JSON from list.php
// {"id":1,"title":"Test Song A","artist":"Artist A","vote_count":2}

// id
// title
// artist
// vote_count


// Function to render list of songs into rows in the queue div
/* Not used anymore
function renderSongs(songs) {
  queue.textContent = '';

  for (const song of songs) {
    const row = document.createElement('div');
    row.className = 'song';

    const title = document.createElement('strong');
    title.textContent = song.title;

    const artist = document.createElement('span');
    artist.textContent = song.artist;

    const votes = document.createElement('span');
    if (song.vote_count === 0) {
      votes.textContent = 'No votes';
    } else if (song.vote_count === 1) {
      votes.textContent = '1 vote';
    } else {
      votes.textContent = song.vote_count + ' votes';
    }

    row.append(title, ' ', artist, ' ', votes);
    queue.appendChild(row);
  }
}
*/


// Creates a single row for a song instead of looping
function createRow(song) {

  const row = document.createElement('div');
  row.className = 'song';
  row.id = 'song-' + song.id;

  const rank = document.createElement('span');
  rank.id = 'rank-' + song.id;

  const title = document.createElement('strong');
  title.textContent = song.title;

  const artist = document.createElement('span');
  artist.textContent = song.artist;

  const votes = document.createElement('span');
  votes.id = 'votes-' + song.id;

  row.append(rank, ' ', title, ' ', artist, ' ', votes);
  return row;
}

// Returns the vote label
function voteText(count) {
  if (count === 0) {
    return 'No votes';
  } else if (count === 1) {
    return '1 vote';
  } else {
    return count + ' votes';
  }
}

// Renders list of songs by looping
// Create all song rows with createRow
// Update the vote count and move the row
function renderSongs(songs) {
  let position = 0;

  for (const song of songs) {
    let row = document.getElementById('song-' + song.id);

    // New song, new row
    if (row === null) {
      row = createRow(song);
      queue.appendChild(row);
    }

    // Update the vote count
    const votes = document.getElementById('votes-' + song.id);
    votes.textContent = voteText(song.vote_count);

    // Update the rank number shown in the row, allowing typereaders to read position
    const rank = document.getElementById('rank-' + song.id);
    rank.textContent = '#' + (position + 1);

    // Updates the position of the row in the queue
    // Multiplies the position it has by row height
    // This way it moves up or down using pixels
    row.style.top = (position * row_height) + 'px'; // PENDING: CSS needed to reflect this change

    position = position + 1;
  }
}


// Fetches list of song from list.php
// Gets JSON and runs renderSongs with it
async function loadList() {
  try {
    const response = await fetch('list.php');
    if (!response.ok) throw new Error('HTTP ' + response.status);

    const songs = await response.json();
    renderSongs(songs);
  } catch (error) {
    console.log('Could not load queue: ' + error.message);
  }
}


loadList();
setInterval(loadList, 3000);