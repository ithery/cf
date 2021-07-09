<div class="panel panel-primary">
    <div class="panel-heading">Manage Category TreeView</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <h3>Category List</h3>
                <ul id="tree1">
                    @foreach($categories as $category)
                    <li>
                        {{ carr::get($category,'title') }}

                        @if(count(carr::get($category,'childs',[])))

                        @include('administrator.documentation.tree-child',['childs' => $category['childs']])

                        @endif

                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Add New Category</h3>

                

                

            </div>
        </div>
    </div>
</div>

