<div x-data="productVoaData()" id="item-editor" class="item-editor" @add-item.window="addItem($event)">

    <div class="card card-product-voa-item mb-3">
        <h6 class="card-header with-elements">
            <div class="card-header-title">@lang('Items')</div>
            <div class="card-header-elements ml-auto">

                <a href="javascript:;" class="btn btn-primary w-auto" data-toggle="modal" data-target="#modal-new-item"><i
                        class="ti ti-plus"></i> @lang('Add New Item')</a>

            </div>
        </h6>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <th width="50px" class="text-center">#</th>
                        <th>@lang('Product')</th>

                        <th width="200px">@lang('Buy Price')</th>
                        <th width="100px">@lang('Qty')</th>
                        <th width="300px">@lang('Subtotal')</th>
                    </thead>
                    <tbody>
                        <template x-for="(item, i) in items">
                            <tr class="product-voa-item">
                                <td>
                                    <a href="javascript:;" class="btn btn-danger" x-on:click="deleteItem(i)"
                                        title="@lang('Delete')">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </td>
                                <td>
                                    <input type="hidden" x-bind:value="item.id"
                                        x-bind:name="`items[${i}][id]`" />
                                    <input type="text" x-model="item.name" x-bind:id="'item_' + i"
                                        x-bind:name="`items[${i}][name]`" class="form-control material-control"
                                        readonly="readonly" placeholder="@lang('Product')" />
                                </td>
                                <td>
                                    <input type="text" x-model="item.price" x-bind:id="'price_' + i"
                                        x-bind:name="`items[${i}][price]`"
                                        class="form-control material-control text-right" x-autonumeric
                                        placeholder="@lang('Price')" data-m-dec="2" />
                                </td>
                                <td>
                                    <input type="text" x-model="item.qty" min="1"
                                        x-bind:id="'qty_' + i" x-bind:name="`items[${i}][qty]`" data-m-dec="0"
                                        x-autonumeric class="form-control material-control text-right input-qty"
                                        placeholder="Qty" x-on:change="reassignItem()" />
                                </td>


                                <td>
                                    <input type="text" x-model="item.subtotal"
                                        x-bind:id="'subtotal_' + i"
                                        x-bind:name="`items[${i}][subtotal]`"
                                        class="form-control material-control text-right" x-autonumeric data-m-dec="2"
                                        readonly="readonly" placeholder="@lang('Subtotal')" />

                                </td>

                            </tr>

                        </template>
                    </tbody>
                </table>
            </div>
            <div id="modal-new-item" class="modal" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content modal-new-item-wrapper" x-data=newItemHandler()>
                        <div class="modal-header asd">
                            <h5 class="modal-title">Add New Item</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            @CAppElement(function () {
                                return CElement_FormInput_SelectSearch::factory('new_item_id')
                                    ->setDataFromModel(Cresenity\Demo\Model\Country::class, function ($q) {})
                                    ->setKeyField('id')
                                    ->setFormat(function (Cresenity\Demo\Model\Country $country) {
                                    return '<div>' . $country->name . '</div>';
                                });
                            })
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="choose">
                                <i class="ti ti-plus"></i> Add Item
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-6 col-md-8">
                </div>
                <div class="col-sm-6 col-md-4">
                    <table class="ml-auto table product-voa-detail-table-subtotal">
                        <tbody>


                            <tr>
                                <td class="text-right align-middle font-weight-bold">Total</td>
                                <td width="300px" class="text-right align-middle">

                                    <input type="text" id="'product_voa_total" name="product_voa_total"
                                        class="form-control material-control text-right" data-m-dec="2" x-autonumeric
                                        x-ref="totalInput" placeholder="Total" x-model="total"
                                        readonly="readonly" />

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <hr class="border-1 my-3"></hr>
            <textarea name="master-detail-value-1" cols="30" rows="10" x-bind:value="value1"></textarea>
        </div>

    </div>

    <div class="card card-product-voa-item mb-3">
        <h6 class="card-header with-elements">
            <div class="card-header-title">@lang('Items With x-select2')</div>
            <div class="card-header-elements ml-auto">

                <a href="javascript:;" class="btn btn-primary w-auto" x-on:click="addItem2()"><i
                        class="ti ti-plus"></i> @lang('Add New Item')</a>

            </div>
        </h6>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <th width="50px" class="text-center">#</th>
                        <th>@lang('Product')</th>

                        <th width="200px">@lang('Buy Price')</th>
                        <th width="100px">@lang('Qty')</th>
                        <th width="300px">@lang('Subtotal')</th>
                    </thead>
                    <tbody>
                        <template x-for="(item, i) in items2">
                            <tr class="product-voa-item">
                                <td>
                                    <a href="javascript:;" class="btn btn-danger" x-on:click="deleteItem2(i)"
                                        title="@lang('Delete')">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </td>
                                <td>

                                    <select x-model="item.id" x-bind:id="'item_2_' + i"
                                        x-bind:name="`items2[${i}][name]`" class="form-control material-control"
                                        x-select2="selectData"
                                    >
                                    </select>
                                </td>
                                <td>
                                    <input type="text" x-model="item.price" x-bind:id="'price_2_' + i"
                                        x-bind:name="`items2[${i}][price]`"
                                        class="form-control material-control text-right" x-autonumeric
                                        placeholder="@lang('Price')" data-m-dec="2" />
                                </td>
                                <td>
                                    <input type="text" x-model="item.qty" min="1"
                                        x-bind:id="'qty_2_' + i" x-bind:name="`items2[${i}][qty]`" data-m-dec="0"
                                        x-autonumeric class="form-control material-control text-right input-qty"
                                        placeholder="Qty" x-on:change="reassignItem()" />
                                </td>


                                <td>
                                    <input type="text" x-model="item.subtotal"
                                        x-bind:id="'subtotal_' + i"
                                        x-bind:name="`items[${i}][subtotal]`"
                                        class="form-control material-control text-right" x-autonumeric data-m-dec="2"
                                        readonly="readonly" placeholder="@lang('Subtotal')" />

                                </td>

                            </tr>

                        </template>
                    </tbody>
                </table>
            </div>



            <div class="row">
                <div class="col-sm-6 col-md-8">
                </div>
                <div class="col-sm-6 col-md-4">
                    <table class="ml-auto table product-voa-detail-table-subtotal">
                        <tbody>


                            <tr>
                                <td class="text-right align-middle font-weight-bold">Total</td>
                                <td width="300px" class="text-right align-middle">

                                    <input type="text" id="'product_voa_total" name="product_voa_total"
                                        class="form-control material-control text-right" data-m-dec="2" x-autonumeric
                                        x-ref="totalInput" placeholder="Total" x-model="total2"
                                        readonly="readonly" />

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <hr class="border-1 my-3"></hr>
            <textarea name="master-detail-value-2" cols="30" rows="10" x-bind:value="value2"></textarea>
        </div>

    </div>

