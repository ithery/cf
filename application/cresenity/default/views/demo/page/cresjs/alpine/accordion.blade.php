<style>
.accordion {
  max-width: 600px;
  margin: 2rem auto;
  color:#fff;
}

.accordion-title {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  cursor: pointer;
  background-color: #CC131F;
}

.accordion-title:hover {
  background-color: #FF131F;
}

.accordion-title,
.accordion-content {
  padding: 1rem;
}

.accordion-content {
  background-color: #CC131F;
}

</style>
<div x-data="accordionData()" class="accordion">
    <template x-for="(row, i) in data">
        <div class="accordion-item">
            <div class="accordion-title" x-on:click="setIsActive(i)">
                <div x-text="row.title"></div>
                <div x-text="row.isActive ? '-' : '+'"></div>
            </div>
            <div class="accordion-content" x-text="row.content" x-show="row.isActive"></div>
        </div>
    </template>
</div>
@CAppPushScript
<script>
    function accordionData() {
        return {
            data: @json($data),
            setIsActive(index) {
                this.data[index].isActive = !this.data[index].isActive;
            }
        }
    }
</script>

@CAppEndPushScript
