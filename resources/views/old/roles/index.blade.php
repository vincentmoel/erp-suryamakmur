@extends('layouts.main', [
    "title" => "Roles",
    "breadcrumbs" => [
        ['title' => 'Roles'],
    ]
])

@section('css')
    <link rel="stylesheet" href="{{ asset('src/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css?' . date('H:i:s')) }}">
@endsection

@section('content')
<section class="datatables datatable-container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        @include('partials.indexButtons',[
                            'module'        => \App\Enums\Module::Role->name,
                            'createRoute'   => route('roles.create'),
                        ])
                    </div>
                    <div class="table-responsive">
                        <table class="dataTable table border table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Created By</th>
                                    <th>Updated By</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                           
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
    <script src="{{ asset('src/libs/datatables.net/js/jquery.dataTables.min.js?' . date('Y-m-d H:i:s')) }}"></script>
    <script src="{{ asset('src/js/custom-script.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('.dataTable').DataTable({
                stripeClasses: false,
                ajax: {
                    url: "{{ url()->current() }}",
                },
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: true,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'updated_by',
                        name: 'updated_by'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ]
            });
        });
    </script>
@endsection