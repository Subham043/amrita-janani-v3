<?php

namespace App\Modules\Enquiries\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enquiries\Models\Enquiry;
use App\Modules\Enquiries\Requests\EnquiryMultipleSelectionRequest;
use Illuminate\Support\Facades\DB;

class EnquiryMultiDeleteController extends Controller
{

    
    public function index(EnquiryMultipleSelectionRequest $request){
        $request->validated();
        $ids = $request->enquiries;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one enquiry to toggle status."], 400);
        }

        DB::beginTransaction();

        try {
            //code...
            Enquiry::whereIn('id', $ids)->forceDelete();
            return response()->json(["message"=>"Enquiry deleted successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

}