</div>

@CAppPushScript
<script>
    function productVoaData() {
        return {
            items: @json($items),
            items2 : @json($items),
            total: 0,
            total2: 0,
            selectData: @json($selectData),
            value1:'',
            value2:'',
            init() {

                this.$watch('items', () => {
                    this.calculateItemSubtotal();
                    this.recalculate();
                    this.updateValues();
                });
                this.$watch('items2', () => {
                    this.calculateItemSubtotal();
                    this.recalculate();
                    this.updateValues();
                });
                this.$nextTick(() => {
                    this.calculateItemSubtotal();
                    this.recalculate();
                    this.updateValues();
                });



            },
            updateValues()
            {
                this.value1 = JSON.stringify(this.items);
                this.value2 = JSON.stringify(this.items2);
            },
            reassignItem() {
                this.items = [].concat(this.items);
                this.items2 = [].concat(this.items2);
            },


            calculateItemSubtotal() {
                for (i in this.items) {
                    this.items[i].subtotal = this.items[i].price * this.items[i].qty;
                }
                for (i in this.items2) {
                    this.items2[i].subtotal = this.items2[i].price * this.items2[i].qty;
                }
            },
            deleteItem(index) {
                if (index > -1) {
                    cresenity.confirm({
                        owner: null,
                        message: 'Hapus Item ini?',
                        confirmCallback: (confirmed) => {
                            if (confirmed) {
                                this.items.splice(index, 1); // 2nd parameter means remove one item only
                            }
                        }
                    });
                }
            },
            deleteItem2(index) {
                if (index > -1) {
                    cresenity.confirm({
                        owner: null,
                        message: 'Hapus Item ini?',
                        confirmCallback: (confirmed) => {
                            if (confirmed) {
                                this.items2.splice(index, 1); // 2nd parameter means remove one item only
                            }
                        }
                    });
                }
            },
            recalculate() {
                let itemTotal = 0;

                for (i in this.items) {
                    itemTotal += parseFloat(this.items[i].subtotal);
                }


                this.total = itemTotal;

                let itemTotal2 = 0;

                for (i in this.items2) {
                    itemTotal2 += parseFloat(this.items2[i].subtotal);
                }


                this.total2 = itemTotal2;

            },
            addItem(event) {
                const payload = event.detail;
                console.log(payload);

                //validate item already exists

                let allItem = this.items;
                let itemExists = false;
                allItem.forEach(item => {

                    if (item.id == payload.id) {
                        itemExists = true;
                    }
                });

                if (itemExists) {
                    cresenity.toast('error','Item ' + payload.name + ' Already Exists');
                    return;
                }
                this.items = [].concat(this.items, payload);
                console.log(this.items);
            },
            addItem2() {
                console.log(this.items2);
                this.items2 = [].concat(this.items2, [{
                    id: null,
                    qty: 1,
                    price: 0,
                    subtotal: 0
                }]);
            }

        }
    }

    function newItemHandler() {
        return {
            submitted: false,
            async getItemPayload(itemId) {
                return new Promise((resolve, reject) => {
                    if (this.submitted) {
                        return resolve(false);
                    }

                    cresenity.blockElement('.modal-new-item-wrapper');


                    $.ajax({
                        url: "{{ c::url('demo/cresjs/alpine/masterDetail/json') }}" +'/'+ itemId,
                        cache: false,
                        type: 'get',
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function(responseData) {
                            console.log('responseData', responseData);
                            if (typeof responseData.errCode === 'undefined') {
                                cresenity.toast('error','Unknown error');
                                return resolve(false);
                            }
                            if (responseData.errCode != 0) {
                                cresenity.toast('error',responseData.errMessage);
                                return resolve(false);
                            }
                            this.submitted = true;
                            return resolve(responseData.data);
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            let error = thrownError;

                            if (typeof thrownError == 'object') {
                                console.log(JSON.stringify(thrownError));
                                error = thrownError.error;
                            }
                            cresenity.toast('error',error);
                            return resolve(false);
                        },
                        complete: function() {
                            cresenity.unblockElement('.modal-new-item-wrapper');

                        },
                    });
                });
            },
            async choose() {
                const select = $('#new_item_id');
                const itemId = select.val();
                if (!itemId) {
                    cresenity.toast('error', '@lang('Please choose country')');
                    return false;
                }
                const payload = await this.getItemPayload(itemId);

                let event = new Event('add-item');
                event.detail = payload;
                window.dispatchEvent(event);
                $('#modal-new-item').modal('hide');
            }
        }

    }
</script>
@CAppEndPushScript
