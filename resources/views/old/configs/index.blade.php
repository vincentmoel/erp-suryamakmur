@php
    $breadcrumbs = [['title' => $title]];
    if (Request::segment(2) == 'trashed') {
        $breadcrumbs[] = ['title' => "Restore $title"];
        $breadcrumbs[0]['link'] = route("$route.index");
    }
@endphp

@extends('layouts.main', [
    'title' => $title,
    'breadcrumbs' => $breadcrumbs,
])

@section('css')
@endsection

@section('content')
    <section class="datatables datatable-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4">
                            @include('partials.indexButtons', [
                                'module' => \App\Enums\Module::from($module)->name,
                                'indexRoute' => route("$route.index"),
                            ])
                        </div>
                        <div class="table-responsive">
                            <table class="dataTable table border table-striped table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Key</th>
                                        <th>Value</th>
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

    @include('configs.modals.editValue')
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.btn-edit-value', function() {
                const dataName = $(this).data('name');
                const dataValue = $(this).data('value');
                const dataUrl = $(this).data('url');

                // Populate the edit modal
                $('#edit-value-form').attr('action', dataUrl);
                $('#name').html(dataName);
                $('#value').val(dataValue);

                // Show the edit modal
                var editModal = new bootstrap.Modal(document.getElementById('edit-value-modal'));
                editModal.show();
            });


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
                        name: 'name',
                        className: 'align-content-center text-center'
                    },
                    {
                        data: 'key',
                        name: 'key',
                        className: 'align-content-center text-center'
                    },
                    {
                        data: 'value',
                        name: 'value',
                        className: 'align-content-center text-center'
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
