<?php

use App\Jobs\ExampleJob;
use Curl\MultiCurl;
use Illuminate\Http\Request;

//$app->get('/', 'ExampleController@index');
$app->get('/', function() {

//    ini_set('display_errors', 1);
//    restore_error_handler();
//
//    echo $as;
    return 'Hello dude';
});

$app->get('job', function() {
    dispatch(new ExampleJob);
});

$app->post('/post/{id}', ['middleware' => 'auth', function (Request $request, $id) {

    $start = microtime(true);
    $initialMem = memory_get_usage();
    /* Script comes here */
//    $conn = mysqli_connect(
//        env('DB_HOST'),
//        env('DB_USERNAME'),
//        env('DB_PASSWORD'),
//        env('DB_DATABASE')
//    );
//
//    $result = mysqli_query($conn, "SELECT SourceURL FROM campaign_backlinks where campaign_id=1353 and recheck_nr=0 order by id desc limit 77");
//    $data = mysqli_fetch_all($result);
//    $titles=[];
    $anchor_texts = [];
//    $codes = [];
//    dd($data);
//        $ch = curl_init('http://topsape.ru/r=xk2?http://stasva.com/8-prichin-zavesti-blog/');
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
//        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
//        $html = curl_exec($ch);
//        dd($html);

//    $ch = curl_init('http://topsape.ru/reader/blog/1517/');
    $ch = curl_init('https://yandex.ru/pogoda/kishinev');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
//    curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
    $html = curl_exec($ch);
    $saw = new nokogiri($html);
    foreach($saw->get('a')->toDom()->getElementsByTagName('a') as $a) {
        $anchor_texts[] = [
            'text' => $a->textContent,
            'href' => $a->getAttribute('href')
        ];
    }
    dd($anchor_texts);

//    foreach($data as $item) {
//        /*======curl====*/
//        $ch = curl_init($item[0]);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
//        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//        $html = curl_exec($ch);
////        $saw = new nokogiri($html);
////        foreach($saw->get('a')->toDom()->getElementsByTagName('a') as $a) {
////            $anchor_texts[] = [
////                'text' => $a->textContent,
////                'href' => $a->getAttribute('href')
////            ];
////        }
//        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        $anchor_texts[] = $httpcode;
//        curl_close($ch);
//    }
//    dd($anchor_texts);
//    die(count($anchor_texts));

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
    $multi_curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);
    $multi_curl->setOpt(CURLOPT_RETURNTRANSFER, true);

    $multi_curl->success(function($instance) use (&$titles, &$anchor_texts) {
        $saw = new nokogiri($instance->response);
//        $title = $saw->get('title')->toText();
//        $titles[] = $title;
        foreach($saw->get('a')->toDom()->getElementsByTagName('a') as $item) {
            $anchor_texts[] = [
                'text' => $item->textContent,
                'href' => $item->getAttribute('href')
            ];
        }
    });
    $multi_curl->error(function($instance) {
//        dd($instance->errorCode);
//        echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
//        echo 'error code: ' . $instance->errorCode . "\n";
//        echo 'error message: ' . $instance->errorMessage . "\n";
    });
    $multi_curl->complete(function($instance) use ($titles, &$codes) {
        $httpcode = curl_getinfo($instance->curl, CURLINFO_HTTP_CODE);
        $codes[] = [$instance->url, $httpcode];
//        dd($httpcode);
//        echo 'call completed' . "\n";
    });

    foreach($data as $item) {
        $multi_curl->addGet($item[0]);
    }
    //1

    $multi_curl->start(); // Blocks until all items in the queue have been processed.
//    echo (memory_get_usage() - $initialMem)/1024 . " Kbytes";exit;
    dd($codes);
    dd($anchor_texts);


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
//    $start = microtime(true);
//    $conn = mysqli_connect(
//        env('DB_HOST'),
//        env('DB_USERNAME'),
//        env('DB_PASSWORD'),
//        env('DB_DATABASE')
//    );
//    $result = mysqli_query($conn, "SELECT * FROM users");
//    $data = mysqli_fetch_assoc($result);
//    $time = number_format((microtime(true) - $start), 7);
//    echo "<p>$time seconds (<strong>" . number_format(1 / $time) . "</strong> per second)</p>";
//    echo $time;
//    exit;
//    dd($data);
//    $users = $app->make('db')->select('SELECT * from users');
//    $data = \DB::select('SELECT * FROM users');

    $start = microtime(true);
    $initialMem = memory_get_usage();
    $ch = curl_init('http://ihtik.lib.ru/2011.07_ihtik_hudlit-ru/');//http://ihtik.lib.ru/2011.07_ihtik_hudlit-ru/
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_TIMEOUT, 3);

    $html = curl_exec($ch);
//    $html = file_get_contents('https://yandex.ru/pogoda/kishinev');


//    \phpQuery::newDocument($html);
//    $title = pq('title')->html();
//    $anchor_texts = [];
//    foreach(pq('a') as $item) {
//        $anchor_texts[] = [
//            'text' => pq($item)->text(),
//            'href' => pq($item)->attr('href')
//        ];
//    }

//    dd($anchor_texts);

//    dd(pq('a')->count());
//
//    dd($title);
//    \phpQuery::unloadDocuments();


    $saw = new nokogiri($html);
    $title = $saw->get('title')->toText();
    $anchor_texts = [];
    foreach($saw->get('a')->toDom()->getElementsByTagName('a') as $item) {
        $anchor_texts[] = [
            'text' => $item->textContent,
            'href' => $item->getAttribute('href')
        ];
    }
    print_r($anchor_texts);
//    foreach($saw->get('a') as $item) {
//        $anchor_texts[] = [
////            'text' => $item['#text'],
//            'href' => $item['href']
//        ];
//    }
//    $i=0;
//    foreach($saw->get('a')->toTextArray() as $item) {
//        $anchor_texts[$i++]['text'] = $item;
//    }

    $time = number_format(microtime(true) - $start, 7);
    echo "<p>$time seconds (<strong>" . number_format(1 / $time) . "</strong> per second)</p>";
    echo (memory_get_usage() - $initialMem)/1024 . " Kbytes";exit;
});

