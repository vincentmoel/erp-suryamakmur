@extends('layouts.main', [
    'title' => 'User Details',
    'breadcrumbs' => [['title' => 'Users', 'link' => route('users.index')], ['title' => 'User Details']],
])

@section('content')
    <div class="card">
        <div class="card-body">
            <h6>User Information</h6>

            <div class="line-separator"></div>

            <table class="table customize-table">
                <tbody>
                    <tr>
                        <th>Name</th>
                        <td>{{ $user['name'] }}</td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td>{{ $user['username'] }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ \Carbon\Carbon::parse($user['created_at'])->translatedFormat('d F Y (H:i:s)') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ \Carbon\Carbon::parse($user['updated_at'])->translatedFormat('d F Y (H:i:s)') }}</td>
                    </tr>
                    <tr>
                        <th>Created By</th>
                        <td>{{ $user->user_created_by->name }}</td>
                    </tr>
                    <tr>
                        <th>Updated By</th>
                        <td>{{ $user->user_updated_by->name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .customize-table> :not(caption)>*>* {
            padding: 10px 10px !important;
        }
    </style>
@endsection
