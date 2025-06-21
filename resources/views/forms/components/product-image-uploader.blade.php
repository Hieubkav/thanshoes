@php
    $id = $getId();
    $statePath = $getStatePath();
@endphp

<div
    x-data="{
        images: @entangle('state'),
        variantImages: @js($getVariantImages()),
        uploadingFiles: false,
        addingUrls: false,
        urls: '',
        
        init() {
            new Sortable(this.$refs.imageList, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: (evt) => {
                    const ids = Array.from(evt.to.children).map(el => el.dataset.id)
                    $wire.call('{{ $statePath }}.reorder', ids)
                }
            })
        }
    }"
    class="space-y-4"
>
    <!-- Danh sách ảnh hiện tại -->
    <div class="grid grid-cols-3 gap-4 md:grid-cols-4 lg:grid-cols-6" x-ref="imageList">
        <template x-for="image in images" :key="image.id">
            <div :data-id="image.id" class="group relative aspect-square rounded-lg bg-gray-100 shadow-sm">
                <img :src="image.image.startsWith('http') ? image.image : '/storage/' + image.image"
                     loading="lazy"
                     class="h-full w-full rounded-lg object-cover"
                     :alt="'Product image ' + image.id">
                
                <div class="absolute inset-0 rounded-lg bg-black/50 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                    <div class="flex h-full items-center justify-center gap-2">
                        <button type="button" 
                                class="drag-handle flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30"
                                title="Kéo để sắp xếp">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                            </svg>
                        </button>
                        
                        <button type="button"
                                @click="$wire.call('{{ $statePath }}.deleteImage', image.id)"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30"
                                title="Xóa ảnh">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div x-show="image.type == 'variant'"
                     class="absolute left-2 top-2 rounded bg-primary-500 px-1.5 py-0.5 text-xs font-medium text-white">
                    Variant
                </div>
            </div>
        </template>
    </div>

    <!-- Panel điều khiển -->
    <div class="flex flex-wrap gap-4">
        <!-- Upload từ máy -->
        <div>
            <input type="file" 
                   x-ref="fileInput" 
                   @change="async (e) => {
                       const files = Array.from(e.target.files)
                       if (!files.length) return
                       
                       uploadingFiles = true
                       try {
                           await $wire.call('{{ $statePath }}.uploadMultiple', files)
                       } finally {
                           uploadingFiles = false
                           e.target.value = null
                       }
                   }" 
                   multiple 
                   accept="image/*" 
                   class="hidden">
            
            <button type="button"
                    @click="$refs.fileInput.click()"
                    :disabled="uploadingFiles"
                    class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-white shadow hover:bg-primary-500 disabled:opacity-50">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span x-text="uploadingFiles ? 'Đang tải lên...' : 'Tải ảnh lên'"></span>
            </button>
        </div>

        <!-- Thêm từ URL -->
        <div x-show="!addingUrls">
            <button type="button"
                    @click="addingUrls = true"
                    class="inline-flex items-center rounded-lg bg-gray-600 px-4 py-2 text-white shadow hover:bg-gray-500">
                Thêm từ URL
            </button>
        </div>
        
        <!-- Form nhập URL -->
        <div x-show="addingUrls" class="w-full">
            <div class="flex gap-4">
                <div class="flex-1">
                    <textarea x-model="urls"
                              placeholder="Nhập một hoặc nhiều URL (mỗi URL một dòng)"
                              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                </div>
                
                <div class="flex flex-col gap-2">
                    <button type="button"
                            @click="async () => {
                                if (!urls.trim()) return
                                const urlList = urls.split('\n').filter(url => url.trim())
                                if (!urlList.length) return
                                
                                await $wire.call('{{ $statePath }}.addFromUrls', urlList)
                                urls = ''
                                addingUrls = false
                            }"
                            class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-white shadow hover:bg-primary-500">
                        Thêm
                    </button>
                    
                    <button type="button"
                            @click="addingUrls = false; urls = ''"
                            class="inline-flex items-center rounded-lg bg-gray-600 px-4 py-2 text-white shadow hover:bg-gray-500">
                        Hủy
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách ảnh từ variant -->
    <div x-show="variantImages.length > 0" class="rounded-xl border border-gray-200 p-4">
        <h3 class="text-base font-medium">Ảnh từ biến thể sản phẩm</h3>
        
        <div class="mt-4 grid grid-cols-3 gap-4 md:grid-cols-4 lg:grid-cols-6">
            <template x-for="image in variantImages" :key="image.id">
                <div class="group relative aspect-square rounded-lg bg-gray-100">
                    <input type="checkbox" 
                           :value="image.id" 
                           name="variant_images[]"
                           class="absolute left-2 top-2 z-10">
                           
                    <img :src="image.image.startsWith('http') ? image.image : '/storage/' + image.image"
                         class="h-full w-full rounded-lg object-cover" 
                         :alt="'Variant image ' + image.id">
                </div>
            </template>
        </div>
        
        <div class="mt-4">
            <button type="button"
                    @click="async () => {
                        const selected = Array.from(document.querySelectorAll('[name=\'variant_images[]\']:checked')).map(el => el.value)
                        if (!selected.length) return
                        
                        await $wire.call('{{ $statePath }}.addFromVariantImages', selected)
                        variantImages = variantImages.filter(img => !selected.includes(img.id.toString()))
                    }"
                    class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-white shadow hover:bg-primary-500">
                Thêm ảnh đã chọn
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js" defer></script>