$app->get('memory', function() {
    echo 'Memory in use: ' . memory_get_usage() . ' ('. ((memory_get_usage() / 1024) / 1024) .'M) <br>';
    echo 'Peak usage: ' . memory_get_peak_usage() . ' ('. ((memory_get_peak_usage() / 1024) / 1024) .'M) <br>';
    echo 'Memory limit: ' . ini_get('memory_limit') . '<br>';
    $initialMem = memory_get_usage();
//    function asd() {
//        echo 123123;
//    }
//    asd();
//    exit;
//    $html = file_get_contents('/home/user/sites/linkquidator-master/storage/app/majestic/2017/03/14/GetBackLinkData-3926-19_34_05.json');//https://yandex.ru/pogoda/kishinev
//    $html = fopen('/home/user/sites/linkquidator-master/storage/app/majestic/2017/03/14/GetBackLinkData-3926-19_34_05.json', 'r');//https://yandex.ru/pogoda/kishinev
//    $contents = '';
//    $buffer = fgets($html, 4096);
//    echo $buffer;
//    while (!feof($html)) {
//        $contents .= fread($html, 8192);
//    }
//    fclose($html);
    foreach(nums() as $key => $item) {
//        dd(json_decode(mb_substr($item, 0, strpos($item, '}')+1)));
//        if($key == 3294) {
//            dd($item);
//            dd(mb_substr($item, 0, strpos($item, '}')+1));
//
//
//
//        }
        $var = json_decode(mb_substr($item, 0, strpos($item, '}')+1));
        if(!$var) continue;
//        dd($var);
//        dd(mb_substr($item, 0, -2));
        echo $key.'<br>';
        echo $var->SourceURL.'<br>';
//        dd($var->SourceURL);

//        return;
    }


//    $t = fread($html, 1024);
//    dd(strlen($contents));
//    $saw = new nokogiri($html);
//    $title = $saw->get('title')->toText();
//    $anchor_texts = [];
////        echo $asd;
//    print_r($title);
    echo "<br>";
    echo (memory_get_usage() - $initialMem)/1024 . " Kbytes";exit;

    echo 'Memory in use: ' . memory_get_usage() . ' ('. ((memory_get_usage() / 1024) / 1024) .'M) <br>';
    echo 'Peak usage: ' . memory_get_peak_usage() . ' ('. ((memory_get_peak_usage() / 1024) / 1024) .'M) <br>';
    exit;
});

function nums() {
    $html = fopen('/home/user/sites/linkquidator-master/storage/app/majestic/2017/03/14/GetBackLinkData-3926-19_34_05.json', 'r');//https://yandex.ru/pogoda/kishinev
//    while (($line = fgets($html)) !== false) {
//        yield 'key' => $line;
//    }
    $obj = '';
    for($i=0,$j=0,$obj_c=0; ($line = fgets($html)) !== false; $i++) {
        if($i < 32) continue;
//        dd(strpos($line, 'DomainsInfo'));
        if(strpos($line, 'DomainsInfo') !== false) return;
        $j++;
        $obj .= $line;
        if($j === 33) {
            yield $obj_c++ => $obj;
            $obj = '';
            $j=0;
        }
//        yield $i => $line;

    }


    fclose($html);
}

