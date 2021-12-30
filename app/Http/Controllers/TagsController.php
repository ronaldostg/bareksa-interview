<?php

namespace App\Http\Controllers;

use App\Models\Tags;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;


class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $cacheDataTags =  Redis::get('tags-data');
        // get data from redis
        if (isset($cacheDataTags)) {

            $dataTags = json_decode($cacheDataTags, FALSE);
            
            return response()->json([
                'message' => 'Mengambil data dari redis',
                'status' => 200,
                'data' => $dataTags
            ], 201);



        }else{
            $dataTags = Tags::all();


            Redis::set('tags-data', $dataTags, 'EX', 120);


            return response()->json([
                'message' => 'Mengambil data dari database',
                'status' => 200,
                'data' => $dataTags
            ], 201);
        }


        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $validator = Validator::make($request->all(), [
            'tags_name' => 'required|string',      
            'slug' => 'required|string|unique:tags',
        ]);


        $Data = array(
            'tags_name'=>$request->tags_name,
            'slug'=>$request->slug
        );

        if (Tags::create($Data)) {
            return response()->json([
                'message' => 'Tags Data has been added',
                'data' => $Data,
                'status' => 200

            ], 201);
        } else {
            return response()->json([
                'message' => 'Tags input your data',
                // 'data'=>$Data,
                'status' => 400

            ]);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tags  $tags
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

        $findData = 'tags-data:id:' . $id;

        $cacheDataTags=  Redis::get($findData);

        if (isset($cacheDataTags)) {
            $dataTags = json_decode($cacheDataTags, FALSE);

            return response()->json([
                'message' => 'Success to get tags data from redis ',
                'status' => '200',
                'data' => $dataTags

            ]);
        }else{
            $dataTags =  Tags::find($id);
            Redis::set('tags-data:id:' . $id, $dataTags, 'EX', 120);
            
            return response()->json([
                'message' => 'Success to get tags data from database ',
                'status' => '200',
                'data' => $dataTags
    
            ]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tags  $tags
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $rules = [
            'tags_name' => 'required|string',
            'slug' => 'required|string|unique:tags',
        ];

        $request->validate($rules);

        $validatedData['tags_name'] =  $request->tags_name;
        $validatedData['slug'] =  $request->slug;


        Redis::del('tags-data:id:' . $id);
        

        if(Tags::where('id', $id)->update($validatedData)){

            $tempData = json_encode($validatedData);
            
            Redis::set('tags-data:id:' . $id, $tempData);

            return response()->json([
                'message' => 'Success to update data ',
                'status' => 200,
                'data' => $validatedData
    
            ]);
        }else{
            return response()->json([
                'message' => 'Failed to update data ',
                'status' => 400
    
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tags  $tags
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        if ($id == null) {

            return response()->json([
                'message' => 'Please input your id ',
                'status' => 400
                

            ]);
        } else {

            Tags::destroy($id);

            // Menghapus data yang ada di Redis
            Redis::del('tags-data:id:' . $id);

            return response()->json([
                'message' => 'Success to delete tags data ',
                'status' => 201
                

            ]);
        }
        
    }
}
