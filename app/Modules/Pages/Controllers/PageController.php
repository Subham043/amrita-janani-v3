<?php

namespace App\Modules\Pages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Pages\Models\PageContentModel;
use App\Modules\Pages\Requests\PageContentCreateRequest;
use App\Modules\Pages\Requests\PageContentUpdateRequest;
use App\Modules\Pages\Requests\PageCreateRequest;
use App\Modules\Pages\Requests\PageUpdateRequest;
use App\Modules\Pages\Services\PageService;
use App\Modules\Pages\Services\PageContentService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function __construct(private PageService $pageService, private PageContentService $pageContentService){}

    public function home_page(Request $request){
        $page = $this->pageService->getHomePage();
        $page_content = $this->pageContentService->paginate($page->id, $request->total ?? 10);
        return view('pages.admin.page_content.home')
        ->with([
            'page_detail' => $page,
            'page_content_detail' => $page_content,
            'page_name' => 'Home',
        ]);
    }
    
    public function about_page(Request $request){
        $page = $this->pageService->getAboutPage();
        $page_content = $this->pageContentService->paginate($page->id, $request->total ?? 10);
        return view('pages.admin.page_content.home')
        ->with([
            'page_detail' => $page,
            'page_content_detail' => $page_content,
            'page_name' => 'About',
        ]);
    }

    public function edit_dynamic_page($id){
        $page = $this->pageService->getById($id);
        $page_content = $this->pageContentService->paginate($page->id, $request->total ?? 10);
        return view('pages.admin.page_content.edit')
        ->with([
            'page_detail' => $page,
            'page_content_detail' => $page_content,
            'page_name' => $page->page_name,
        ]);
    }

    public function getPageContent(Request $req){
        $rules = array(
            'id' => ['required','string', 'exists:page_contents,id'],
        );

        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return response()->json(["errors"=>$validator->errors()], 400);
        }
        return response()->json(['data'=>$this->pageContentService->getById($req->id)], 200);
    }

    public function dynamic_page_list(Request $request){
        $data = $this->pageService->paginate($request->total ?? 10);
        return view('pages.admin.page_content.list')->with('data', $data);
    }

    public function storePage(PageCreateRequest $req){

        DB::beginTransaction();
        try {
            //code...
            $data = $this->pageService->create([
                ...$req->validated(),
                'url' => str()->slug($req->url),
                'user_id' => Auth::guard('admin')->user()->id,
            ]);
            return response()->json(["url"=>empty($req->refreshUrl)?route('edit_dynamic_page', $data->id):$req->refreshUrl, "message" => "Data updated successfully.", "data" => $data], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

    public function updatePage(PageUpdateRequest $request, $id){

        $data = $this->pageService->getById($id);
        DB::beginTransaction();
        try {
            //code...
            $this->pageService->update([
                ...$request->validated(),
                'url' => str()->slug($request->url),
            ], $data);
            return response()->json(["url"=>empty($req->refreshUrl)?URL::previous():$request->refreshUrl, "message" => "Data updated successfully.", "data" => $data], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

    public function deletePage($id){
        $data = $this->pageService->getById($id);
        $this->pageService->forceDelete($data);
        return redirect()->back()->with('success_status', 'Data Deleted permanently.');
    }

    public function storePageContent(PageContentCreateRequest $req) {

        DB::beginTransaction();
        try {
            //code...
            $data = $this->pageContentService->create([
                ...$req->safe()->except(['image_position', 'image']),
            ]);

            if($req->hasFile('image') && $req->file('image')->isValid()){
                $data->image = (new FileService)->save_public_image('image', (new PageContentModel)->file_path);
                $data->image_position = $req->image_position;
                $data->save();
                $data->refresh();
            }
            return response()->json(["url"=>empty($req->refreshUrl)?URL::previous():$req->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

    public function updatePageContent(PageContentUpdateRequest $req) {

        $data = $this->pageContentService->getById($req->id);

        DB::beginTransaction();
        try {
            //code...
            $this->pageContentService->update([
                ...$req->safe()->except(['id', 'image']),
            ], $data);

            if($req->hasFile('image') && $req->file('image')->isValid()){
                if($data->image){
                    (new FileService)->remove_file($data->image, ('app/public/'.(new PageContentModel)->file_path));
                    (new FileService)->remove_file('compressed-'.$data->image, ('app/public/'.(new PageContentModel)->file_path));
                }
                $data->image = (new FileService)->save_public_image('image', (new PageContentModel)->file_path);
                $data->save();
                $data->refresh();
            }
            return response()->json(["url"=>empty($req->refreshUrl)?URL::previous():$req->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

    public function deletePageContent($id){
        $data = $this->pageContentService->getById($id);
        if($data->image){
            (new FileService)->remove_file($data->image, ('app/public/'.(new PageContentModel)->file_path));
            (new FileService)->remove_file('compressed-'.$data->image, ('app/public/'.(new PageContentModel)->file_path));
        }
        $data->forceDelete();
        return redirect()->intended(URL::previous())->with('success_status', 'Data Deleted permanently.');
    }
}
