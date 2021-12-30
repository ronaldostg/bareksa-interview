<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;


class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $filterNews = '';

        // filter data by status
        if (request('status')) {

            $filterNews = request('status') ? ':status:' . request('status') : '';
        }

        // filter data by topics
        if (request('topics')) {

            $filterNews = request('topics') ? ':topics:' . request('topics') : '';
        }
         
        // make keyword for keys data in redis
        $findData = "news-data" . $filterNews;

        // find data from redis with keys
        $cacheDataNews =  Redis::get($findData);

        // get data from redis
        if (isset($cacheDataNews)) {

            $news = json_decode($cacheDataNews, FALSE);

            // response when data saved in redis
            return response()->json([
                'message' => 'Mengambil data dari redis',
                'status' => 200,
                'data' => $news
            ], 201);
        }

        // get data from database and save to redis
        else {

            $dataNews = News::all();

            // load data from database by status 
            if (request('status')) {
                $dataNews = News::where('status', '=', request('status'))->get();
            }
            // load data from database by topics
            if (request('topics')) {

                $dataNews = News::where('topics_id', '=', request('topics'))->get();
            }

            // set new key and store data in redis
            Redis::set('news-data' . $filterNews, $dataNews, 'EX', 120);

            // response when data saved in database
            return response()->json([
                'message' => 'Mengambil data dari database',
                'status' => '200',
                // 'news-status'=>request('status'),
                'news' => $dataNews
            ], 201);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function show(News $news)
    {
        

        $findData = 'news-data:id:' . $news->id;

        $cacheDataNews =  Redis::get($findData);

        if (isset($cacheDataNews)) {

            $dataNews = json_decode($cacheDataNews, FALSE);

            return response()->json([
                'message' => 'Success to get data from redis ',
                'status' => '200',
                'data' => $dataNews->tags

            ]);
        } else {

            $dataNews =  News::find($news->id);
            Redis::set('news-data:id:' . $news->id, $dataNews, 'EX', 120);

            $dataNews->tags = 1;
            return response()->json([
                'message' => 'Success to get data from database ',
                'status' => '200',
                'data' => $dataNews->tags

            ]);
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

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'tags' => 'required',
            'news_text' => 'required|string',
            // 'topics_id'=>'required|string',
            'status' => 'required|string',
            // 'topics_id' => 'required|string',

        ]);

        $mergingTags = explode(",", $request->tags);
        $tempTags = json_encode($mergingTags);


        $Data = array(
            'title' => $request->title,
            'tags' => $tempTags,
            'news_text' => $request->news_text,
            'topics_id' => $request->topics_id,
            'status' => 'publish'
        );


        if (News::create($Data)) {
            return response()->json([
                'message' => 'News Data has been added',
                'data' => $Data,
                'status' => 200

            ], 201);
        } else {
            return response()->json([
                'message' => 'Please input your data',
                // 'data'=>$Data,
                'status' => 400

            ]);
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, News $news)

    {
        //



        $rules = [
            'title' => 'required|string',
            'tags' => 'required',
            'news_text' => 'required|string',
            // 'topics_id'=>'required|string',
            'status' => 'required|string',
        ];

        $request->validate($rules);
 
        // $mergingTags = explode(",", $request->tags);


        $mergingTags = explode(",", $request->tags);
        $tempTags = json_encode($mergingTags);


        $validatedData['title'] =  $request->title;
        $validatedData['tags'] =  $tempTags;
        $validatedData['news_text'] =  $request->news_text;
        $validatedData['status'] =  $request->status;

        Redis::del('news-data:id:' . $news->id);

        // var_dump($validatedData);

        if (News::where('id', $news->id)->update($validatedData)) {

            $tempData = json_encode($validatedData);
            Redis::set('news-data:id:' . $news->id, $tempData );

            return response()->json([
                'message' => 'Success to update data ',
                'data' => $validatedData

            ]);
        } else {
            return response()->json([
                'message' => 'Failed to update data ',
                'status' => 400
                // 'news_id'=>

            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(News $news)
    {
        //

        if ($news->id == null) {

            return response()->json([
                'message' => 'Please input your id ',
                'status' => 400
                // 'news_id'=>

            ]);
        } else {

            News::destroy($news->id);

            // Menghapus data yang ada di Redis
            Redis::del('news-data:id:' . $news->id);

            return response()->json([
                'message' => 'Success to delete data ',
                'status' => 201
                // 'news_id'=>

            ]);
        }
    }

    
}
