<?php

namespace App\Http\Controllers\novel_frontend;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Validator;
use DB; 
use Storage;
class bookController extends Controller
{
    public function book($name)
    {  

        $title = str_replace('-', ' ', $name);
        $code = DB::table('d_novel')
                ->where('dn_title',$title)
                ->first();
        $novel = DB::table('d_novel')
                ->where('dn_created_by','=',$code->dn_created_by)
                ->where('dn_title','!=',$title)
                ->get();
        $book = DB::table('d_novel')
                ->join('users','users.id','=','d_novel.dn_created_by')
                ->where('dn_id',$code->dn_id)
                ->first();
        $q_total_book = DB::table('d_novel')
                ->get();
        $total_book = count($q_total_book);
        $chapter = DB::table('d_novel_chapter')
                ->where('dnch_ref_id',$code->dn_id)
                ->get();
        $tags = DB::table('d_novel_tags')
                ->where('dnt_ref_id',$code->dn_id)
                ->get();
        
        $novel_rate = DB::table('d_novel_rate')
                ->select('d_novel_rate.*','name','id','u_image')
                ->join('users','users.id','=','d_novel_rate.dr_rated_by')
                ->where('dr_ref_id',$code->dn_id)
                ->get();
        // return response()->json(['chapter'=>$chapter,'book'=>$book,'tags'=>$tags,'code'=>$code,'novel'=>$novel]);
        return view('novel_frontend.detail_novel.detail_novel',compact('book','chapter','tags','novel','total_book','novel_rate'));
    }
    public function novel_rate_star(Request $request)
    {
        // dd(Auth::user());
        // dd($request->all());
        $check = DB::table('d_novel_rate')
                ->where('dr_ref_id',$request->id)
                ->where('dr_rated_by','=',Auth::user()->id)
                ->get();

        $sum_check = count($check); 

        if ($sum_check > 0) {
            $check = DB::table('d_novel_rate')
                    ->where('dr_ref_id',$request->id)
                    ->where('dr_rated_by','=',Auth::user()->id)
                    ->update([
                        'dr_rate'=>$request->rate,
                        'dr_message'=>$request->message,
                        'dr_updated_at'=>date('Y-m-d h:i:s'),
                    ]);
            // $delete = DB::table('d_novel_rate')
            //         ->where('dr_ref_id',$request->id)
            //         ->where('dr_rated_by','=',Auth::user()->id)
            //         ->delete();

            // $insert = DB::table('d_novel_rate')
            //         ->insert([
            //             'dr_rate'=>$request->rate,
            //             'dr_message'=>$request->message,
            //             'dr_ref_id'=>$request->id,
            //             'dr_rated_by'=>Auth::user()->id,
            //             'dr_created_at'=>date('Y-m-d h:i:s'),
            //         ]);

        }else{
            $insert = DB::table('d_novel_rate')
                    ->insert([
                        'dr_rate'=>$request->rate,
                        'dr_message'=>$request->message,
                        'dr_ref_id'=>$request->id,
                        'dr_rated_by'=>Auth::user()->id,
                        'dr_created_at'=>date('Y-m-d h:i:s'),
                    ]);
        }
        return $check;
    }
}
