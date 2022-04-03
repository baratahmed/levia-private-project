@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Contact Requests')

@section('brand', 'Contact Requests')

@section('content')
    <div id="container">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Created at</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contact)
                    <tr>
                        <td>{{ $contact->id }}</td>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->subject }}</td>
                        <td>{{ $contact->message }}</td>
                        <td>{{ $contact->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $contacts->links() }}
    </div>
@endsection

@section('extra-js')
    <script>
        $('li').removeClass('active');
        $('#menu-contact').parent().addClass('active');
    </script>
@endsection