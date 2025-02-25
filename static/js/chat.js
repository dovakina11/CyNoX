/* static/js/chat.js */

/**
 * Handles real-time chat functionality for the CyNoX application.
 */

// Establish a WebSocket connection for real-time communication
const socket = new WebSocket('ws://localhost:5000/chat');

// DOM Elements
const chatBox = document.querySelector('.chat-box');
const messagesList = document.querySelector('.messages');
const messageForm = document.querySelector('.message-form');
const messageInput = document.querySelector('#message');

// Event listener for WebSocket connection open
socket.addEventListener('open', () => {
    console.log('WebSocket connection established.');
});

// Event listener for receiving messages from the server
socket.addEventListener('message', (event) => {
    const data = JSON.parse(event.data);
    appendMessage(data.user, data.message);
});

// Event listener for WebSocket connection close
socket.addEventListener('close', () => {
    console.log('WebSocket connection closed.');
});

// Event listener for WebSocket errors
socket.addEventListener('error', (error) => {
    console.error('WebSocket error:', error);
});

// Function to append a new message to the chat box
function appendMessage(user, message) {
    const messageElement = document.createElement('li');
    messageElement.classList.add('message');
    messageElement.innerHTML = `<strong>${user}:</strong> ${message}`;
    messagesList.appendChild(messageElement);

    // Scroll to the bottom of the chat box
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Event listener for the message form submission
messageForm.addEventListener('submit', (event) => {
    event.preventDefault();

    const message = messageInput.value.trim();
    if (message) {
        // Send the message to the server via WebSocket
        socket.send(JSON.stringify({ message }));

        // Append the message locally
        appendMessage('You', message);

        // Clear the input field
        messageInput.value = '';
    }
});
