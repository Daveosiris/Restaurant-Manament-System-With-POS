<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Language;
use App\Models\Pcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PsubCategory;
use Illuminate\Support\Facades\Session;

class ProductSubCategory extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();
        $lang_id = $lang->id;
        $data['categories'] = Pcategory::where('language_id', $lang_id)->orderBy('id', 'DESC')->paginate(10);
        $data['psubcategories'] = PsubCategory::where('language_id', $lang_id)->orderBy('id', 'DESC')->paginate(10);

        $data['lang_id'] = $lang_id;
        return view('admin.product.subcategory.index', $data);
    }


    public function store(Request $request)
    {
        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $rules = [
            'language_id' => 'required',
            'category_id' => 'required',
            'name' => 'required|max:255',
            'status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
        $input['slug'] =  make_slug($request->name);
        $data = new PsubCategory();
        $input = $request->all();
        $data->create($input);

        Session::flash('success', 'Sub Category added successfully!');
        return "success";
    }


    public function edit($id)
    {
        $lang = Language::where('code', request('language'))->first();
        $data['data'] = PsubCategory::findOrFail($id);
        $data['categories'] = Pcategory::where('language_id', $lang->id)->orderBy('id', 'DESC')->paginate(10);
        return view('admin.product.subcategory.edit', $data);
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'category_id' => 'required|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
        $data = PsubCategory::findOrFail($request->subcategory_id);
        $input = $request->all();
        $input['slug'] =  make_slug($request->name);
        $data->update($input);

        Session::flash('success', 'Sub Category Update successfully!');
        return "success";
    }

    public function delete(Request $request)
    {
        $category = PsubCategory::findOrFail($request->subcategory_id);
        if ($category->products()->count() > 0) {
            Session::flash('warning', 'First, delete all the product under the selected sub categories!');
            return back();
        }
        $category->delete();

        Session::flash('success', 'Sub Category deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $pcategory = PsubCategory::findOrFail($id);
            if ($pcategory->products()->count() > 0) {
                Session::flash('warning', 'First, delete all the product under the selected sub categories!');
                return "success";
            }
            $pcategory->delete();
        }
        Session::flash('success', 'product sub categories deleted successfully!');
        return "success";
    }

    public function FeatureCheck(Request $request)
    {
        $id = $request->subcategory_id;
        $value = $request->feature;

        $pcategory = PsubCategory::findOrFail($id);
        $pcategory->is_feature = $value;
        $pcategory->save();

        Session::flash('success', 'Product subcategory updated successfully!');
        return back();
    }
}
