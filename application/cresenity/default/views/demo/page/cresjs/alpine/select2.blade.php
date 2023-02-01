<div x-data="{ options: [{id: 1, text: 'Option 1'}, {id: 2, text: 'Option 2'}, {id: 3, text: 'Option 3'}], selectedOption: '' }">
    <select class="your-select" x-model="selectedOption" @change="selectedOption = $event.target.value">
      <option value="">Please select an option</option>
      <template x-for="option in options" :key="option.id">
        <option :value="option.id" x-text="option.text"></option>
      </template>
    </select>
    <p>Selected value: <span x-text="selectedOption"></span></p>
  </div>

  <script>

    $('.your-select').select2();

  </script>
