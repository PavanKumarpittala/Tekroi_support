@extends('layouts.app')

@section('content')
    <!-- yajra database table link---->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- End Of Data Tables--->


    <main class="app-content  ">

        <div class="container mt-3 text-center bg-light p-3">
            @if (session()->has('success'))
                <h6 class="alert alert-success">{{ session('success') }}</h6>
            @endif
        </div>

        <header class="container ">
            <div class="d-flex justify-content-end align-items-center h-100">
                <a class="btn btn-lg mt-3" href="{{ route('birthdayform') }}" role="button" style="background: linear-gradient(to right, #ff6a00, #ee0979); color: #fff;">Add Employee Birthday Details</a>
            </div>
            <br />
            <table class="datatable" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">Employee Name </th>
                        <th scope="col">Employee Id</th>
                        <th scope="col">Employee Date of Birth</th>
                        <th scope="col">Employee Role</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($birthdays as $birthday)
                        <tr>
                            <td> {{ $birthday->ename }}</td>
                            <td>{{ $birthday->eid }}</td>
                            <td>{{ $birthday->edob }}</td>
                            <td>{{ $birthday->erole }}</td>
                            <td><a href="{{ route('employeeformpostedit', ['id' => $birthday]) }}"
                                    class="btn btn-success">Edit</a>
                            </td>
                            <td>


                                <form action="{{ route('employeeformpostdelete', ['id' => $birthday]) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <input type="submit" value="delete" class="btn btn-warning">
                                </form>




                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </header>
    </main>
    <!-- data table js -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
@endsection


{{-- @extends('layout')
@section('title', 'birthday')
@section('content')
    @auth
        <header class="container ">
            <div class="d-flex justify-content-center align-items-center h-100">
                <a class="btn btn-dark mt-3 btn-lg" href="{{ route('employeeform') }}" role="button">Add Employee Details</a>
            </div>
            <div class="container mt-3 text-center bg-light p-3">
                @if (session()->has('success'))
                    <h6 class="alert alert-success">{{ session('success') }}</h6>
                @endif
            </div>
            <table class="datatable" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">Employee Name </th>
                        <th scope="col">Employee Id</th>
                        <th scope="col">Employee Date of Birth</th>
                        <th scope="col">Employee Role</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($birthdays as $birthday)
                        <tr>
                            <td> {{ $birthday->ename }}</td>
                            <td>{{ $birthday->eid }}</td>
                            <td>{{ $birthday->edob }}</td>
                            <td>{{ $birthday->erole }}</td>
                            <td><a href="{{ route('employeeformpostedit', ['id' => $birthday]) }}"
                                    class="btn btn-success">Edit</a>
                            </td>
                            <td>


                                <form action="{{ route('employeeformpostdelete', ['id' => $birthday]) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <input type="submit" value="delete" class="btn btn-warning">
                                </form>




                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </header>

    @endauth
@endsection --}}
