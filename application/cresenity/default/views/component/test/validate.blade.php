<div class="container mt-5">
    <form cf:submit.prevent="save" class="form">
        <input cf:model="name" type="text" class="form-control"/>
        @error('name') <span class="error text-danger">{{ $message }}</span> @enderror

        <div class="form-action mt-4">
        <button class="btn btn-primary">Save</button>
        </div>
    </form>
</div>