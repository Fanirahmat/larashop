@extends('layouts.global')
@section('title') Book Trash List @endsection
@section('content')

<h1>Book Trash List</h1>
<div class="row">
    <div class="col-md-6">

    </div>
    <div class="col-md-6">
        <ul class="nav nav-pills card-header-pills">
            <li class="nav-item">
                <a class="nav-link {{Request::get('status') == NULL && Request::path() == 'books' ? 'active' : ''}}" href=" {{route('books.index')}}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Request::get('status') == 'publish' ? 'active' : '' }}" href=" {{route('books.index', ['status' => 'publish'])}}">Publish</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Request::get('status') == 'draft' ? 'active' : '' }}" href=" {{route('books.index', ['status' => 'draft'])}}">draft</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Request::path() == 'books/trash' ? 'active' : ''}}" href=" {{route('books.trash')}}">Trash</a>
            </li>
        </ul>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        @if (session('status'))
            <div class="alert alert-warning">
                {{session('status')}}
            </div>
        @endif
        <table class="table table-bordered table-stripped">
            <thead>
                <tr>
                    <th><b>Cover</b></th>
                    <th><b>Title</b></th>
                    <th><b>Author</b></th>
                    <th><b>Status</b></th>
                    <th><b>Categories</b></th>
                    <th><b>Stock</b></th>
                    <th><b>Price</b></th>
                    <th><b>Action</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($books as $book)
                    <tr>
                        <td>
                            @if($book->cover)
                                <img src="{{asset('storage/' . $book->cover)}}" width="96px"/>
                            @endif
                        </td>
                        <td>{{$book->title}}</td>
                        <td>{{$book->author}}</td>
                        <td>
                            @if($book->status == "DRAFT")
                                <span class="badge bg-dark text-white">{{$book->status}}</span>
                            @else
                                <span class="badge badge-success">{{$book->status}}</span>
                            @endif
                        </td>
                        <td>
                            <ul class="pl-3">
                                @foreach($book->categories as $category)
                                    <li>{{$category->name}}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{$book->stock}}</td>
                        <td>{{$book->price}}</td>
                        <td>
                            <a class="btn btn-success text-white btn-sm" href="{{route('books.restore', [$book->id])}}">Restore</a>
                            <form onsubmit="return confirm('Hapus Permamen ?')" class="d-inline" action="{{route('books.deletePermanent', [$book->id])}}" method="POST">
                                @csrf
                                    <input type="hidden" name="_method" value="DELETE"> 
                                    <input type="submit" value="Delete" class="btn btn-danger btn-sm">
                             </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10">
                        {{$books->appends(Request::all())->links()}}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection