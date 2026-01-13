<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>PSI AFRICA - Messagerie Interne</title>
  <link rel="icon" href="{{ asset('favicon.png') }}"/>
  <style>
    :root {
      --primary-color: #0066cc;
      --primary-dark: #004999;
      --danger-color: #f44336;
      --light-bg: #f0f2f5;
      --white: #fff;
      --text-dark: #333;
      --text-light: #555;
      --border-color: #ddd;
      --success-color: #4CAF50;
      --warning-color: #ff9800;
      --voice-color: #9c27b0;
      --video-color: #2196F3;
      --sidebar-bg: #f8f9fa;
      --hover-bg: #e9ecef;
    }
    
    * { 
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body { 
      font-family: 'Segoe UI', Arial, sans-serif; 
      background: var(--light-bg); 
      color: var(--text-dark);
      overflow: hidden;
    }
    
    .hidden { display: none !important; }

    .loading-screen {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      flex-direction: column;
      gap: 15px;
    }

    .spinner {
      border: 4px solid var(--light-bg);
      border-top: 4px solid var(--primary-color);
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .main-app { 
      display: flex;
      height: 100vh;
      width: 100%;
    }
    
    /* SIDEBAR STYLES */
    .sidebar {
      width: 350px;
      background: var(--white);
      border-right: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      height: 100vh;
    }

    .sidebar-header {
      padding: 15px 20px;
      background: var(--primary-color);
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .sidebar-header h2 {
      font-size: 18px;
      font-weight: 600;
      margin: 0;
    }

    .sidebar-header .user-info {
      font-size: 12px;
      opacity: 0.9;
      margin-top: 3px;
    }

    .sidebar-search {
      padding: 12px 15px;
      background: var(--sidebar-bg);
      border-bottom: 1px solid var(--border-color);
    }

    .sidebar-search input {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid var(--border-color);
      border-radius: 20px;
      font-size: 14px;
      outline: none;
    }

    .sidebar-search input:focus {
      border-color: var(--primary-color);
    }

    .conversations-list {
      flex: 1;
      overflow-y: auto;
      background: var(--white);
    }

    .conversation-item {
      padding: 12px 15px;
      border-bottom: 1px solid #f0f0f0;
      cursor: pointer;
      transition: background 0.2s;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .conversation-item:hover {
      background: var(--hover-bg);
    }

    .conversation-item.active {
      background: #e3f2fd;
      border-left: 3px solid var(--primary-color);
    }

    .conversation-item.all-users {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-weight: 600;
    }

    .conversation-item.all-users:hover {
      background: linear-gradient(135deg, #5568d3 0%, #65398b 100%);
    }

    .conversation-item.no-messages {
      opacity: 0.7;
    }

    .conversation-avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: var(--primary-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      font-weight: 600;
      flex-shrink: 0;
      position: relative;
    }

    .conversation-item.all-users .conversation-avatar {
      background: rgba(255,255,255,0.2);
    }

    .online-status {
      position: absolute;
      bottom: 2px;
      right: 2px;
      width: 12px;
      height: 12px;
      background: var(--success-color);
      border: 2px solid white;
      border-radius: 50%;
    }

    .conversation-info {
      flex: 1;
      min-width: 0;
    }

    .conversation-header {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      margin-bottom: 4px;
    }

    .conversation-name {
      font-weight: 600;
      font-size: 15px;
      color: var(--text-dark);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .conversation-item.all-users .conversation-name {
      color: white;
    }

    .conversation-time {
      font-size: 12px;
      color: var(--text-light);
      flex-shrink: 0;
    }

    .conversation-item.all-users .conversation-time {
      color: rgba(255,255,255,0.9);
    }

    .conversation-preview {
      font-size: 13px;
      color: var(--text-light);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .conversation-item.all-users .conversation-preview {
      color: rgba(255,255,255,0.85);
    }

    .conversation-item.no-messages .conversation-preview {
      font-style: italic;
      color: #999;
    }

    .unread-badge {
      background: var(--danger-color);
      color: white;
      border-radius: 10px;
      padding: 2px 8px;
      font-size: 11px;
      font-weight: 600;
      margin-left: auto;
      flex-shrink: 0;
    }

    .conversation-item.all-users .unread-badge {
      background: rgba(255,255,255,0.3);
    }

    /* CHAT CONTAINER */
    .chat-container { 
      display: flex; 
      flex-direction: column; 
      height: 100vh; 
      flex: 1;
      background: white; 
    }
    
    .chat-header {
      padding: 12px 20px; 
      background: var(--primary-color); 
      color: white; 
      display: flex;
      justify-content: space-between;
      align-items: center;
      min-height: 60px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .chat-header-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .chat-header-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255,255,255,0.2);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      font-weight: 600;
    }

    .chat-header-text h1 {
      font-size: 16px;
      font-weight: 600;
      margin: 0;
    }

    .chat-header-text small {
      opacity: 0.9;
      font-size: 12px;
    }
    
    .chat-messages { 
      flex: 1; 
      padding: 20px; 
      overflow-y: auto; 
      background: #fafafa;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .message-date-divider {
      text-align: center;
      margin: 15px 0 10px 0;
      position: relative;
    }

    .message-date-divider span {
      background: var(--light-bg);
      padding: 5px 15px;
      border-radius: 15px;
      font-size: 12px;
      color: var(--text-light);
      font-weight: 500;
    }
    
    .message {
      padding: 8px 12px;
      border-radius: 12px;
      max-width: 65%;
      word-wrap: break-word;
      position: relative;
      animation: slideIn 0.2s ease-out;
      box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .message.own {
      align-self: flex-end;
      background-color: var(--primary-color);
      color: white;
      border-bottom-right-radius: 4px;
    }
    
    .message.other {
      align-self: flex-start;
      background-color: white;
      color: var(--text-dark);
      border-bottom-left-radius: 4px;
      border: 1px solid #e0e0e0;
    }

    .message.system {
      align-self: center;
      background-color: #fff3cd;
      color: #856404;
      border-radius: 6px;
      max-width: 85%;
      font-style: italic;
      text-align: center;
      padding: 6px 10px;
      font-size: 13px;
    }

    .message.voice {
      border: 2px solid var(--voice-color);
    }

    .message-header {
      font-weight: 600;
      font-size: 13px;
      margin-bottom: 4px;
    }

    .message-content {
      font-size: 14px;
      line-height: 1.4;
    }

    .message-time {
      font-size: 10px;
      opacity: 0.7;
      margin-top: 4px;
      text-align: right;
    }

    .voice-message-player {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 5px;
    }

    .voice-message-player audio {
      flex: 1;
      height: 30px;
    }

    .voice-duration {
      font-size: 11px;
      opacity: 0.8;
    }

    button { 
      padding: 8px 16px; 
      background: var(--primary-color); 
      color: white; 
      border: none; 
      border-radius: 4px; 
      cursor: pointer; 
      font-size: 14px;
      font-weight: 500;
      transition: all 0.2s;
    }
    
    button:hover { 
      background: var(--primary-dark);
      transform: translateY(-1px);
    }
    button:disabled { opacity: 0.5; cursor: not-allowed; }

    .btn-danger { background: var(--danger-color); }
    .btn-danger:hover { background: #d32f2f; }

    .btn-success { background: var(--success-color); }
    .btn-success:hover { background: #388E3C; }

    .btn-voice {
      background: var(--voice-color);
    }

    .btn-voice:hover {
      background: #7b1fa2;
    }

    .btn-voice.recording {
      background: var(--danger-color);
      animation: pulse 1.5s infinite;
    }

    .btn-video {
      background: var(--video-color);
    }

    .btn-video:hover {
      background: #1976D2;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }

    .chat-input-container {
      display: flex;
      flex-direction: column;
      padding: 10px 15px;
      gap: 8px;
      background: white;
      border-top: 1px solid var(--border-color);
    }
    
    .chat-input { 
      display: flex; 
      gap: 8px;
    }
    
    .chat-input input { 
      flex: 1; 
      padding: 10px 15px; 
      border: 1px solid var(--border-color); 
      border-radius: 20px; 
      font-size: 14px;
      outline: none;
    }

    .chat-input input:focus {
      border-color: var(--primary-color);
    }
    
    .chat-input button { 
      border-radius: 20px; 
      padding: 10px 20px;
    }

    .voice-controls {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .voice-controls button {
      font-size: 13px;
      padding: 7px 14px;
      border-radius: 20px;
    }

    .recording-indicator {
      display: flex;
      align-items: center;
      gap: 6px;
      color: var(--danger-color);
      font-size: 13px;
    }

    .recording-dot {
      width: 10px;
      height: 10px;
      background: var(--danger-color);
      border-radius: 50%;
      animation: blink 1s infinite;
    }

    @keyframes blink {
      0%, 50% { opacity: 1; }
      51%, 100% { opacity: 0.3; }
    }

    .chat-actions {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .chat-actions button {
      padding: 8px 12px;
      font-size: 13px;
    }

    /* GROUP CALL MODAL */
    .group-call-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.8);
      z-index: 1500;
      display: none;
      justify-content: center;
      align-items: center;
    }

    .group-call-modal.active {
      display: flex;
    }

    .group-call-container {
      background: white;
      width: 90%;
      max-width: 500px;
      border-radius: 8px;
      padding: 25px;
      max-height: 80vh;
      overflow-y: auto;
    }

    .group-call-container h3 {
      margin: 0 0 20px 0;
      color: var(--video-color);
      font-size: 20px;
    }

    .user-selection-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 20px;
    }

    .user-selection-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .user-selection-item:hover {
      background: var(--hover-bg);
    }

    .user-selection-item.selected {
      background: #e3f2fd;
      border-color: var(--primary-color);
    }

    .user-selection-item input[type="checkbox"] {
      width: 20px;
      height: 20px;
      cursor: pointer;
    }

    .user-selection-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--primary-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      font-weight: 600;
    }

    .user-selection-info {
      flex: 1;
    }

    .user-selection-name {
      font-weight: 600;
      font-size: 14px;
    }

    .user-selection-role {
      font-size: 12px;
      color: var(--text-light);
    }

    .group-call-actions {
      display: flex;
      gap: 10px;
      justify-content: space-between;
      margin-top: 20px;
    }

    .group-call-actions .selected-count {
      align-self: center;
      color: var(--text-light);
      font-size: 14px;
    }

    /* MODALS */
    .audio-preview-modal,
    .video-invitation-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.8);
      z-index: 1000;
      display: none;
      justify-content: center;
      align-items: center;
    }

    .audio-preview-modal.active,
    .video-invitation-modal.active {
      display: flex;
    }

    .audio-preview-container,
    .video-invitation-container {
      background: white;
      width: 90%;
      max-width: 450px;
      border-radius: 8px;
      padding: 25px;
      text-align: center;
    }

    .audio-preview-container h3 {
      margin: 0 0 15px 0;
      color: var(--voice-color);
      font-size: 20px;
    }

    .audio-preview-player {
      background: #f5f5f5;
      padding: 15px;
      border-radius: 6px;
      margin: 15px 0;
    }

    .audio-preview-player audio {
      width: 100%;
      margin-top: 10px;
    }

    .audio-preview-actions {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin-top: 15px;
    }

    .audio-preview-actions button {
      flex: 1;
      max-width: 150px;
    }

    .video-invitation-modal {
      z-index: 3000;
    }

    .video-invitation-container {
      animation: slideInDown 0.3s ease-out;
    }

    @keyframes slideInDown {
      from {
        opacity: 0;
        transform: translateY(-50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .video-invitation-container h2 {
      margin: 0 0 10px 0;
      color: var(--video-color);
      font-size: 24px;
    }

    .video-invitation-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: var(--video-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      margin: 15px auto;
      animation: pulse 2s infinite;
    }

    .video-invitation-caller {
      font-size: 20px;
      font-weight: bold;
      margin: 10px 0;
      color: var(--text-dark);
    }

    .video-invitation-text {
      color: var(--text-light);
      margin-bottom: 20px;
      font-size: 14px;
    }

    .video-invitation-actions {
      display: flex;
      gap: 10px;
      justify-content: center;
    }

    .video-invitation-actions button {
      flex: 1;
      max-width: 150px;
      padding: 12px;
      font-size: 15px;
    }

    .video-call-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #1a1a1a;
      z-index: 2000;
      display: none;
      flex-direction: column;
    }

    .video-call-container.active {
      display: flex;
    }

    .video-grid {
      flex: 1;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 10px;
      padding: 15px;
      overflow: auto;
    }

    .video-participant {
      position: relative;
      background: #000;
      border-radius: 8px;
      overflow: hidden;
      min-height: 200px;
    }

    .video-participant video {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .participant-info {
      position: absolute;
      bottom: 10px;
      left: 10px;
      background: rgba(0,0,0,0.7);
      color: white;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 13px;
    }

    .video-controls-bar {
      display: flex;
      justify-content: center;
      gap: 12px;
      padding: 15px;
      background: rgba(0,0,0,0.8);
    }

    .video-controls-bar button {
      border-radius: 50%;
      width: 55px;
      height: 55px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
    }

    .video-controls-bar .btn-danger {
      width: 65px;
      height: 65px;
    }

    .new-message-indicator {
      position: fixed;
      top: 70px;
      right: 20px;
      background: var(--success-color);
      color: white;
      padding: 12px 20px;
      border-radius: 6px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      z-index: 1000;
      display: none;
      animation: slideInRight 0.5s ease-out;
      font-size: 14px;
    }

    .new-message-indicator.show {
      display: block;
    }

    @keyframes slideInRight {
      from {
        transform: translateX(400px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    .video-grid.group-mode {
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }

    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      color: var(--text-light);
      padding: 40px;
      text-align: center;
    }

    .empty-state-icon {
      font-size: 64px;
      margin-bottom: 20px;
      opacity: 0.5;
    }

    .empty-state h3 {
      font-size: 20px;
      margin-bottom: 10px;
      color: var(--text-dark);
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #555;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 280px;
      }

      .chat-header {
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
        padding: 10px 15px;
      }

      .message {
        max-width: 85%;
      }

      .voice-controls {
        flex-direction: column;
        width: 100%;
      }

      .voice-controls button {
        width: 100%;
      }

      .video-grid {
        grid-template-columns: 1fr;
        padding: 10px;
      }
    }
  </style>
</head>
<body>
  <div id="loadingScreen" class="loading-screen">
    <div class="spinner"></div>
    <p>Chargement de la messagerie...</p>
  </div>

  <div id="mainApp" class="main-app hidden">
    <!-- SIDEBAR -->
    <div class="sidebar">
      <div class="sidebar-header">
        <div>
          <h2>💬 Messagerie</h2>
          <div class="user-info">
            <span id="sidebarUserName"></span> • <span id="sidebarUserRole"></span>
          </div>
        </div>
        <button class="btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="window.location.href='{{ route('dashboard') }}'">
          🏠
        </button>
      </div>

      <div class="sidebar-search">
        <input type="text" id="searchUsers" placeholder="🔍 Rechercher une conversation..." onkeyup="filterConversations()">
      </div>

      <div class="conversations-list" id="conversationsList">
        <!-- Les conversations seront ajoutées ici -->
      </div>
    </div>

    <!-- CHAT CONTAINER -->
    <div id="chatContainer" class="chat-container">
      <div class="chat-header">
        <div class="chat-header-info">
          <div class="chat-header-avatar" id="chatHeaderAvatar">👥</div>
          <div class="chat-header-text">
            <h1 id="chatHeaderName">Sélectionnez une conversation</h1>
            <small id="chatHeaderStatus"></small>
          </div>
        </div>
        <div class="chat-actions">
          <button class="btn-video" onclick="initiateVideoCall()">📹 Appel</button>
          <button class="btn-success" id="selectMultipleBtn" onclick="openGroupCallModal()">👥 Groupe</button>
        </div>
      </div>
      
      <div class="chat-messages" id="chatMessages">
        <div class="empty-state">
          <div class="empty-state-icon">💬</div>
          <h3>Bienvenue dans la messagerie PSI AFRICA</h3>
          <p>Sélectionnez une conversation pour commencer</p>
        </div>
      </div>
      
      <div class="chat-input-container">
        <div class="chat-input">
          <input type="text" id="chatInput" placeholder="Écrire un message..." onkeypress="handleKeyPress(event)">
          <button id="sendButton" onclick="sendMessage()">📤</button>
        </div>

        <div class="voice-controls">
          <button id="recordButton" class="btn-voice" onclick="toggleRecording()">
            🎤 Vocal
          </button>
          <div id="recordingIndicator" class="recording-indicator hidden">
            <div class="recording-dot"></div>
            <span>Enregistrement...</span>
            <span id="recordingTimer">0:00</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- GROUP CALL MODAL -->
  <div id="groupCallModal" class="group-call-modal">
    <div class="group-call-container">
      <h3>👥 Sélectionner les participants pour l'appel de groupe</h3>
      
      <div id="userSelectionList" class="user-selection-list">
        <!-- Liste des utilisateurs sera générée ici -->
      </div>

      <div class="group-call-actions">
        <span class="selected-count" id="selectedCountText">0 participant(s) sélectionné(s)</span>
        <div style="display: flex; gap: 10px;">
          <button class="btn-danger" onclick="closeGroupCallModal()">Annuler</button>
          <button class="btn-video" id="startGroupCallBtn" onclick="startGroupVideoCall()" disabled>Démarrer l'appel</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODALS -->
  <div id="audioPreviewModal" class="audio-preview-modal">
    <div class="audio-preview-container">
      <h3>🎤 Prévisualisation</h3>
      <div class="audio-preview-player">
        <p style="margin: 0 0 8px 0; color: var(--text-light); font-size: 13px;">
          Durée : <span id="previewDuration">0:00</span>
        </p>
        <audio id="previewAudio" controls></audio>
      </div>
      <div class="audio-preview-actions">
        <button class="btn-danger" onclick="cancelAudioPreview()">❌ Annuler</button>
        <button class="btn-success" onclick="confirmSendAudio()">📤 Envoyer</button>
      </div>
    </div>
  </div>

  <div id="videoInvitationModal" class="video-invitation-modal">
    <div class="video-invitation-container">
      <h2>📹 Appel vidéo</h2>
      <div class="video-invitation-avatar">📹</div>
      <div class="video-invitation-caller" id="callerName"></div>
      <p class="video-invitation-text" id="invitationText">vous invite à un appel vidéo</p>
      <div class="video-invitation-actions">
        <button class="btn-danger" onclick="rejectVideoCall()">❌ Refuser</button>
        <button class="btn-success" onclick="acceptVideoCall()">✅ Accepter</button>
      </div>
    </div>
  </div>

  <div id="videoCallContainer" class="video-call-container">
    <div class="video-grid" id="videoGrid"></div>
    <div class="video-controls-bar">
      <button id="toggleMic" class="btn-video" onclick="toggleMicrophone()" title="Microphone">🎤</button>
      <button id="toggleVideo" class="btn-video" onclick="toggleCamera()" title="Caméra">📹</button>
      <button class="btn-danger" onclick="endVideoCall()" title="Raccrocher">📞</button>
    </div>
  </div>

  <div id="newMessageIndicator" class="new-message-indicator">
    <div style="display: flex; align-items: center; gap: 8px;">
      <span style="font-size: 20px;">💬</span>
      <div>
        <strong id="newMessageFrom" style="font-size: 13px;">Nouveau message</strong>
        <div id="newMessageText" style="font-size: 12px; opacity: 0.9;"></div>
      </div>
    </div>
  </div>

  <audio id="notificationSound" preload="auto">
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZURE" type="audio/wav">
  </audio>

  <audio id="videoCallRingtone" loop preload="auto">
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZURE" type="audio/wav">
  </audio>

  <script>
    const CURRENT_USER = {
      id: {{ Auth::id() }},
      name: "{{ Auth::user()->name }}",
      email: "{{ Auth::user()->email }}",
      role: "{{ Auth::user()->getRoleNames()->first() ?? 'User' }}"
    };

    const API_BASE_URL = '/messagerie/api';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const POLLING_INTERVAL = 10000;

    let users = [];
    let messages = [];
    let selectedRecipient = null;
    let pollingInterval = null;
    let lastMessageTimestamp = 0;
    let unreadCounts = {};
    let lastNotificationTime = 0;
    let notificationSound = null;
    let conversationsData = {};
    let selectedParticipants = [];

    let mediaRecorder = null;
    let audioChunks = [];
    let isRecording = false;
    let recordingTimer = null;
    let recordingSeconds = 0;
    let recordedAudioBlob = null;

    let localStream = null;
    let videoCallActive = false;
    let isMuted = false;
    let isVideoOff = false;
    let currentCallId = null;
    let pendingCallData = null;
    let callStatusCheckInterval = null;
    let isGroupCall = false;
    
    // ✅ CORRECTION PRINCIPALE: Variables WebRTC simplifiées
    let peerConnection = null;  // Une seule connexion pour appel 1-to-1
    let remoteStream = null;
    let isInitiator = false;
    let webrtcPollingInterval = null;
    let pendingIceCandidates = [];
    let lastProcessedSignalId = 0;
    
    const rtcConfiguration = {
      iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' },
        { urls: 'stun:stun2.l.google.com:19302' }
      ]
    };

    document.addEventListener('DOMContentLoaded', async function() {
      console.log('🚀 Initialisation de la messagerie');

      try {
        notificationSound = document.getElementById('notificationSound');
        
        await loadUsers();
        await loadMessages();
        await loadUnreadCounts();
        initializeApp();
        startPolling();
        
        document.getElementById('loadingScreen').classList.add('hidden');
        document.getElementById('mainApp').classList.remove('hidden');
        
        console.log('✅ Messagerie chargée avec succès');
        
      } catch (error) {
        console.error('❌ Erreur lors du chargement:', error);
        alert('Erreur lors du chargement de la messagerie.');
      }
    });

    async function loadUsers() {
      try {
        const response = await fetch(`${API_BASE_URL}/users`, {
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
          }
        });

        const data = await response.json();
        
        if (data.success) {
          users = data.users || [];
          console.log('✅ Utilisateurs chargés:', users.length);
        }

      } catch (error) {
        console.error('❌ Erreur loadUsers:', error);
        users = [];
      }
    }

    async function loadMessages() {
      try {
        const response = await fetch(`${API_BASE_URL}/messages`, {
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
          }
        });

        const data = await response.json();
        
        if (data.success) {
          const newMessages = data.messages || [];
          
          if (newMessages.length === 0) {
            return;
          }
          
          const latestTimestamp = Math.max(...newMessages.map(m => m.timestamp), 0);
          
          if (latestTimestamp <= lastMessageTimestamp && messages.length > 0) {
            return;
          }
          
          const existingIds = new Set(messages.map(m => m.id));
          const previousCount = messages.length;
          let newMessagesAdded = 0;
          
          newMessages.forEach(msg => {
            if (!existingIds.has(msg.id)) {
              messages.push(msg);
              newMessagesAdded++;
              
              const isForMe = !msg.recipient || msg.recipient === 'all' || 
                             parseInt(msg.recipient) === CURRENT_USER.id;
              
              if (previousCount > 0 && msg.userId !== CURRENT_USER.id && isForMe && msg.type !== 'system') {
                console.log('🔔 Nouveau message détecté:', msg);
                showNewMessageNotification(msg);
              }
            }
          });
          
          if (newMessagesAdded > 0) {
            console.log(`✅ ${newMessagesAdded} nouveau(x) message(s)`);
            lastMessageTimestamp = latestTimestamp;
            updateConversationsData();
            renderConversationsList();
            if (selectedRecipient !== null) {
              renderMessages();
            }
            
            if (previousCount > 0) {
              await loadUnreadCounts();
            }
          }
        }

      } catch (error) {
        console.error('❌ Erreur loadMessages:', error);
      }
    }

    function updateConversationsData() {
      conversationsData = {};
      
      const allUserMessages = messages.filter(m => !m.recipient || m.recipient === 'all');
      if (allUserMessages.length > 0) {
        const lastMsg = allUserMessages[allUserMessages.length - 1];
        conversationsData['all'] = {
          id: 'all',
          name: 'Tous les utilisateurs',
          avatar: '👥',
          lastMessage: lastMsg.type === 'voice' ? '🎤 Message vocal' : lastMsg.text,
          lastMessageTime: lastMsg.timestamp,
          unreadCount: 0,
          isOnline: true,
          hasMessages: true
        };
      } else {
        conversationsData['all'] = {
          id: 'all',
          name: 'Tous les utilisateurs',
          avatar: '👥',
          lastMessage: 'Démarrer une conversation de groupe',
          lastMessageTime: 0,
          unreadCount: 0,
          isOnline: true,
          hasMessages: false
        };
      }

      users.forEach(user => {
        const userMessages = messages.filter(m => {
          if (m.type === 'system') return false;
          const isSentByMe = m.userId === CURRENT_USER.id && m.recipient && parseInt(m.recipient) === user.id;
          const isSentToMe = m.userId === user.id && m.recipient && parseInt(m.recipient) === CURRENT_USER.id;
          return isSentByMe || isSentToMe;
        });

        if (userMessages.length > 0) {
          const lastMsg = userMessages[userMessages.length - 1];
          
          const unread = userMessages.filter(m => {
            return !m.read && m.userId === user.id && m.recipient && parseInt(m.recipient) === CURRENT_USER.id;
          }).length;
          
          conversationsData[user.id] = {
            id: user.id,
            name: user.name,
            role: user.role,
            avatar: user.name.charAt(0).toUpperCase(),
            lastMessage: lastMsg.type === 'voice' ? '🎤 Message vocal' : lastMsg.text,
            lastMessageTime: lastMsg.timestamp,
            lastMessageFrom: lastMsg.userId === CURRENT_USER.id ? 'Vous' : user.name.split(' ')[0],
            unreadCount: unread,
            isOnline: user.active || false,
            hasMessages: true
          };
        } else {
          conversationsData[user.id] = {
            id: user.id,
            name: user.name,
            role: user.role,
            avatar: user.name.charAt(0).toUpperCase(),
            lastMessage: 'Démarrer une conversation',
            lastMessageTime: 0,
            unreadCount: 0,
            isOnline: user.active || false,
            hasMessages: false
          };
        }
      });
    }

    function renderConversationsList() {
      const container = document.getElementById('conversationsList');
      container.innerHTML = '';

      const sortedConversations = Object.values(conversationsData).sort((a, b) => {
        if (a.id === 'all') return -1;
        if (b.id === 'all') return 1;
        if (a.hasMessages && !b.hasMessages) return -1;
        if (!a.hasMessages && b.hasMessages) return 1;
        return b.lastMessageTime - a.lastMessageTime;
      });

      sortedConversations.forEach(conv => {
        const item = document.createElement('div');
        item.className = 'conversation-item';
        
        if (conv.id === 'all') {
          item.classList.add('all-users');
        }
        
        if (!conv.hasMessages) {
          item.classList.add('no-messages');
        }
        
        if (selectedRecipient !== null && (
          (selectedRecipient === 'all' && conv.id === 'all') ||
          (selectedRecipient !== 'all' && conv.id === selectedRecipient)
        )) {
          item.classList.add('active');
        }

        const timeStr = conv.lastMessageTime > 0 ? formatConversationTime(conv.lastMessageTime) : '';
        const preview = conv.lastMessageFrom ? `${conv.lastMessageFrom}: ${conv.lastMessage}` : conv.lastMessage;

        item.innerHTML = `
          <div class="conversation-avatar">
            ${conv.avatar}
            ${conv.isOnline ? '<div class="online-status"></div>' : ''}
          </div>
          <div class="conversation-info">
            <div class="conversation-header">
              <div class="conversation-name">${conv.name}</div>
              ${timeStr ? `<div class="conversation-time">${timeStr}</div>` : ''}
            </div>
            <div class="conversation-preview">
              ${preview.substring(0, 50)}${preview.length > 50 ? '...' : ''}
              ${conv.unreadCount > 0 ? `<span class="unread-badge">${conv.unreadCount}</span>` : ''}
            </div>
          </div>
        `;

        item.onclick = () => selectConversation(conv.id);
        container.appendChild(item);
      });
    }

    function formatConversationTime(timestamp) {
      const date = new Date(timestamp * 1000);
      const now = new Date();
      const diffMs = now - date;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);

      if (diffMins < 1) return 'À l\'instant';
      if (diffMins < 60) return `${diffMins}m`;
      if (diffHours < 24) return `${diffHours}h`;
      if (diffDays === 1) return 'Hier';
      if (diffDays < 7) return `${diffDays}j`;
      
      return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
    }

    function formatMessageDate(timestamp) {
      const date = new Date(timestamp * 1000);
      const now = new Date();
      const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);
      const messageDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());

      if (messageDate.getTime() === today.getTime()) {
        return 'Aujourd\'hui';
      } else if (messageDate.getTime() === yesterday.getTime()) {
        return 'Hier';
      } else if (now - date < 7 * 24 * 60 * 60 * 1000) {
        return date.toLocaleDateString('fr-FR', { weekday: 'long' });
      } else {
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
      }
    }

    function selectConversation(convId) {
      selectedRecipient = convId;
      renderConversationsList();
      updateChatHeader();
      renderMessages();
      markCurrentConversationAsRead();
      document.getElementById('chatInput').focus();
    }

    function updateChatHeader() {
      const avatar = document.getElementById('chatHeaderAvatar');
      const name = document.getElementById('chatHeaderName');
      const status = document.getElementById('chatHeaderStatus');

      if (selectedRecipient === null) {
        avatar.textContent = '👥';
        name.textContent = 'Sélectionnez une conversation';
        status.textContent = '';
        return;
      }

      const conv = conversationsData[selectedRecipient];
      if (conv) {
        avatar.textContent = conv.avatar;
        name.textContent = conv.name;
        status.textContent = conv.role ? conv.role : (conv.isOnline ? '🟢 En ligne' : '');
      }
    }

    function filterConversations() {
      const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
      const items = document.querySelectorAll('.conversation-item');
      
      items.forEach(item => {
        const name = item.querySelector('.conversation-name').textContent.toLowerCase();
        if (name.includes(searchTerm)) {
          item.style.display = 'flex';
        } else {
          item.style.display = 'none';
        }
      });
    }

    function openGroupCallModal() {
      const modal = document.getElementById('groupCallModal');
      const userList = document.getElementById('userSelectionList');
      
      userList.innerHTML = '';
      selectedParticipants = [];
      
      users.forEach(user => {
        const item = document.createElement('div');
        item.className = 'user-selection-item';
        item.innerHTML = `
          <input type="checkbox" id="user-${user.id}" value="${user.id}" onchange="toggleUserSelection(${user.id})">
          <div class="user-selection-avatar">${user.name.charAt(0).toUpperCase()}</div>
          <div class="user-selection-info">
            <div class="user-selection-name">${user.name}</div>
            <div class="user-selection-role">${user.role || 'User'}</div>
          </div>
        `;
        
        item.onclick = (e) => {
          if (e.target.type !== 'checkbox') {
            const checkbox = item.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            toggleUserSelection(user.id);
          }
        };
        
        userList.appendChild(item);
      });
      
      updateSelectedCount();
      modal.classList.add('active');
    }

    function closeGroupCallModal() {
      document.getElementById('groupCallModal').classList.remove('active');
      selectedParticipants = [];
    }

    function toggleUserSelection(userId) {
      const checkbox = document.getElementById(`user-${userId}`);
      const item = checkbox.closest('.user-selection-item');
      
      if (checkbox.checked) {
        if (!selectedParticipants.includes(userId)) {
          selectedParticipants.push(userId);
        }
        item.classList.add('selected');
      } else {
        selectedParticipants = selectedParticipants.filter(id => id !== userId);
        item.classList.remove('selected');
      }
      
      updateSelectedCount();
    }

    function updateSelectedCount() {
      const countText = document.getElementById('selectedCountText');
      const startBtn = document.getElementById('startGroupCallBtn');
      
      countText.textContent = `${selectedParticipants.length} participant(s) sélectionné(s)`;
      startBtn.disabled = selectedParticipants.length === 0;
    }

    async function startGroupVideoCall() {
      if (selectedParticipants.length === 0) {
        alert('⚠️ Sélectionnez des participants');
        return;
      }

      closeGroupCallModal();

      try {
        const response = await fetch(`${API_BASE_URL}/start-group-video-call`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            participant_ids: selectedParticipants
          })
        });

        const data = await response.json();
        
        if (data.success) {
          currentCallId = data.call_id;
          isInitiator = true;
          isGroupCall = true;
          
          alert(`📞 Invitations envoyées à ${selectedParticipants.length} participant(s) !`);
          
          await startVideoCallStream();
        } else {
          alert('❌ Erreur appel de groupe');
        }
        
      } catch (error) {
        console.error('❌ Erreur groupe:', error);
        alert('Erreur appel groupe');
      }
    }

    function startPolling() {
      pollingInterval = setInterval(async () => {
        await loadMessages();
        await checkVideoCallInvitations();
      }, POLLING_INTERVAL);
      
      setInterval(async () => {
        await loadUnreadCounts();
      }, 30000);
      
      console.log('✅ Polling démarré');
    }

    function stopPolling() {
      if (pollingInterval) {
        clearInterval(pollingInterval);
      }
    }

    window.addEventListener('beforeunload', stopPolling);

    function initializeApp() {
      document.getElementById('sidebarUserName').textContent = CURRENT_USER.name;
      document.getElementById('sidebarUserRole').textContent = CURRENT_USER.role;
      
      updateConversationsData();
      renderConversationsList();
      updateChatHeader();
    }

    async function loadUnreadCounts() {
      try {
        const response = await fetch(`${API_BASE_URL}/unread-count`, {
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
          }
        });

        const data = await response.json();
        
        if (data.success) {
          unreadCounts = data.unread_by_user || {};
          const totalUnread = data.total_unread || 0;
          
          updateTotalUnreadBadge(totalUnread);
          updateConversationsData();
          renderConversationsList();
        }

      } catch (error) {
        console.error('❌ Erreur loadUnreadCounts:', error);
      }
    }

    function updateTotalUnreadBadge(count) {
      if (count > 0) {
        document.title = `(${count}) PSI AFRICA - Messagerie`;
      } else {
        document.title = 'PSI AFRICA - Messagerie';
      }
    }

    function showNewMessageNotification(message) {
      const now = Date.now();
      
      if (now - lastNotificationTime < 2000) {
        return;
      }
      
      lastNotificationTime = now;
      
      playNotificationSound();
      
      const indicator = document.getElementById('newMessageIndicator');
      const fromSpan = document.getElementById('newMessageFrom');
      const textSpan = document.getElementById('newMessageText');
      
      fromSpan.textContent = `Nouveau message de ${message.from}`;
      
      if (message.type === 'voice') {
        textSpan.textContent = '🎤 Message vocal';
      } else if (message.text) {
        textSpan.textContent = message.text.substring(0, 50) + (message.text.length > 50 ? '...' : '');
      } else {
        textSpan.textContent = 'Nouveau message';
      }
      
      indicator.classList.add('show');
      
      setTimeout(() => {
        indicator.classList.remove('show');
      }, 4000);
      
      if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(`Nouveau message de ${message.from}`, {
          body: message.text || '🎤 Message vocal',
          icon: '/favicon.ico',
          tag: 'messagerie-notification'
        });
      }
    }

    function playNotificationSound() {
      try {
        if (notificationSound) {
          notificationSound.currentTime = 0;
          notificationSound.volume = 0.5;
          notificationSound.play().catch(error => {
            console.log('Son désactivé');
          });
        }
      } catch (error) {
        console.error('Erreur son:', error);
      }
    }

    async function markCurrentConversationAsRead() {
      if (selectedRecipient === null || selectedRecipient === 'all') {
        return;
      }

      try {
        const unreadMessageIds = messages
          .filter(m => {
            if (m.read || m.type === 'system') return false;
            if (m.userId === CURRENT_USER.id) return false;
            
            const otherUserId = parseInt(selectedRecipient);
            return (
              m.userId === otherUserId && 
              m.recipient && 
              parseInt(m.recipient) === CURRENT_USER.id
            );
          })
          .map(m => m.id);
        
        if (unreadMessageIds.length === 0) {
          return;
        }
        
        const response = await fetch(`${API_BASE_URL}/mark-as-read`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            message_ids: unreadMessageIds
          })
        });

        const data = await response.json();
        
        if (data.success) {
          unreadMessageIds.forEach(id => {
            const msg = messages.find(m => m.id === id);
            if (msg) {
              msg.read = true;
              msg.read_at = Date.now() / 1000;
            }
          });
          
          await loadUnreadCounts();
        }

      } catch (error) {
        console.error('❌ Erreur markAsRead:', error);
      }
    }

    function requestNotificationPermission() {
      if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
      }
    }

    setTimeout(requestNotificationPermission, 2000);

    async function sendMessage() {
      const input = document.getElementById('chatInput');
      const text = input.value.trim();
      const sendButton = document.getElementById('sendButton');
      
      if (!text || selectedRecipient === null) return;
      
      try {
        sendButton.disabled = true;
        sendButton.textContent = '⏳';
        
        const response = await fetch(`${API_BASE_URL}/messages`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            text: text,
            recipient: selectedRecipient,
            type: 'text'
          })
        });

        const data = await response.json();
        
        if (data.success) {
          input.value = '';
          messages.push(data.data);
          updateConversationsData();
          renderConversationsList();
          renderMessages();
          setTimeout(() => loadMessages(), 500);
        } else {
          alert('Erreur lors de l\'envoi');
        }
        
      } catch (error) {
        console.error('❌ Erreur sendMessage:', error);
        alert('Erreur lors de l\'envoi');
      } finally {
        sendButton.disabled = false;
        sendButton.textContent = '📤';
      }
    }

    function handleKeyPress(event) {
      if (event.key === 'Enter') {
        sendMessage();
      }
    }

    async function toggleRecording() {
      if (isRecording) {
        stopRecording();
      } else {
        startRecording();
      }
    }

    async function startRecording() {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
          audio: {
            echoCancellation: true,
            noiseSuppression: true
          } 
        });

        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];
        
        mediaRecorder.ondataavailable = (event) => {
          audioChunks.push(event.data);
        };
        
        mediaRecorder.onstop = () => {
          recordedAudioBlob = new Blob(audioChunks, { type: 'audio/wav' });
          showAudioPreview(recordedAudioBlob);
          stream.getTracks().forEach(track => track.stop());
        };
        
        mediaRecorder.start();
        isRecording = true;
        
        document.getElementById('recordButton').classList.add('recording');
        document.getElementById('recordButton').textContent = '⏹️ Arrêter';
        document.getElementById('recordingIndicator').classList.remove('hidden');
        
        recordingSeconds = 0;
        updateRecordingTimer();
        recordingTimer = setInterval(updateRecordingTimer, 1000);
        
      } catch (error) {
        console.error('❌ Erreur micro:', error);
        alert('Impossible d\'accéder au microphone.');
      }
    }

    function stopRecording() {
      if (mediaRecorder && isRecording) {
        mediaRecorder.stop();
        isRecording = false;
        
        document.getElementById('recordButton').classList.remove('recording');
        document.getElementById('recordButton').textContent = '🎤 Vocal';
        document.getElementById('recordingIndicator').classList.add('hidden');
        
        clearInterval(recordingTimer);
      }
    }

    function updateRecordingTimer() {
      recordingSeconds++;
      
      if (recordingSeconds >= 300) {
        stopRecording();
        alert('⏱️ Durée max atteinte');
      }
      
      const minutes = Math.floor(recordingSeconds / 60);
      const seconds = recordingSeconds % 60;
      document.getElementById('recordingTimer').textContent = 
        `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    }

    function showAudioPreview(audioBlob) {
      const audioURL = URL.createObjectURL(audioBlob);
      const previewAudio = document.getElementById('previewAudio');
      
      previewAudio.src = audioURL;
      
      const minutes = Math.floor(recordingSeconds / 60);
      const seconds = recordingSeconds % 60;
      document.getElementById('previewDuration').textContent = 
        `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
      
      document.getElementById('audioPreviewModal').classList.add('active');
    }

    function cancelAudioPreview() {
      document.getElementById('audioPreviewModal').classList.remove('active');
      recordedAudioBlob = null;
    }

    async function confirmSendAudio() {
      if (!recordedAudioBlob || selectedRecipient === null) return;
      
      document.getElementById('audioPreviewModal').classList.remove('active');
      
      try {
        const reader = new FileReader();
        
        reader.onload = async function() {
          const audioDataUrl = reader.result;
          
          const response = await fetch(`${API_BASE_URL}/messages`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              audio: audioDataUrl,
              audio_duration: recordingSeconds,
              recipient: selectedRecipient,
              type: 'voice'
            })
          });

          const data = await response.json();
          
          if (data.success) {
            messages.push(data.data);
            updateConversationsData();
            renderConversationsList();
            renderMessages();
            setTimeout(() => loadMessages(), 500);
          } else {
            alert('Erreur envoi vocal');
          }
        };
        
        reader.readAsDataURL(recordedAudioBlob);
        
      } catch (error) {
        console.error('❌ Erreur vocal:', error);
        alert('Erreur envoi vocal');
      }
      
      recordedAudioBlob = null;
    }

    // ✅ CORRECTION CRITIQUE: Logique appel vidéo simplifiée
    async function initiateVideoCall() {
      if (selectedRecipient === null || selectedRecipient === 'all') {
        alert('⚠️ Sélectionnez un utilisateur pour l\'appel');
        return;
      }

      try {
        const response = await fetch(`${API_BASE_URL}/start-video-call`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            recipient_id: selectedRecipient
          })
        });

        const data = await response.json();
        
        if (data.success) {
          currentCallId = data.call_id;
          isInitiator = true;
          isGroupCall = false;
          
          console.log('✅ Appel initié, call_id:', currentCallId);
          alert('📞 Invitation envoyée ! En attente de réponse...');
          
          startCallStatusCheck();
        } else {
          alert('❌ Erreur: ' + (data.error || 'Impossible d\'initier l\'appel'));
        }
        
      } catch (error) {
        console.error('❌ Erreur vidéo:', error);
        alert('Erreur appel');
      }
    }

    function startCallStatusCheck() {
      if (callStatusCheckInterval) {
        clearInterval(callStatusCheckInterval);
      }

      callStatusCheckInterval = setInterval(async () => {
        if (!currentCallId) {
          clearInterval(callStatusCheckInterval);
          return;
        }

        try {
          const response = await fetch(`${API_BASE_URL}/check-call-status?call_id=${currentCallId}`, {
            headers: {
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Accept': 'application/json'
            }
          });

          const data = await response.json();
          
          if (data.success) {
            if (data.status === 'accepted') {
              console.log('✅ Appel accepté !');
              clearInterval(callStatusCheckInterval);
              await startVideoCallStream();
            } else if (data.status === 'expired') {
              clearInterval(callStatusCheckInterval);
              currentCallId = null;
              alert('⏰ Appel expiré');
            }
          }
        } catch (error) {
          console.error('❌ Erreur status:', error);
        }
      }, 2000);
    }

    async function checkVideoCallInvitations() {
      try {
        const response = await fetch(`${API_BASE_URL}/check-video-call`, {
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
          }
        });

        const data = await response.json();
        
        if (data.success && data.has_invitation) {
          showVideoInvitation(data.call_data);
        }

      } catch (error) {
        console.error('❌ Erreur check invitation:', error);
      }
    }

    function showVideoInvitation(callData) {
      pendingCallData = callData;
      
      if (callData.type === 'group') {
        isGroupCall = true;
        document.getElementById('callerName').textContent = callData.initiator_name;
        document.getElementById('invitationText').textContent = 
          `vous invite à un appel de groupe (${callData.participant_ids ? callData.participant_ids.length + 1 : 2} participants)`;
      } else {
        isGroupCall = false;
        document.getElementById('callerName').textContent = callData.caller_name;
        document.getElementById('invitationText').textContent = 'vous invite à un appel vidéo';
      }
      
      document.getElementById('videoInvitationModal').classList.add('active');
      playVideoCallRingtone();
    }

    function playVideoCallRingtone() {
      try {
        const ringtone = document.getElementById('videoCallRingtone');
        if (ringtone) {
          ringtone.currentTime = 0;
          ringtone.volume = 0.6;
          ringtone.loop = true;
          ringtone.play().catch(error => {
            console.log('Sonnerie désactivée');
          });
        }
      } catch (error) {
        console.error('Erreur sonnerie:', error);
      }
    }

    function stopVideoCallRingtone() {
      try {
        const ringtone = document.getElementById('videoCallRingtone');
        if (ringtone) {
          ringtone.pause();
          ringtone.currentTime = 0;
        }
      } catch (error) {
        console.error('Erreur arrêt sonnerie:', error);
      }
    }

    async function acceptVideoCall() {
      if (!pendingCallData) return;

      stopVideoCallRingtone();

      try {
        const endpoint = isGroupCall ? 'accept-group-video-call' : 'accept-video-call';
        
        const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            call_id: pendingCallData.call_id
          })
        });

        const data = await response.json();
        
        if (data.success) {
          document.getElementById('videoInvitationModal').classList.remove('active');
          currentCallId = pendingCallData.call_id;
          isInitiator = false;
          
          console.log('✅ Appel accepté, call_id:', currentCallId);
          
          await startVideoCallStream();
        } else {
          alert('❌ Erreur acceptation');
        }
        
      } catch (error) {
        console.error('❌ Erreur accept:', error);
        alert('Erreur acceptation');
      }
    }

    async function rejectVideoCall() {
      if (!pendingCallData) return;

      stopVideoCallRingtone();

      try {
        const response = await fetch(`${API_BASE_URL}/reject-video-call`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            call_id: pendingCallData.call_id
          })
        });

        const data = await response.json();
        
        if (data.success) {
          document.getElementById('videoInvitationModal').classList.remove('active');
          pendingCallData = null;
          isGroupCall = false;
        }
        
      } catch (error) {
        console.error('❌ Erreur reject:', error);
      }
    }

    // ✅ FONCTION PRINCIPALE CORRIGÉE
    async function startVideoCallStream() {
      try {
        console.log('🎥 Démarrage du stream vidéo...');
        console.log('📍 État: isInitiator=' + isInitiator + ', callId=' + currentCallId);
        
        localStream = await navigator.mediaDevices.getUserMedia({ 
          video: { 
            width: { ideal: 1280 },
            height: { ideal: 720 }
          }, 
          audio: {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true
          }
        });
        
        console.log('✅ Stream local obtenu');
        
        document.getElementById('videoCallContainer').classList.add('active');
        videoCallActive = true;
        
        addVideoStream('local', CURRENT_USER.name + ' (Vous)', localStream, true);
        
        // ✅ CORRECTION: Démarrer le polling AVANT d'initialiser WebRTC
        startWebRTCPolling();
        
        // ✅ Attendre un peu avant d'initialiser WebRTC
        setTimeout(async () => {
          await initializeWebRTC();
        }, 1000);
        
      } catch (error) {
        console.error('❌ Erreur stream:', error);
        alert('Impossible d\'accéder à la caméra/microphone.');
      }
    }

    // ✅ FONCTION WEBRTC COMPLÈTEMENT RÉÉCRITE
    async function initializeWebRTC() {
      try {
        console.log('🔗 Initialisation WebRTC...');
        console.log('📍 isInitiator:', isInitiator);
        console.log('📍 isGroupCall:', isGroupCall);
        console.log('📍 callId:', currentCallId);
        
        if (isGroupCall) {
          alert('❌ Appel de groupe non supporté pour le moment');
          endVideoCall();
          return;
        }
        
        // Créer la PeerConnection
        await createPeerConnection();
        
        // Si on est l'initiateur, créer et envoyer l'offre
        if (isInitiator) {
          console.log('📤 Création de l\'offre SDP...');
          await createAndSendOffer();
        } else {
          // ✅ NOUVEAU: Le destinataire attend l'offre activement
          console.log('⏳ En attente de l\'offre...');
        }
        
        console.log('✅ WebRTC initialisé');
        
      } catch (error) {
        console.error('❌ Erreur WebRTC init:', error);
        alert('Erreur lors de l\'initialisation de l\'appel vidéo');
      }
    }

    async function createPeerConnection() {
      try {
        console.log('🔧 Création PeerConnection...');
        
        peerConnection = new RTCPeerConnection(rtcConfiguration);
        pendingIceCandidates = [];
        
        console.log('✅ PeerConnection créée, état:', peerConnection.signalingState);
        
        // Ajouter les tracks locaux
        if (localStream) {
          let audioAdded = false;
          let videoAdded = false;
          
          localStream.getTracks().forEach(track => {
            console.log('➕ Ajout track:', track.kind, 'enabled:', track.enabled);
            peerConnection.addTrack(track, localStream);
            
            if (track.kind === 'audio') audioAdded = true;
            if (track.kind === 'video') videoAdded = true;
          });
          
          console.log('✅ Tracks ajoutés - Audio:', audioAdded, 'Video:', videoAdded);
        } else {
          console.warn('⚠️ Pas de localStream disponible');
        }
        
        // Gérer les tracks distants
        peerConnection.ontrack = (event) => {
          console.log('📥 Track distant reçu:', event.track.kind);
          console.log('📥 Streams:', event.streams.length);
          
          if (!remoteStream) {
            console.log('🎬 Création du stream distant');
            remoteStream = new MediaStream();
            const otherUserName = isInitiator ? getRecipientName(selectedRecipient) : pendingCallData.caller_name;
            addVideoStream('remote', otherUserName, remoteStream, false);
          }
          
          // ✅ CORRECTION: Ajouter le track directement depuis event.track
          if (!remoteStream.getTracks().find(t => t.id === event.track.id)) {
            remoteStream.addTrack(event.track);
            console.log('✅ Track ajouté au stream distant:', event.track.kind);
          }
          
          // ✅ NOUVEAU: Vérifier l'état du stream
          console.log('📊 Remote stream tracks:', remoteStream.getTracks().map(t => t.kind).join(', '));
        };
        
        // Gérer les ICE candidates
        peerConnection.onicecandidate = async (event) => {
          if (event.candidate) {
            console.log('🧊 ICE candidate local généré');
            console.log('📍 Type:', event.candidate.type, 'Protocol:', event.candidate.protocol);
            
            try {
              await sendWebRTCSignal('ice-candidate', {
                candidate: event.candidate.candidate,
                sdpMLineIndex: event.candidate.sdpMLineIndex,
                sdpMid: event.candidate.sdpMid
              });
              console.log('✅ ICE candidate envoyé');
            } catch (error) {
              console.error('❌ Erreur envoi ICE:', error);
            }
          } else {
            console.log('🏁 ICE gathering terminé');
          }
        };
        
        // Gérer l'état de la connexion
        peerConnection.onconnectionstatechange = () => {
          console.log('🔌 Connection state:', peerConnection.connectionState);
          
          if (peerConnection.connectionState === 'connected') {
            console.log('✅✅✅ CONNEXION WEBRTC ÉTABLIE ! ✅✅✅');
            // ✅ Afficher une notification à l'utilisateur
            showConnectionSuccess();
          } else if (peerConnection.connectionState === 'disconnected') {
            console.log('⚠️ Connexion WebRTC déconnectée');
          } else if (peerConnection.connectionState === 'failed') {
            console.log('❌ Connexion WebRTC échouée');
            alert('❌ La connexion vidéo a échoué. Veuillez réessayer.');
          }
        };
        
        peerConnection.oniceconnectionstatechange = () => {
          console.log('🧊 ICE connection state:', peerConnection.iceConnectionState);
          
          if (peerConnection.iceConnectionState === 'connected' || 
              peerConnection.iceConnectionState === 'completed') {
            console.log('✅ ICE connecté !');
          } else if (peerConnection.iceConnectionState === 'failed') {
            console.log('❌ ICE échoué');
          }
        };
        
        peerConnection.onsignalingstatechange = () => {
          console.log('📡 Signaling state:', peerConnection.signalingState);
        };
        
        console.log('✅ PeerConnection configurée complètement');
        
        return peerConnection;
        
      } catch (error) {
        console.error('❌ Erreur création peer:', error);
        throw error;
      }
    }
    
    // ✅ NOUVEAU: Afficher un message de succès
    function showConnectionSuccess() {
      const indicator = document.getElementById('newMessageIndicator');
      const fromSpan = document.getElementById('newMessageFrom');
      const textSpan = document.getElementById('newMessageText');
      
      fromSpan.textContent = '✅ Connexion établie !';
      textSpan.textContent = 'L\'appel vidéo est maintenant actif';
      
      indicator.style.background = 'var(--success-color)';
      indicator.classList.add('show');
      
      setTimeout(() => {
        indicator.classList.remove('show');
      }, 3000);
    }

    async function createAndSendOffer() {
      try {
        if (!peerConnection) {
          console.error('❌ Pas de peerConnection');
          return;
        }
        
        console.log('📝 Création offre SDP...');
        
        const offer = await peerConnection.createOffer({
          offerToReceiveAudio: true,
          offerToReceiveVideo: true
        });
        
        console.log('📝 Offre créée, setting local description...');
        await peerConnection.setLocalDescription(offer);
        
        console.log('📤 Envoi offre au serveur...');
        await sendWebRTCSignal('offer', {
          type: offer.type,
          sdp: offer.sdp
        });
        
        console.log('✅ Offre envoyée');
        
      } catch (error) {
        console.error('❌ Erreur création offre:', error);
        throw error;
      }
    }

    // ✅ ENVOI DE SIGNAL WEBRTC SIMPLIFIÉ
    async function sendWebRTCSignal(type, data) {
      try {
        console.log('📤 Envoi signal:', type);
        
        const response = await fetch(`${API_BASE_URL}/exchange-webrtc`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            call_id: currentCallId,
            type: type,
            data: data
          })
        });

        const result = await response.json();
        
        if (!result.success) {
          console.error('❌ Erreur serveur:', result);
        } else {
          console.log('✅ Signal envoyé:', type);
        }
        
      } catch (error) {
        console.error('❌ Erreur envoi signal:', error);
        throw error;
      }
    }

    // ✅ POLLING WEBRTC CORRIGÉ - VERSION ROBUSTE
    function startWebRTCPolling() {
      if (webrtcPollingInterval) {
        console.log('⚠️ Polling déjà actif');
        return;
      }

      console.log('🔄 Démarrage polling WebRTC...');
      lastProcessedSignalId = 0;

      webrtcPollingInterval = setInterval(async () => {
        if (!currentCallId) {
          console.log('⚠️ Pas de callId, arrêt polling');
          clearInterval(webrtcPollingInterval);
          webrtcPollingInterval = null;
          return;
        }

        try {
          const response = await fetch(`${API_BASE_URL}/get-webrtc-data?call_id=${currentCallId}`, {
            headers: {
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Accept': 'application/json'
            }
          });

          const result = await response.json();
          
          if (result.success && result.data && result.data.length > 0) {
            console.log(`📥 ${result.data.length} signaux reçus du serveur`);
            
            // ✅ NOUVEAU: Trier les signaux par timestamp
            const sortedSignals = result.data.sort((a, b) => a.timestamp - b.timestamp);
            
            for (const signal of sortedSignals) {
              // ✅ Ignorer nos propres signaux
              if (signal.user_id === CURRENT_USER.id) {
                console.log('⏭️ Ignorer mon propre signal:', signal.type);
                continue;
              }
              
              // ✅ Éviter de retraiter les mêmes signaux
              if (signal.timestamp <= lastProcessedSignalId) {
                continue;
              }
              
              console.log('📥 Traitement signal:', signal.type, 'timestamp:', signal.timestamp);
              
              try {
                if (signal.type === 'offer') {
                  await handleReceivedOffer(signal.data);
                } else if (signal.type === 'answer') {
                  await handleReceivedAnswer(signal.data);
                } else if (signal.type === 'ice-candidate') {
                  await handleReceivedIceCandidate(signal.data);
                }
                
                lastProcessedSignalId = signal.timestamp;
                console.log('✅ Signal traité, lastProcessedSignalId:', lastProcessedSignalId);
                
              } catch (error) {
                console.error('❌ Erreur traitement signal:', signal.type, error);
              }
            }
          }
        } catch (error) {
          console.error('❌ Erreur polling WebRTC:', error);
        }
      }, 500); // ✅ CORRECTION: Polling plus fréquent (500ms au lieu de 1000ms)
    }

    // ✅ GESTION OFFRE REÇUE - VERSION AMÉLIORÉE
    async function handleReceivedOffer(offerData) {
      try {
        console.log('📥 Traitement offre reçue...');
        console.log('📍 État PeerConnection:', peerConnection ? 'existe' : 'null');
        
        if (!peerConnection) {
          console.log('⚙️ Création PeerConnection pour recevoir l\'offre...');
          await createPeerConnection();
        }
        
        // ✅ NOUVEAU: Vérifier l'état actuel
        if (peerConnection.signalingState !== 'stable' && peerConnection.signalingState !== 'have-local-offer') {
          console.log('⚠️ État signaling incorrect:', peerConnection.signalingState);
          console.log('🔄 Tentative de récupération...');
        }
        
        const offer = new RTCSessionDescription({
          type: offerData.type,
          sdp: offerData.sdp
        });
        
        console.log('📝 Setting remote description (offer)...');
        await peerConnection.setRemoteDescription(offer);
        console.log('✅ Remote description définie');
        
        console.log('📝 Création answer...');
        const answer = await peerConnection.createAnswer({
          offerToReceiveAudio: true,
          offerToReceiveVideo: true
        });
        
        console.log('📝 Setting local description (answer)...');
        await peerConnection.setLocalDescription(answer);
        console.log('✅ Local description définie');
        
        console.log('📤 Envoi answer au serveur...');
        await sendWebRTCSignal('answer', {
          type: answer.type,
          sdp: answer.sdp
        });
        console.log('✅ Answer envoyée');
        
        // ✅ Ajouter les ICE candidates en attente
        await addPendingIceCandidates();
        
        console.log('✅ Offre traitée complètement');
        
      } catch (error) {
        console.error('❌ Erreur traitement offre:', error);
        console.error('📍 État PeerConnection:', peerConnection ? peerConnection.signalingState : 'null');
      }
    }

    // ✅ GESTION ANSWER REÇUE - VERSION AMÉLIORÉE
    async function handleReceivedAnswer(answerData) {
      try {
        console.log('📥 Traitement answer reçue...');
        console.log('📍 État PeerConnection:', peerConnection ? peerConnection.signalingState : 'null');
        
        if (!peerConnection) {
          console.error('❌ Pas de PeerConnection pour answer');
          return;
        }
        
        // ✅ NOUVEAU: Vérifier qu'on est dans le bon état
        if (peerConnection.signalingState !== 'have-local-offer') {
          console.warn('⚠️ État signaling incorrect pour answer:', peerConnection.signalingState);
        }
        
        const answer = new RTCSessionDescription({
          type: answerData.type,
          sdp: answerData.sdp
        });
        
        console.log('📝 Setting remote description (answer)...');
        await peerConnection.setRemoteDescription(answer);
        console.log('✅ Remote description définie (answer)');
        
        // ✅ Ajouter les ICE candidates en attente
        await addPendingIceCandidates();
        
        console.log('✅ Answer traitée complètement');
        console.log('📍 État final:', peerConnection.signalingState);
        console.log('📍 ICE state:', peerConnection.iceConnectionState);
        
      } catch (error) {
        console.error('❌ Erreur traitement answer:', error);
        console.error('📍 État PeerConnection:', peerConnection ? peerConnection.signalingState : 'null');
      }
    }

    // ✅ GESTION ICE CANDIDATE REÇU - VERSION AMÉLIORÉE
    async function handleReceivedIceCandidate(candidateData) {
      try {
        console.log('🧊 Réception ICE candidate');
        
        if (!peerConnection) {
          console.warn('⚠️ Pas de PeerConnection pour ICE, mise en attente');
          pendingIceCandidates.push(candidateData);
          return;
        }
        
        const candidate = new RTCIceCandidate({
          candidate: candidateData.candidate,
          sdpMLineIndex: candidateData.sdpMLineIndex,
          sdpMid: candidateData.sdpMid
        });
        
        // ✅ Si on a déjà une remote description, ajouter directement
        if (peerConnection.remoteDescription) {
          console.log('🧊 Ajout ICE candidate direct');
          await peerConnection.addIceCandidate(candidate);
          console.log('✅ ICE candidate ajouté');
        } else {
          // ✅ Sinon, mettre en attente
          console.log('⏳ ICE candidate en attente (pas de remoteDescription)');
          pendingIceCandidates.push(candidateData);
        }
        
      } catch (error) {
        console.error('❌ Erreur ICE candidate:', error);
        // Ne pas bloquer sur une erreur ICE
      }
    }

    // ✅ AJOUTER LES ICE CANDIDATES EN ATTENTE - VERSION AMÉLIORÉE
    async function addPendingIceCandidates() {
      if (pendingIceCandidates.length > 0 && peerConnection && peerConnection.remoteDescription) {
        console.log(`🧊 Traitement de ${pendingIceCandidates.length} ICE candidates en attente`);
        
        for (const candidateData of pendingIceCandidates) {
          try {
            const candidate = new RTCIceCandidate({
              candidate: candidateData.candidate,
              sdpMLineIndex: candidateData.sdpMLineIndex,
              sdpMid: candidateData.sdpMid
            });
            
            await peerConnection.addIceCandidate(candidate);
            console.log('✅ ICE candidate en attente ajouté');
          } catch (error) {
            console.error('❌ Erreur ajout ICE en attente:', error);
            // Continuer avec les autres candidats
          }
        }
        
        pendingIceCandidates = [];
        console.log('✅ Tous les ICE candidates en attente traités');
      }
    }

    function addVideoStream(id, userName, stream, isLocal) {
      const videoGrid = document.getElementById('videoGrid');
      
      const existingParticipant = document.getElementById(`participant-${id}`);
      if (existingParticipant) {
        const video = existingParticipant.querySelector('video');
        if (video && stream) {
          video.srcObject = stream;
        }
        return;
      }
      
      const participantDiv = document.createElement('div');
      participantDiv.className = 'video-participant';
      participantDiv.id = `participant-${id}`;
      
      const video = document.createElement('video');
      video.autoplay = true;
      video.playsinline = true;
      video.muted = isLocal;
      
      if (stream) {
        video.srcObject = stream;
        
        video.onloadedmetadata = () => {
          video.play().catch(error => {
            console.error('❌ Erreur play video:', error);
          });
        };
      }
      
      const infoDiv = document.createElement('div');
      infoDiv.className = 'participant-info';
      infoDiv.textContent = userName;
      
      participantDiv.appendChild(video);
      participantDiv.appendChild(infoDiv);
      
      videoGrid.appendChild(participantDiv);
      
      console.log('✅ Vidéo ajoutée:', id);
    }

    function toggleMicrophone() {
      if (localStream) {
        const audioTracks = localStream.getAudioTracks();
        audioTracks.forEach(track => {
          track.enabled = !track.enabled;
        });
        isMuted = !isMuted;
        
        const micButton = document.getElementById('toggleMic');
        micButton.textContent = isMuted ? '🎤❌' : '🎤';
        micButton.style.background = isMuted ? 'var(--danger-color)' : 'var(--video-color)';
      }
    }

    function toggleCamera() {
      if (localStream) {
        const videoTracks = localStream.getVideoTracks();
        videoTracks.forEach(track => {
          track.enabled = !track.enabled;
        });
        isVideoOff = !isVideoOff;
        
        const videoButton = document.getElementById('toggleVideo');
        videoButton.textContent = isVideoOff ? '📹❌' : '📹';
        videoButton.style.background = isVideoOff ? 'var(--danger-color)' : 'var(--video-color)';
      }
    }

    function endVideoCall() {
      console.log('📞 Fin de l\'appel');
      
      if (isGroupCall && currentCallId) {
        fetch(`${API_BASE_URL}/leave-group-video-call`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            call_id: currentCallId
          })
        }).catch(error => console.error('Erreur leave:', error));
      }
      
      // Fermer les streams
      if (localStream) {
        localStream.getTracks().forEach(track => track.stop());
        localStream = null;
      }
      
      if (remoteStream) {
        remoteStream.getTracks().forEach(track => track.stop());
        remoteStream = null;
      }
      
      // Fermer la peer connection
      if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
      }
      
      // Arrêter les intervals
      if (callStatusCheckInterval) {
        clearInterval(callStatusCheckInterval);
        callStatusCheckInterval = null;
      }
      
      if (webrtcPollingInterval) {
        clearInterval(webrtcPollingInterval);
        webrtcPollingInterval = null;
      }
      
      // Réinitialiser les variables
      pendingIceCandidates = [];
      lastProcessedSignalId = 0;
      isInitiator = false;
      isGroupCall = false;
      
      // Masquer l'interface vidéo
      document.getElementById('videoCallContainer').classList.remove('active');
      videoCallActive = false;
      isMuted = false;
      isVideoOff = false;
      currentCallId = null;
      
      const videoGrid = document.getElementById('videoGrid');
      videoGrid.innerHTML = '';
      
      document.getElementById('toggleMic').textContent = '🎤';
      document.getElementById('toggleMic').style.background = 'var(--video-color)';
      document.getElementById('toggleVideo').textContent = '📹';
      document.getElementById('toggleVideo').style.background = 'var(--video-color)';
      
      console.log('✅ Appel terminé');
    }

    function renderMessages() {
      const chatBox = document.getElementById('chatMessages');
      const wasAtBottom = chatBox.scrollTop >= chatBox.scrollHeight - chatBox.clientHeight - 50;
      
      chatBox.innerHTML = '';
      
      if (selectedRecipient === null) {
        chatBox.innerHTML = `
          <div class="empty-state">
            <div class="empty-state-icon">💬</div>
            <h3>Bienvenue dans la messagerie PSI AFRICA</h3>
            <p>Sélectionnez une conversation pour commencer</p>
          </div>
        `;
        return;
      }

      messages.sort((a, b) => a.timestamp - b.timestamp);
      
      const filteredMessages = messages.filter(m => shouldDisplayMessage(m));
      
      if (filteredMessages.length === 0) {
        const conv = conversationsData[selectedRecipient];
        const userName = conv ? conv.name : 'cet utilisateur';
        chatBox.innerHTML = `
          <div class="empty-state">
            <div class="empty-state-icon">💬</div>
            <h3>Aucun message avec ${userName}</h3>
            <p>Commencez la conversation !</p>
          </div>
        `;
        return;
      }

      let lastDate = null;
      
      filteredMessages.forEach(m => {
        const msgDate = formatMessageDate(m.timestamp);
        
        if (msgDate !== lastDate) {
          const dateDivider = document.createElement('div');
          dateDivider.className = 'message-date-divider';
          dateDivider.innerHTML = `<span>${msgDate}</span>`;
          chatBox.appendChild(dateDivider);
          lastDate = msgDate;
        }

        const messageDiv = document.createElement('div');
        const isOwnMessage = m.userId === CURRENT_USER.id;
        const isSystemMessage = m.type === 'system';
        const isVoiceMessage = m.type === 'voice';
        
        if (isSystemMessage) {
          messageDiv.className = 'message system';
          messageDiv.innerHTML = `
            <div>${m.text}</div>
            <div class="message-time">${m.time}</div>
          `;
        } else {
          let messageClass = `message ${isOwnMessage ? 'own' : 'other'}`;
          if (isVoiceMessage) messageClass += ' voice';
          
          messageDiv.className = messageClass;
          
          let messageContent = '';
          if (isVoiceMessage && m.audio) {
            const minutes = Math.floor(m.audio_duration / 60);
            const seconds = m.audio_duration % 60;
            const durationText = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            
            messageContent = `
              <div class="voice-message-player">
                <audio controls>
                  <source src="${m.audio}" type="audio/wav">
                </audio>
                <span class="voice-duration">${durationText}</span>
              </div>
            `;
          } else if (m.text) {
            messageContent = `<div class="message-content">${m.text}</div>`;
          }
          
          messageDiv.innerHTML = `
            ${!isOwnMessage ? `<div class="message-header">${m.from}</div>` : ''}
            ${messageContent}
            <div class="message-time">${m.time}</div>
          `;
        }
        
        chatBox.appendChild(messageDiv);
      });
      
      if (wasAtBottom || filteredMessages.length <= 1) {
        chatBox.scrollTop = chatBox.scrollHeight;
      }
    }

    function shouldDisplayMessage(message) {
      if (selectedRecipient === null) {
        return false;
      }

      if (message.type === 'system') {
        return selectedRecipient === 'all';
      }
      
      if (selectedRecipient === 'all') {
        return !message.recipient || message.recipient === 'all';
      }
      
      const otherUserId = parseInt(selectedRecipient);
      
      const isSentByMeToThem = (
        message.userId === CURRENT_USER.id && 
        message.recipient && 
        parseInt(message.recipient) === otherUserId
      );
      
      const isSentByThemToMe = (
        message.userId === otherUserId && 
        message.recipient && 
        parseInt(message.recipient) === CURRENT_USER.id
      );
      
      return isSentByMeToThem || isSentByThemToMe;
    }

    function getRecipientName(recipientId) {
      if (recipientId === 'all') return 'Tous';
      const user = users.find(u => u.id.toString() === recipientId.toString());
      return user ? user.name : 'Inconnu';
    }
  </script>
</body>
</html>