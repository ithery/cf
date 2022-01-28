@CAppPushScript
<script>

    function getEditableData(item) {

        return {
            init() {
                this.$watch('item.qty',()=> {
                    this.item.subtotal = this.item.qty * this.item.price;
                });
            },
            item:item,
            isEditing: false,
            toggleEditingState() {
                this.isEditing = !this.isEditing;

                if (this.isEditing) {
                    this.$nextTick(() => {
                        this.$refs.inputTranslation.focus();
                    });
                }
            },

            saveEditing() {
                let success = true;
                if(item.initialValue != item.value) {
                    let success = false;
                    let ajaxOptions = {};
                    ajaxOptions.url = item.storeTranslationUrl;
                    ajaxOptions.dataAddition = item;
                    ajaxOptions.method = 'post';
                    ajaxOptions.onSuccess = function(data) {
                        cresenity.toast('success','{{ c::__('translation.translation_updated') }}');
                    }
                    cresenity.ajax(ajaxOptions);
                }
                if(success) {
                    this.toggleEditingState();
                }

            },
            disableEditing() {
                this.isEditing = false;
            },
            remove() {

                cresenity.confirm({
                    owner: this.$refs.btnDelete,
                    message: '{{ c::__('translation.prompt_delete') }}',
                    confirmCallback:  (confirmed) => {
                        if(confirmed) {
                            this.$refs.tr.remove();
                        }

                    }
                });

            }
        };

    }
</script>
@CAppEndPushScript
