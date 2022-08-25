
<div class="alpine-loader rex-ajax-loader rex-visible"><div class="rex-ajax-loader-elements"><div class="rex-ajax-loader-element1 rex-ajax-loader-element"></div><div class="rex-ajax-loader-element2 rex-ajax-loader-element"></div><div class="rex-ajax-loader-element3 rex-ajax-loader-element"></div><div class="rex-ajax-loader-element4 rex-ajax-loader-element"></div><div class="rex-ajax-loader-element5 rex-ajax-loader-element"></div></div></div>

<section class="repeater">

    <div x-data="repeater()" id="x-repeater">

        <template x-if="groups.length">
            <a href="#" type="button" class="btn btn-primary mb-3" @click.prevent="addGroup(0)"><i class="fas fa-plus-circle"></i> New Group</a>
        </template>

        <template x-for="(group, groupIndex) in groups" :key="groupIndex">
            <div class="repeater-group">
                <header class="mb-3 pb-3">
                    <div class="container-fluid p-0">
                        <div class="row">
                            <div class="col-sm-9"><strong>Group</strong></div>
                            <div class="col-sm-3 text-right">

                                <template x-if="groupIndex !== 0">
                                    <a href="#" @click.prevent="moveGroup(groupIndex, groupIndex-1)" class="button move"><i class="fas fa-chevron-up"></i></a>
                                </template>

                                <template x-if="groupIndex+1 < groups.length">
                                    <a href="#" @click.prevent="moveGroup(groupIndex, groupIndex+1)" class="button move"><i class="fas fa-chevron-down"></i></a>
                                </template>

                                <a href="#" @click.prevent="removeGroup(groupIndex)" class="button remove"><i class="fas fa-times"></i></a>
                            </div>
                        </div>
                    </div>
                </header>

                <div>
                    <!-- Group field definitions -->
                    <label :for="'group-headline-'+groupIndex">Headline</label>
                    <input type="text"
                           class="form-control mb-3"
                           placeholder="Headline"
                           x-model="group.headline"
                           type="text"
                           name="headline[]"
                           :id="'group-headline-'+groupIndex"
                           x-on:change="updateValues()">

                    <template x-for="(field, index) in group.fields" :key="index">
                        <div class="repeater-group">
                            <header class="mb-3 pb-3">
                                <div class="container-fluid p-0">
                                    <div class="row">
                                        <div class="col-sm-9"><strong>Field Group</strong></div>
                                        <div class="col-sm-3 text-right">

                                            <template x-if="index !== 0">
                                                <a href="#" @click.prevent="moveField(groupIndex, index, index-1)" class="button move"><i class="fas fa-chevron-up"></i></a>
                                            </template>
                                            <template x-if="index+1 < group.fields.length">
                                                <a href="#" @click.prevent="moveField(groupIndex, index, index+1)" class="button move"><i class="fas fa-chevron-down"></i></a>
                                            </template>

                                            <a href="#" @click.prevent="removeField(groupIndex, index)" class="button remove"><i class="fas fa-times"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </header>

                            <div>
                                <!-- Field definitions within the group -->
                                <label :for="'title-'+groupIndex+'-'+index">Title</label>
                                <input type="text"
                                       class="form-control mb-3"
                                       placeholder="Titel"
                                       name="title[]"
                                       :id="'title-'+groupIndex+'-'+index"
                                       x-model="field.title"
                                       x-on:change="updateValues()">

                                <label :for="'text-'+groupIndex+'-'+index">Text</label>
                                <textarea class="form-control mb-3"
                                          type="text"
                                          name="text[]"
                                          placeholder="Text"
                                          :id="'text-'+groupIndex+'-'+index"
                                          x-model="field.text"
                                          x-on:change="updateValues()"></textarea>

                            </div>
                        </div>
                    </template>

                    <a href="#" type="button" class="btn btn-primary" @click.prevent="addFields(groupIndex)"><i class="fas fa-plus-circle"></i> Add Fields</a>
                </div>
            </div>
        </template>

        <a href="#" type="button" class="btn btn-primary" @click.prevent="addGroup(1)"><i class="fas fa-plus-circle"></i> New Group</a>

        <!--  Repeater Value in which the data is stored as JSON...  -->
        <!--  Data is updated after blur  -->
        <hr class="border-1 my-3"></hr>
        <textarea name="repeater-value" cols="30" rows="10" x-bind:value="value"></textarea>
    </div>
</section>


<style>
    section.repeater .repeater-group {
        background-color: #fff;
        padding: 10px;
        border: 1px solid #9ca5b2;
        margin-bottom: 10px;
        transition: background-color 0.3s ease-in-out;
    }

    section.repeater .repeater-group:hover {
        background-color: #f8f8f8;
    }

    section.repeater .repeater-group header {
        border-bottom: 3px solid #e9f5ef;
    }

    section.repeater .repeater-group .button {
        padding: 5px;
        line-height: 1;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        text-decoration: none;
        transition: background-color 0.3s ease-in-out;
    }

    section.repeater .repeater-group .button:hover {
        background-color: #dfe3e9;
    }

    section.repeater .repeater-group .button.move {
        color: #3c4d60;
    }

    section.repeater .repeater-group .button.remove {
        color: #d9534f;
    }

    /* utilities... */
    section.repeater .mb-3 {
        margin-bottom: 1rem;
    }

    section.repeater .pb-0 {
        padding-bottom: 0;
    }

    section.repeater .pb-3 {
        padding-bottom: 1rem;
    }
</style>

@CAppPushScript
<script>
    window.repeater = () =>
    {
        return {
            groups: [{headline:'Headline Value',fields:[{title:'Awesome Title',text:'Some Text'}]}],
            value: '',
            $alpineLoader: document.querySelector('.alpine-loader'),
            init() {

                this.value = JSON.stringify(this.groups);

                this.$nextTick(() =>
                {
                    this.$alpineLoader.classList.remove('rex-visible');
                });
            },
            addGroup(position) {
                /**
                 * 0 = top
                 * 1 = bottom
                 */

                /**
                 * Object Group Definition
                 */
                const obj = {
                    headline: '',
                    fields: [],
                };

                if(position) {
                    this.groups.push(obj);
                }
                else {
                    this.groups.unshift(obj);
                }
            },
            addFields(index)
            {
                /**
                 * Objekt entsprechend der Felddefinitionen
                 */
                this.groups[index].fields.push({
                    title: '',
                    text: '',
                });
            },
            removeGroup(index)
            {
                this.groups.splice(index, 1);
                this.updateValues();
            },
            removeField(groupIndex, fieldIndex)
            {
                this.groups[groupIndex].fields.splice(fieldIndex, 1);
                this.updateValues();
            },
            updateValues()
            {
                /**
                 * Groups are stored as a string in the value model...
                 */
                this.value = JSON.stringify(this.groups);
            },

            moveGroup(from, to) {
                this.groups.splice(to, 0, this.groups.splice(from, 1)[0]);
                this.updateValues();
            },
            moveField(groupIndex, from, to) {
                this.groups[groupIndex].fields.splice(to, 0, this.groups[groupIndex].fields.splice(from, 1)[0]);
                this.updateValues();
            },

        }
    }
</script>
@CAppEndPushScript
