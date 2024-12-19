<div id="toast-success"
    class=" z-50 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 fixed top-4 right-4"
    role="alert">
    <div
        class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8
    @if ($color == 'green') text-green-500 bg-green-100 
    @elseif ($color == 'red') text-red-500 bg-red-100 
    @else text-yellow-500 bg-yellow-100 @endif 
    
     rounded-lg dark:bg-green-800 dark:text-green-200">


        @if ($color == 'green')
            <i class="fas fa-check-circle"></i>
        @elseif ($color == 'red')
            <i class="fas fa-exclamation-circle"></i>
        @else
            <i class="fas fa-info-circle"></i>
        @endif

    </div>
    <div class="ms-3 text-sm font-normal">{{ $message }}</div>
    <button type="button"
        class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
        data-dismiss-target="#toast-success" aria-label="Close">
        <span class="sr-only">Close</span>
        <i class="fas fa-times"></i>
    </button>
</div>

<script>
    let toast = document.getElementById('toast-success');

    toast.classList.add('hidden');
    document.addEventListener('{{ $event }}', event => {
        // Show id toast trong 3s sau đó từ từ mờ dần rồi biến mất
        let toast = document.getElementById('toast-success');
        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 2500);
    });
</script>


{{-- 
@include('partials.dispatch', [
        'event' => 'show_cho_vui',
        'message' => 'Post with ID  was created!',
        'color' => 'yellow'
]) 
--}}
