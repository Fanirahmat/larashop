<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    public function __construct(){
        //OTORISASI GATE
        $this->middleware(function($request,$next){
            if (Gate::allows('manage-books'))
            {   
                return $next($request);
            } 
            else 
            {
                abort(403,'anda tidak memiliki cukup hak akses');
            }
        });
    }
    
    public function index(Request $request)
    {
        $filterStatus = $request->get('status');
        $keyword = $request->get('keyword');
        if ($filterStatus)
        {
            if ($keyword) 
            {
                $books = \App\Book::with('categories')->where('title',"LIKE","%$keyword%")->where('status', strtoupper($filterStatus))->paginate(10);
            } 
            else 
            {
                $books = \App\Book::with('categories')->where('status', strtoupper($filterStatus))->paginate(10);
            }
        } 
        else 
        {
            if ($keyword) 
            {
                $books = \App\Book::with('categories')->where('title',"LIKE","%$keyword%")->paginate(10);
            } 
            else 
            {
                $books = \App\Book::with('categories')->paginate(10);
            }
        }
        return view('books.index', ['books' => $books]);
    }

    
    public function create()
    {
       return view('books.create');
    }

    
    public function store(Request $request)
    {
        \Validator::make($request->all(),[
            "title" => "required|min:5|max:200",
            "description" => "required|min:20|max:1000",
            "author" => "required|min:3|max:100",
            "publisher" => "required|min:3|max:200",
            "price" => "required|digits_between:0,10",
            "stock" => "required|digits_between:0,10",
            "cover" => "required"
        ]);
        
        $new_book = new \App\Book;
        $new_book->title = $request->get('title');
        $new_book->description = $request->get('description');
        $new_book->author = $request->get('author');
        $new_book->publisher = $request->get('publisher');
        $new_book->price = $request->get('price');
        $new_book->stock = $request->get('stock');
        $new_book->cover = $request->file('cover')->store('book_covers','public');
        $new_book->slug = \Str::slug($request->get('title'));
        $new_book->created_by = \Auth::user()->id;
        $new_book->status = $request->get('save_action');
        $new_book->save();
        $new_book->categories()->attach($request->get('categories'));
        if ($request->get('save_action') == 'PUBLISH') 
        {
           return redirect()->route('books.create')->with('status', 'Book successfully saved and published');
        } 
        else 
        {
            return redirect()->route('books.create')->with('status', 'Book successfully saved as draft');
        }
        
    }

   
    public function show($id)
    {
        //
    }

    
    public function edit($id)
    {
        $book = \App\Book::findOrFail($id);
        return view('books.edit', ['book' => $book]);
    }

    
    public function update(Request $request, $id)
    {
        \Validator::make($request->all(), [
            "title" => "required|min:5|max:200",
            "description" => "required|min:20|max:1000",
            "author" => "required|min:3|max:100",
            "publisher" => "required|min:3|max:200",
            "price" => "required|digits_between:0,10",
            "stock" => "required|digits_between:0,10",
        ])->validate();

        $new_book = \App\Book::findOrFail($id);

        $new_book->title = $request->get('title');
        $new_book->description = $request->get('description');
        $new_book->author = $request->get('author');
        $new_book->publisher = $request->get('publisher');
        $new_book->price = $request->get('price');
        $new_book->stock = $request->get('stock');
        if (file_exists(storage_path('app/public/' . $new_book->cover))) 
        {
            \Storage::delete('public/'.$new_book->cover);
             $new_book->cover = $request->file('cover')->store('book_covers','public');
        } 
        else 
        {
            $new_book->cover = $request->file('cover')->store('book_covers','public');
        }
        $new_book->slug = \Str::slug($request->get('title'));
        $new_book->updated_by = \Auth::user()->id;
        $new_book->status =  $request->get('status');
        $new_book->save();
        $new_book->categories()->attach($request->get('categories'));
        return redirect()->route('books.edit', [$new_book->id])->with('status','Book successfully updated');

    }

    
    public function destroy($id)
    {
        $trash = \App\Book::findOrFail($id);
        $trash->delete();
        return redirect()->route('books.index')->with('status','Book successfully move to trash');
    }

    public function trash()
    {
        $books = \App\Book::onlyTrashed()->paginate(10);
        return view('books.trash', ['books' => $books]);
    }

    public function restore($id)
    {
        $book = \App\Book::withTrashed()->findOrFail($id);
        if ($book->trashed()) 
        {
            $book->restore();
            return redirect()->route('books.index')->with('status','Book successfully restored');
        } 
        else 
        {
            return redirect()->route('books.index')->with('status','Book not found');
        }
        
    }

    public function deletePermanent($id)
    {
        $book = \App\Book::withTrashed()->findOrFail($id);
        if ($book->trashed()) 
        {
            $book->categories()->detach();
            $book->forceDelete();
            return redirect()->route('books.trash')->with('status','Buku telah dihapus secara permanen');
        } 
        else 
        {
            return redirect()->route('books.trash')->with('status','Buku telah gagal dihapus secara permanen');
        }
    }
}
