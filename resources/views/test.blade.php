<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Image Editor</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .image-container {
            position: relative;
            display: inline-block;
        }

        .text-overlay {
            position: absolute;
            white-space: nowrap;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-gray-100 h-screen w-screen flex items-center justify-center p-4">

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full max-w-4xl h-full">
    <!-- Image Column -->
    <div class="image-container w-full flex items-center justify-center bg-white rounded shadow-lg p-4">
        <img src="{{asset('images/cup.jpg')}}" alt="Demo Image"
             class="rounded shadow-lg h-72 lg:h-[28rem] object-cover">
        <!-- Text Overlay -->
        <div class="text-overlay" id="textOverlay"
             style="top: 50%; left: 50%; color: #000000; font-size: 32px; font-weight: bold; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
            Hello!
        </div>
    </div>

    <!-- Controls Column -->
    <div class="p-6 bg-white shadow-lg rounded h-full overflow-y-auto space-y-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Enter Text:</label>
            <input type="text" id="textInput"
                   class="w-full border border-gray-300 shadow-sm p-3 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                   value="Hello!">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Font Size:</label>
            <input type="number" id="fontSizeInput"
                   class="w-full border border-gray-300 shadow-sm p-3 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                   value="32">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Text Color:</label>
            <input type="color" id="textColorInput" class="w-full border p-3 rounded focus:ring-2 focus:ring-blue-400"
                   value="#000000">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-2">X Position:</label>
                <input type="number" id="xPositionInput"
                       class="w-full border border-gray-300 shadow-sm p-3 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                       value="300">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Y Position:</label>
                <input type="number" id="yPositionInput"
                       class="w-full border border-gray-300 shadow-sm p-3 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                       value="200">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const textOverlay = document.getElementById('textOverlay');
        const textInput = document.getElementById('textInput');
        textInput.value = undefined;
        const fontSizeInput = document.getElementById('fontSizeInput');
        const textColorInput = document.getElementById('textColorInput');
        const xPositionInput = document.getElementById('xPositionInput');
        const yPositionInput = document.getElementById('yPositionInput');

        function updateOverlay() {
            textOverlay.textContent = textInput.value;
            textOverlay.style.fontSize = `${fontSizeInput.value}px`;
            textOverlay.style.color = textColorInput.value;
            textOverlay.style.left = `${xPositionInput.value}px`;
            textOverlay.style.top = `${yPositionInput.value}px`;
        }

        textInput.addEventListener('input', updateOverlay);
        fontSizeInput.addEventListener('input', updateOverlay);
        textColorInput.addEventListener('input', updateOverlay);
        xPositionInput.addEventListener('input', updateOverlay);
        yPositionInput.addEventListener('input', updateOverlay);
    });
</script>

</body>
</html>
