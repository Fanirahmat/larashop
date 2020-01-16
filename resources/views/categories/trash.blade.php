@extends('layouts.global')
@section('title') Category Trashed @endsection
@section('content')
<h1>Category List Trashed</h1>

<div class="row">
    <div class="col-md-6">
        <form action="{{route('categories.index')}}">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Filter by category name" value="{{Request::get('name')}}" name="name">
                <div class="input-group-append">
                    <input type="submit" value="Filter" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>
       
    <div class="col-md-6">
        <ul class="nav nav-pills card-header-pills">
            <li class="nav-item">
                <a class="nav-link" href=" {{route('categories.index')}}">Published</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href=" {{route('categories.trash')}}">Trash</a>
            </li>
        </ul>
    </div>
       
</div>

<hr class="my-3">

<div class="row">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><b>Name</b></th>
                <th><b>Slug</b></th>
                <th><b>Image</b></th>
                <th><b>Action</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($category as $c)
                <tr>
                    <td>{{$c->name}}</td>
                    <td>{{$c->slug}}</td>
                    <td>
                        @if($c->image)
                        <img src="{{asset('storage/'.$c->image)}}" width="70px"/>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-success text-white btn-sm" href="{{route('categories.restore', [$c->id])}}">Restore</a>
                        <form onsubmit="return confirm('Hapus Permamen ?')" class="d-inline" action="{{route('categories.deletePermanent', [$c->id])}}" method="POST">
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
                <td colspan=10>
                    {{$category->appends(Request::all())->links()}}
                </td>
            </tr>
        </tfoot>       
    </table>
</div>

   @if(session('status'))
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning">
                {{session('status')}}
            </div>
        </div>
    </div>
  @endif
  <br>
  

@endsection
