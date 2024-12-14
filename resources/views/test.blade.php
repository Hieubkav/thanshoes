<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel with Vue 3</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <hello-world></hello-world>
        <div id="recaptcha-container"></div> <!-- Thêm phần tử này -->
    </div>

    <div class="p-4">
        <div class="mb-4">
            <label for="phone-number" class="block text-sm font-medium text-gray-700">Phone number</label>
            <input type="text" id="phone-number"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            <button id="send-otp" class="mt-2 w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">SEND OTP</button>
        </div>
        <div>
            <label for="otp" class="block text-sm font-medium text-gray-700">OTP</label>
            <input type="text" id="otp"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            <button id="verify-otp" class="mt-2 w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">VERIFY OTP</button>
        </div>
    </div>
</body>
</html>