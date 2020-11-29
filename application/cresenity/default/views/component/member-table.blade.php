<div>
    <div class="row mb-4">
        <div class="col form-inline">
            Per Page: &nbsp;
            <select cf:model="perPage" class="form-control">
                <option>10</option>
                <option>15</option>
                <option>25</option>
            </select>
        </div>

        <div class="col">
            <input cf:model="search" class="form-control" type="text" placeholder="Search Contacts...">
        </div>
    </div>

    <div class="row">
        <table class="table">
            <thead>
                <tr>
                    <th><a cf:click.prevent="sortBy('name')" role="button" href="#">
                        Name
                        @include('includes._sort-icon', ['field' => 'name'])
                    </a></th>
                    <th><a cf:click.prevent="sortBy('email')" role="button" href="#">
                        Email
                        @include('includes._sort-icon', ['field' => 'email'])
                    </a></th>
                    <th><a cf:click.prevent="sortBy('birthdate')" role="button" href="#">
                        Birthdate
                        @include('includes._sort-icon', ['field' => 'birthdate'])
                    </a></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $ind=>$member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>
                            @if ($member->birthdate)
                                {{ $member->birthdate->format('m-d-Y') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $members->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $members->firstItem() }} to {{ $members->lastItem() }} out of {{ $members->total() }} results
        </div>
    </div>
</div>