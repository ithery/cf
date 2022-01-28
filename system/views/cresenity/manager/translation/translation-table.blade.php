@php
if(!isset($canCreate))    {
    $canCreate = false;
}
if(!isset($canDelete))    {
    $canDelete = false;
}
@endphp
<div class="table-responsive">
    <table
        id="capp-translation-item-table"
        class="table responsive translation-detail-table translation-detail-table-{{ cstr::snake(cstr::studly(strtolower($language)),'-') }}"
    >
        <thead>

            <tr>
                <th class="thead sortable" width="20%">{{ c::__('translation.group_single') }}</th>
                <th class="thead sortable" width="20%">{{ c::__('translation.key') }}</th>
                <th class="thead sortable">{{ CF::config('app.locale') }}</th>
                <th class="thead sortable">{{ $language }}</th>
                @if($canEdit||$canDelete)
                <th class="thead sortable" width="100px">{{ c::__('translation.action') }}</th>
                @endif
            </tr>

        </thead>
        <tbody>
            @foreach($translations as $type => $items)

                @foreach($items as $group => $translations)

                    @foreach($translations as $key => $value)

                        @if(!is_array($value[CF::config('app.locale')]))
                            @php
                                $item = [
                                    'initialValue'=>$value[$language],
                                    'value'=>$value[$language],
                                    'language'=>$language,
                                    'group'=>$group,
                                    'translationKey'=>$key,
                                    'storeTranslationUrl'=>$storeTranslationUrl,

                                ];
                            @endphp
                            <tr x-data="getEditableData({{ json_encode($item) }})"
                            x-ref="tr"
                            x-init="init"
                            @click.away="disableEditing">
                                <td>{{ $group }}</td>
                                <td>{{ $key }}</td>
                                <td>{{ $value[CF::config('app.locale')] }}</td>
                                <td>
                                    <span x-show="!isEditing" x-text="item.value">{{ carr::get($item, 'item.value') }}</span>
                                    <input type="text" x-ref="inputTranslation" x-model="item.value" x-show="isEditing" @keydown.enter="disableEditing" @keydown.window.escape="disableEditing" class="form-control" x-ref="input">
                                </td>
                                @if($canEdit||$canDelete)
                                <td class="capp-not-printable">
                                    @if($canEdit)
                                    <button x-show="!isEditing" class="btn btn-light" @click="toggleEditingState"><i class="ti ti-pencil" ></i></button>
                                    <button x-show="isEditing" class="btn btn-light" @click="saveEditing"><i class="ti ti-check" ></i></button>
                                    @endif
                                    @if($canDelete)
                                    <button class="btn btn-light translation-delete" x-ref="btnDelete" @click="remove(itemIndex)"><i class="ti ti-trash" ></i></button>
                                    @endif
                                </td>
                                @endif

                            </tr>
                        @endif

                    @endforeach

                @endforeach

            @endforeach
        </tbody>

    </table>
</div>
