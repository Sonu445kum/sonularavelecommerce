@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">📞 Live Support Chat</h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Local Video -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-3">Your Video</h3>
                <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="height: 400px;">
                    <video id="localVideo" autoplay muted playsinline class="w-full h-full object-cover"></video>
                </div>
                <div class="mt-4 flex justify-center space-x-4">
                    <button id="startCall" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition">
                        <i class="fas fa-video"></i> Start Call
                    </button>
                    <button id="toggleVideo" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition" disabled>
                        <i class="fas fa-video-slash"></i>
                    </button>
                    <button id="toggleAudio" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition" disabled>
                        <i class="fas fa-microphone-slash"></i>
                    </button>
                    <button id="endCall" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition" disabled>
                        <i class="fas fa-phone-slash"></i> End
                    </button>
                </div>
            </div>

            <!-- Remote Video -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-3">Support Agent</h3>
                <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="height: 400px;">
                    <video id="remoteVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                    <div id="waitingMessage" class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-user-clock text-6xl mb-4 opacity-50"></i>
                            <p class="text-lg">Waiting for agent to join...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-4">
            <h3 class="text-lg font-semibold mb-3">💬 Chat Messages</h3>
            <div id="chatMessages" class="h-64 overflow-y-auto bg-gray-50 rounded-lg p-4 mb-4 space-y-2">
                <div class="text-gray-400 text-center">Start a call to begin chatting</div>
            </div>
            <div class="flex space-x-2">
                <input type="text" id="chatInput" placeholder="Type a message..." class="flex-1 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" disabled>
                <button id="sendMessage" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition" disabled>
                    Send
                </button>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-semibold text-blue-900 mb-2">📌 Instructions:</h4>
            <ul class="list-disc list-inside text-blue-800 space-y-1 text-sm">
                <li>Click "Start Call" to connect with a support agent</li>
                <li>Allow camera and microphone access when prompted</li>
                <li>Use the controls to mute/unmute your video and audio</li>
                <li>For best experience, use Chrome, Firefox, or Edge browser</li>
                <li><strong>Note:</strong> This is a demo. For production, configure a signaling server.</li>
            </ul>
        </div>
    </div>
</div>

<script>
let localStream;
let remoteStream;
let peerConnection;
let videoEnabled = true;
let audioEnabled = true;

const configuration = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
    ]
};

const startCallBtn = document.getElementById('startCall');
const endCallBtn = document.getElementById('endCall');
const toggleVideoBtn = document.getElementById('toggleVideo');
const toggleAudioBtn = document.getElementById('toggleAudio');
const localVideo = document.getElementById('localVideo');
const remoteVideo = document.getElementById('remoteVideo');
const chatInput = document.getElementById('chatInput');
const sendMessageBtn = document.getElementById('sendMessage');
const chatMessages = document.getElementById('chatMessages');

startCallBtn.addEventListener('click', startCall);
endCallBtn.addEventListener('click', endCall);
toggleVideoBtn.addEventListener('click', toggleVideo);
toggleAudioBtn.addEventListener('click', toggleAudio);
sendMessageBtn.addEventListener('click', sendChatMessage);

async function startCall() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true
        });
        
        localVideo.srcObject = localStream;
        
        peerConnection = new RTCPeerConnection(configuration);
        
        localStream.getTracks().forEach(track => {
            peerConnection.addTrack(track, localStream);
        });
        
        peerConnection.ontrack = (event) => {
            remoteVideo.srcObject = event.streams[0];
            document.getElementById('waitingMessage').style.display = 'none';
        };
        
        peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                // Send candidate to remote peer via signaling server
                console.log('ICE Candidate:', event.candidate);
            }
        };
        
        const offer = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offer);
        
        // Send offer to signaling server
        console.log('Offer created:', offer);
        
        startCallBtn.disabled = true;
        endCallBtn.disabled = false;
        toggleVideoBtn.disabled = false;
        toggleAudioBtn.disabled = false;
        chatInput.disabled = false;
        sendMessageBtn.disabled = false;
        
        addChatMessage('System', 'Call started. Waiting for agent...', 'system');
        
    } catch (error) {
        console.error('Error starting call:', error);
        alert('Could not access camera/microphone. Please check permissions.');
    }
}

function endCall() {
    if (localStream) {
        localStream.getTracks().forEach(track => track.stop());
    }
    
    if (peerConnection) {
        peerConnection.close();
    }
    
    localVideo.srcObject = null;
    remoteVideo.srcObject = null;
    document.getElementById('waitingMessage').style.display = 'flex';
    
    startCallBtn.disabled = false;
    endCallBtn.disabled = true;
    toggleVideoBtn.disabled = true;
    toggleAudioBtn.disabled = true;
    chatInput.disabled = true;
    sendMessageBtn.disabled = true;
    
    addChatMessage('System', 'Call ended.', 'system');
}

function toggleVideo() {
    videoEnabled = !videoEnabled;
    localStream.getVideoTracks()[0].enabled = videoEnabled;
    toggleVideoBtn.innerHTML = videoEnabled ? 
        '<i class="fas fa-video"></i>' : 
        '<i class="fas fa-video-slash"></i>';
    toggleVideoBtn.classList.toggle('bg-gray-600');
}

function toggleAudio() {
    audioEnabled = !audioEnabled;
    localStream.getAudioTracks()[0].enabled = audioEnabled;
    toggleAudioBtn.innerHTML = audioEnabled ? 
        '<i class="fas fa-microphone"></i>' : 
        '<i class="fas fa-microphone-slash"></i>';
    toggleAudioBtn.classList.toggle('bg-gray-600');
}

function sendChatMessage() {
    const message = chatInput.value.trim();
    if (message) {
        addChatMessage('You', message, 'sent');
        chatInput.value = '';
        
        // Send via data channel or signaling server
        console.log('Message sent:', message);
    }
}

function addChatMessage(sender, message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `p-2 rounded ${
        type === 'system' ? 'bg-gray-200 text-gray-700 text-sm text-center' :
        type === 'sent' ? 'bg-blue-100 text-blue-900 ml-auto max-w-md' :
        'bg-gray-100 text-gray-900 mr-auto max-w-md'
    }`;
    messageDiv.innerHTML = `<strong>${sender}:</strong> ${message}`;
    
    if (chatMessages.firstChild.textContent.includes('Start a call')) {
        chatMessages.innerHTML = '';
    }
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

chatInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && !chatInput.disabled) {
        sendChatMessage();
    }
});
</script>
@endsection
