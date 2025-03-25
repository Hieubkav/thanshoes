@extends('layouts.shoplayout')

@section('content')
<div class="container mx-auto px-4 pt-20 min-h-screen">
    {{-- Header with search --}}
    {{-- <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Bài viết mới nhất</h1>
        <div class="relative">
            <input type="text" 
                   placeholder="Tìm kiếm bài viết..." 
                   class="w-full md:w-64 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button class="absolute right-3 top-2.5">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </div> --}}
    
    {{-- Posts Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <article class="bg-white rounded-xl shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300 ease-in-out">
            <a href="{{ route('posts.show', $post->id) }}" class="block">
                <div class="relative overflow-hidden h-48">
                    @if($post->thumbnail)
                        <div class="absolute inset-0 bg-gray-200 animate-pulse"></div>
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" 
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500 ease-in-out">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-blue-100 via-blue-200 to-blue-100 flex items-center justify-center group-hover:from-blue-200 group-hover:to-blue-300 transition-colors duration-300">
                            <svg class="w-16 h-16 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    @endif
                    
                    {{-- Category/Tag Badge --}}
                    <div class="absolute top-3 right-3">
                        <span class="bg-blue-500 text-white text-xs px-3 py-1 rounded-full uppercase tracking-wide font-semibold shadow-lg">
                            Blog
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $post->created_at->format('d/m/Y') }}
                        </span>
                        <span class="mx-3">•</span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            123 lượt xem
                        </span>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition-colors duration-200 line-clamp-2">
                        {{ $post->title }}
                    </h2>

                    <div class="text-gray-600 line-clamp-3 mb-4">
                        {!! \Illuminate\Support\Str::limit(strip_tags($post->content), 150) !!}
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Tags, News
                        </div>
                        <span class="text-blue-500 text-sm font-semibold group-hover:translate-x-2 transition-transform duration-200 inline-flex items-center">
                            Đọc thêm
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
        </article>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-12 mb-8">
        {{ $posts->links() }}
    </div>
</div>
@endsection