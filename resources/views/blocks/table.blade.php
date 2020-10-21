<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        @if (count($blocks))
        <table class="table table-bordered">
            <thead>
                <tr class="table-head">
                    <th>Name</th>
                    <th style="width: 7%">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($blocks as $block)
                <tr>
                    <td>{{ $block->name }}</td>
                    <td>
                    <button class="btn btn-info btn-sm resource-update-btn" data-id="{{ $block->id }}"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btn-sm resource-delete-btn" data-id="{{ $block->id }}"><i class="fa fa-trash-o"></i></button></td>
                </tr>
                @endforeach
            </tbody>
        </table>

         <div id="pagination">
            {!!
                $blocks->render()
            !!}
        </div>
        @else
        <div class="no-data text-center">
            <p>No matching data was found</p>
        </div>
        @endif
    </div>
</div>