<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use App\Category;

class CategoryController extends Controller
{
    public function __construct(){
        //OTORISASI GATE
        $this->middleware(function($request,$next){
            if (Gate::allows('manage-categories'))
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
        //$category = Category::paginate(10);
        $filterName = $request->get('name');
        if ($filterName) 
        {
            $category = Category::where('name', 'LIKE' , "%$filterName%")->paginate(10);
        }     
        else
        {
            $category = Category::paginate(10);
        }
        return view('categories.index', compact('category'));
    }

   
    public function create()
    {
        
        return view('categories.create');
    }

    
    public function store(Request $request)
    {
        \Validator::make($request->all(),[
            "name" => "required|min:3|max:20",
            "image" => "required"
        ]);

        $ctgr = new Category;
        $ctgr->name = $request->name;
        $ctgr->image = $request->file('image')->store('category_images', 'public');
        $ctgr->slug = \Str::slug($request->name, '-');
        $ctgr->created_by = \Auth::user()->id;
        $ctgr->save();
        return redirect()->route('categories.create')->with('status', 'Category successfully created');
    }

    public function show($id)
    {
        //
    }

   
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit',compact('category'));
    }

    
    public function update(Request $request, $id)
    {
        \Validator::make($request->all(),[
            "name" => "required|min:3|max:20",
            "image" => "required"
        ]);
        
        $category = Category::findOrFail($id);
        $category->name = $request->get('name');
        $category->slug = \Str::slug($request->name);
        $category->updated_by = \Auth::user()->id;
        if(file_exists(storage_path('app/public/' . $category->image)))
        {
            \Storage::delete('public/'.$category->image);
            $file = $request->file('image')->store('category_images', 'public');
            $category->image = $file;
        }
        else
        {
            $file = $request->file('image')->store('category_images', 'public');
            $category->image = $file;
        }
        $category->save();
        return redirect()->route('categories.edit', [$id])->with('status', 'Category successfully updated');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->route('categories.index')->with('status', 'Category successfully moved to trash');

    }

    public function trash()
    {
        $category = Category::onlyTrashed()->paginate(10);
        return view('categories.trash', compact('category'));
    }

    public function restore($id)
    {
        $category = Category::withTrashed()->findOrfail($id);
        if ($category->trashed()) 
        {
            $category->restore();
            return redirect()->route('categories.index')->with('status', 'Category successfully restored');  
        } 
        else 
        {
            return redirect()->route('categories.index')->with('status', 'Category is not in trash');
        }
 
    }

    public function deletePermanent($id)
    {
        $category = Category::withTrashed()->findOrfail($id);
        if (!$category->trashed()) 
        {
            return redirect()->route('categories.index')->with('status', 'Can not delete permanent active category');
        } 
        else 
        {
            $category->forceDelete();
            return redirect()->route('categories.index')->with('status', 'Category permanently deleted');
        }
        
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->get('q'); 
        $categories = \App\Category::where("name","LIKE","%$keyword%")->get();
        return $categories;
    }
}
