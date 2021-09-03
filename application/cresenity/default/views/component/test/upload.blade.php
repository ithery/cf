<form cf:submit.prevent="save">
    
    @if ($photo)
        Photo Preview:
        <img src="{{ $photo->temporaryUrl() }}">
    @endif
    
    <input type="file" cf:model="photo">

    @error('photo') <span class="error">{{ $message }}</span> @enderror

    <button type="submit">Save Photo</button>
</form>