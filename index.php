<?php
$api_key = "AIzaSyCS1xSEgDXOrtJuB4F1InEQOlP0nywNB3o";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_message = $_POST["message"];

    $post_data = json_encode([
        "contents" => [[
            "parts" => [["text" => $user_message]]
        ]]
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$api_key");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $response_data = json_decode($response, true);

    if ($http_status !== 200 || isset($response_data["error"])) {
        $error_message = $response_data["error"]["message"] ?? "Unknown error";
        echo json_encode(["reply" => "Error fetching response: $error_message"]);
    } else {
        $bot_reply = $response_data["candidates"][0]["content"]["parts"][0]["text"] ?? "Error: Could not generate a response.";
        echo json_encode(["reply" => $bot_reply]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background: #121212; color: white; margin: 0; display: flex; height: 100vh; }
        
        /* Sidebar */
        .sidebar {
            width: 180px;
            background: #1A1A1A;
            padding: 20px;
            display: flex;
            flex-direction: column;
            border-right: 2px solid #333;
        }
        .sidebar h2 { font-size: 18px; font-weight: 700; margin-bottom: 20px; text-align: center; }
        .menu-item {
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 14px;
            transition: 0.3s;
            color: white;
        }
        .menu-item:hover { background: #292929; }
        .menu-item svg { width: 22px; height: 22px; margin-right: 10px; stroke: white; }

        /* Chat area */
        .chat-container { flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .chat-header { background: #1A1A1A; padding: 15px; text-align: center; font-size: 20px; font-weight: 700; border-bottom: 2px solid #333; }
        .chatbox { flex-grow: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; }
        
        .message {
            padding: 12px;
            margin: 8px;
            border-radius: 10px;
            max-width: 60%;
            font-size: 16px;
            line-height: 1.5;
            font-weight: 400;
        }
        .user { background: #000; color: white; align-self: flex-end; text-align: right; border: 1px solid white; }
        .bot { background: #333; color: white; align-self: flex-start; text-align: left; }

        /* Input area */
        .input-area {
            display: flex;
            background: #1A1A1A;
            padding: 10px;
            border-top: 2px solid #333;
        }
        input {
            flex-grow: 1;
            padding: 12px;
            background: #292929;
            border: none;
            color: white;
            border-radius: 5px;
            outline: none;
            font-size: 14px;
        }
        button {
            padding: 12px 18px;
            margin-left: 10px;
            background: black;
            color: white;
            border: 2px solid white;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
            font-size: 14px;
            font-weight: 600;
        }
        button:hover { background: white; color: black; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>ChatGPT</h2>
    <div class="menu-item" onclick="newChat()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 5v14m7-7H5"/></svg>
        New Chat
    </div>
    <div class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15 10l4.5-4.5m0 0L15 1m4.5 4.5H9a7.5 7.5 0 0 0 0 15h1.5"/></svg>
        History
    </div>
    <div class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 12h-3m0 0H9m3 0V9m0 3v3"/></svg>
        Settings
    </div>
    <div class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
        More Options
    </div>
</div>

<!-- Chat Area -->
<div class="chat-container">
    <div class="chat-header">ChatGPT Clone</div>
    <div class="chatbox" id="chatbox"></div>

    <div class="input-area">
        <input type="text" id="userInput" placeholder="Ask anything...">
        <button onclick="sendMessage()">Send</button>
    </div>
</div>

<script>
function sendMessage() {
    let userInput = document.getElementById("userInput").value;
    if (!userInput) return;

    let chatbox = document.getElementById("chatbox");
    chatbox.innerHTML += `<div class='message user'>${userInput}</div>`;
    document.getElementById("userInput").value = "";

    fetch("index.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "message=" + encodeURIComponent(userInput)
    })
    .then(response => response.json())
    .then(data => {
        chatbox.innerHTML += `<div class='message bot'>${data.reply}</div>`;
        chatbox.scrollTop = chatbox.scrollHeight;
    });
}

function newChat() {
    document.getElementById("chatbox").innerHTML = "";
}
</script>

</body>
</html>
