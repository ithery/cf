<div>
    <form cf:submit.prevent="save">
        <input cf:model="name" type="text" class="form-control"/>
        @error('name') <span class="error text-danger">{{ $message }}</span> @enderror
        <br/><!-- comment -->

        <button class="btn btn-primary">Save</button>
    </form>
</div>