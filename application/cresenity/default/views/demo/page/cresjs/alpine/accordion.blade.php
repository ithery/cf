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

.accordion-body {
  background-color: #CC131F;
  max-height: 0;
  transition: max-height 0.2s ease-out;
  overflow:hidden;
}

</style>
<div x-data="accordionData()" class="accordion">
    <template x-for="(row, i) in data">
        <div class="accordion-item">
            <div class="accordion-title" x-on:click="setIsActive(i)" >
                <div x-text="row.title"></div>
                <div x-text="row.isActive ? '-' : '+'"></div>
            </div>
            <div class="accordion-body" x-bind:class="row.isActive ? 'show':''">
                <div class="accordion-content" x-text="row.content"></div>
            </div>
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
                this.$nextTick(()=>{
                    document.querySelectorAll('.accordion-body').forEach((el)=>{
                        el.style.maxHeight = null;
                    });
                    document.querySelectorAll('.accordion-body.show').forEach((el)=>{
                        el.style.maxHeight = el.scrollHeight + 'px';
                    });
                })

            }
        }
    }
</script>

@CAppEndPushScript
