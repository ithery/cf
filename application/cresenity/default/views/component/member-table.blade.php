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
        <div class="col">
            <a cf:click="doRedirect" class="btn btn-primary">Redirect</a>
        </div>
        <div cf:poll.1s>
            Current time: {{ c::now() }}
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

<div x-data="{ open: false }">
    <button @click="open = true">Show More...</button>

    <ul x-show="open" @click.away="open = false">
        <li><button cf:click="archive">Archive</button></li>
        <li><button cf:click="delete">Delete</button></li>
    </ul>
</div>

<script>

    document.addEventListener('cresenity:load', function () {
        window.cresenity.ui.on('alert', data => {
            const type = data[0];
            const message = data[1];

            let icon = 'fa fa-check mr-1';
            switch (type) {
                case 'success':
                    icon = 'fa fa-check mr-1';
                    break;
                case 'info':
                    icon = 'fa fa-info-circle mr-1';
                    break;
                case 'warning':
                    icon = 'fa fa-exclamation mr-1';
                    break;
                case 'danger':
                    icon = 'fa fa-times mr-1';
                    break;
                default:
                    icon = 'fa fa-info-circle mr-1';
                    break;
            }
            console.log(type, message);
        });
    })

</script>
