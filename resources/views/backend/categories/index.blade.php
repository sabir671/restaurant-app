@extends('backend.layouts.app')
@section('title', '| Categories')

@section('breadcrumb')
<div class="page-header">
    <h1 class="page-title">Categories List</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Categories</li>
        </ol>
    </div>
</div>
@endsection
@section('content')
    <div class="card">
        <div class="card-header justify-content-between">
            <h3 class="card-title font-weight-bold">Categories</h3>
            {{-- @can('add_user') --}}
                <button type="button" class="btn dark-icon btn-primary" data-act="ajax-modal" data-method="get"
                    data-action-url="{{ route('categories.create') }}" data-title="Add New Categories">
                    <i class="ri-add-fill"></i> Add Category
                </button>
            {{-- @endcan --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="categories_datatable" class="table table-bordered text-nowrap key-buttons border-bottom w-100">
                    <thead>
                        <tr>
                            <th class="border-bottom-0">#</th>
                            <th class="border-bottom-0">Name</th>
                            <th class="border-bottom-0">description</th>
                            <th class="border-bottom-0">image</th>
                            <th class="border-bottom-0">Status</th>
                            <th class="border-bottom-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {
            $('#categories_datatable').DataTable({
                ajax: '{{ route('categories-datatable') }}',
                processing: true,
                serverSide: true,
                scrollX: false,
                autoWidth: true,
                columnDefs: [{
                        width: 1,
                        targets: 5
                    },
                    {
                        width: '5%',
                        targets: 0
                    }
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
