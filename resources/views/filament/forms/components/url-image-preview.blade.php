<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if ($getState())
        <div class="flex justify-center">
            <img 
                src="{{ $getState() }}" 
                alt="Preview"
                class="max-w-md rounded-lg shadow-sm"
                style="max-height: 200px; object-fit: contain;"
            />
        </div>
    @endif
</x-dynamic-component>