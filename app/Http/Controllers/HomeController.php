<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Exception\AwsException;
use Aws\Polly\PollyClient;


class HomeController extends Controller
{
    /**
 * This code expects that you have AWS credentials set up per:
 * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html
 */

    // Create a PollyClient
    public function create(Request $request){

        $client = new PollyClient([
            'profile' => 'default',
            'version' => 'latest',
            'region' => 'us-east-1'
        ]);

        $text = ($request->text) ? $request->text : '';

        try {
            $options = [
                //'Engine' => 'standard',// standard | neural
                'OutputFormat' => 'mp3',
                'Text' => "Caixa 2 livre",
                'Text' => "<speak><prosody rate='slow'>".$text."</prosody></speak>",
                'TextType' => 'ssml',
                'LanguageCode' => "pt-BR",
                'VoiceId' => "Camila", // Camila | Vitória/Vitoria | Ricardo
            ];
            
            $result = $client->synthesizeSpeech($options);
            
            $audioContent = $result->get('AudioStream')->getContents();
            $path = public_path('records/audio.mp3');
            $fp = fopen($path, "w+");
            fputs($fp, $audioContent);
            fclose($fp);
            
            // Listening the text
            // $size   = strlen($resultData); // File size
            // $length = $size;           // Content length
            // $start  = 0;               // Start byte
            // $end    = $size - 1;       // End byte
            // header('Content-Transfer-Encoding:chunked');
            // header("Content-Type: audio/mpeg");
            // header("Accept-Ranges: 0-$length");
            // header("Content-Range: bytes $start-$end/$size");
            // header("Content-Length: $length");
            // echo $resultData;
            
            
            // // Download the Text to Speech in MP3 Format
            
            // header('Content-length: ' . strlen($resultData));
            // header('Content-Disposition: attachment; filename="./myfile.mp3"');
            // header('X-Pad: avoid browser bug');
            // header('Cache-Control: no-cache');
            // echo $resultData;
            
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage();
            echo "\n";
        }

    }

    public function create2(){

        $client = new PollyClient([
            'profile' => 'default',
            'version' => '2016-06-10',
            'region' => 'us-east-2'
        ]);

        $text = 'This is a sample text to be synthesized.';
        $format = 'mp3'; //json|mp3|ogg_vorbis|pcm
        $S3Bucket = 'sgf-records';
        $voice = 'Joanna';

        try {
            $result = $client->synthesizeSpeech([
                'Text' => $text,
                'OutputFormat' => $format,
                'OutputS3BucketName' => $S3Bucket,
                'VoiceId' => $voice,
            ]);
            var_dump($result);
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage();
            echo "\n";
        }

    }

}
