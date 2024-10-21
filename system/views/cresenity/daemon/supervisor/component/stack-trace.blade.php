@php
    $uniqid = uniqid();
@endphp
<div id="{{ $uniqid }}" x-data="supervisorStackTrace{{ $uniqid }}(@js($trace))">
    <table class="table mb-0">
        <tbody>
            <template x-for="line in lines()">
                <tr>
                    <td class="card-bg-secondary"><code x-text="line"></code></td>
                </tr>
            </template>

            <tr x-show="! showAll">
                <td class="card-bg-secondary"><a href="*" x-on:click.prevent="showAll = true">Show All</a></td>
            </tr>
        </tbody>
    </table>
</div>

@CAppPushScript
<script>
    window.supervisorStackTrace{{ $uniqid }} = function(trace) {
        return {
            trace: trace,
            minimumLines: 5,
            showAll: false,
            lines() {
                return this.trace.slice(0, this.showAll ? 1000 : this.minimumLines);
            }
        }
    }
</script>
@CAppEndPushScript
