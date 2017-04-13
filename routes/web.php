<?php

use Curl\MultiCurl;
use Illuminate\Http\Request;

$app->get('/', 'ExampleController@index');

$app->post('/post/{id}', ['middleware' => 'auth', function (Request $request, $id) {

    $start = microtime(true);
    $initialMem = memory_get_usage();
    /* Script comes here */

    $conn = mysqli_connect(
        env('DB_HOST'),
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        env('DB_DATABASE')
    );

    $result = mysqli_query($conn, "SELECT SourceURL FROM campaign_backlinks where campaign_id=1272 and recheck_nr=0 limit 25");
    $data = mysqli_fetch_all($result);
    $titles=[];


    // TEST MUlticurl
//    $mh = curl_multi_init();
//    foreach($data as $item) {
//        $ch = curl_init($item[0]);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//        curl_multi_add_handle($mh,$ch);
//    }
//
//    $running=null;
//    do {
//        $mrc = curl_multi_exec($mh, $active);
//        dd($mrc);
//    } while ($active>0);
    //просто запускаем все соединени
//    while( ($mrc = curl_multi_exec($mh, $running))==CURLM_CALL_MULTI_PERFORM );
//    while($running && $mrc == CURLM_OK){
//        if($running and curl_multi_select($mh)!=-1 ){
//            do{
//                $mrc = curl_multi_exec($mh, $running);
//                // если поток завершился
//                if( $info=curl_multi_info_read($mh) and $info['msg'] == CURLMSG_DONE ){
//                    $ch = $info['handle'];
//                    // смотрим http код который он вернул
////                    $status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
//                    // и собственно что он вернул
//                    $data=curl_multi_getcontent($info['handle']);
//                    dd(1);
//                    curl_multi_remove_handle($mh, $ch);
//                    curl_close($ch);
//                }
//            }while ($mrc == CURLM_CALL_MULTI_PERFORM);
//        }
//        usleep(100);
//    }
//    dd(1);




    /*TEST 2 MULTICURL Library*/
    // Requests in parallel with callback functions.
    $multi_curl = new MultiCurl();
    $multi_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);

    $multi_curl->success(function($instance) use (&$titles) {
//        echo 'call to "' . $instance->url . '" was successful.' . "\n";
//        echo 'response:' . "\n";
//        var_dump($instance->response);
        dd($instance->response);

        \phpQuery::newDocument($instance->response);
        $title = pq('title')->html();
        $titles[] = $title;
        \phpQuery::unloadDocuments();
    });
    $multi_curl->error(function($instance) {
//        echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
//        echo 'error code: ' . $instance->errorCode . "\n";
//        echo 'error message: ' . $instance->errorMessage . "\n";
    });
    $multi_curl->complete(function($instance) use ($titles) {
//        echo 'call completed' . "\n";
    });

    foreach($data as $item) {
        $multi_curl->addGet($item[0]);
    }

    $multi_curl->start(); // Blocks until all items in the queue have been processed.
    echo (memory_get_usage() - $initialMem)/1024 . " Kbytes";exit;
    dd($titles);


    /*END TEST 2*/



    foreach($data as $item) {
//        $headers = get_headers($item[0]);
//        if(substr($headers[0], 9, 3) != "200") continue;
//        $html = file_get_contents($item[0]);
//        \phpQuery::newDocument($html);
//        $title = pq('title')->html();
//        $titles[] = $title;
//        continue;

        /*======curl====*/
        $ch = curl_init($item[0]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $html = curl_exec($ch);
        \phpQuery::newDocument($html);
        $title = pq('title')->html();
        $titles[] = $title;
        // Close handle
        curl_close($ch);
        continue;
        $anchor_texts = [];

        foreach(pq('a') as $item) {
            $anchor_texts[] = [
                'text' => pq($item)->text(),
                'href' => pq($item)->attr('href')
            ];
        }

        dd($anchor_texts);

        dd(pq('a')->count());

        dd($title);
        \phpQuery::unloadDocuments();


    }

    dd($titles);


    $time = number_format(microtime(true) - $start, 7);
    echo "<p>$time seconds (<strong>" . number_format(1 / $time) . "</strong> per second)</p>";
    exit;

    $user = $request->user();
}]);


$app->get('/debug', function () use ($app) {

    $start = microtime(true);
    $conn = mysqli_connect(
        env('DB_HOST'),
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        env('DB_DATABASE')
    );
//    $data = \DB::getPdo()->query("SELECT * FROM users")->fetchAll(4);
    $result = mysqli_query($conn, "SELECT * FROM users");
    $data = mysqli_fetch_assoc($result);
    dd($data);
//    $users = $app->make('db')->select('SELECT * from users');
//    $data = \DB::select('SELECT * FROM users');
    $time = number_format(microtime(true) - $start, 7);
    echo "<p>$time seconds (<strong>" . number_format(1 / $time) . "</strong> per second)</p>";
    exit;
});
