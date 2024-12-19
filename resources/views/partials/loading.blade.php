<div x-data="{ text: 'ThanShoes...' }" class="border border-gray-800 bg-gray-900 text-green-500 font-mono text-base p-6 w-48 mx-auto mt-24 shadow-lg rounded relative overflow-hidden box-border">
    <div class="absolute top-0 left-0 right-0 h-6 bg-gray-800 rounded-t px-2 box-border flex justify-between items-center">
        <div class="text-gray-300">Chờ tí</div>
        <div class="flex space-x-2">
            <div class="w-3 h-3 bg-red-600 rounded-full"></div>
            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
            <div class="w-3 h-3 bg-green-600 rounded-full"></div>
        </div>
    </div>
    <div class="inline-block whitespace-nowrap overflow-hidden border-r-2 border-green-500 mt-6" x-text="text" x-init="setInterval(() => text = text === 'ThanShoes...' ? '' : 'ThanShoes...', 4000)"></div>
</div>
