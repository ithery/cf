<div class="cres-editor-js-block">
    <table class="cres-editor-js-table">
        @foreach ($content as $row)
            <tr>
                @foreach ($row as $content)
                    <td>
                        {{ $content }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>
</div>
