<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Aws\Polly\PollyClient;

class PollyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function create(Request $request)    
    {
        $token_api = env('APP_API_TOKEN');
        if($request->token != $token_api){
            echo 'Token inválido';
            return false;
        }

        /**
         * This code expects that you have AWS credentials set up per:
         * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html
         */

        // Create a PollyClient
        $client = new PollyClient([
            // 'profile' => 'default',
            'version' => 'latest',
            'region' => 'us-east-1', //Region Compatibility: https://docs.aws.amazon.com/polly/latest/dg/NTTS-main.html#ntts-regions
            'credentials' => [
                'key' => env('AWS_POLLY_KEY'),
                'secret' => env('AWS_POLLY_SECRET'),
            ]
        ]);


        // Params & defaults
        $text = ($request->text) ? $request->text : '';
        $speed = ($request->speed) ? $request->speed : 'slow'; //x-slow, slow, medium, fast,x-fast
        $output = ($request->output) ? $request->output : 'mp3'; //json | mp3 | ogg_vorbis | pcm
        $voice = ($request->voice) ? $request->voice : 'Camila'; // All voices: https://docs.aws.amazon.com/polly/latest/dg/voicelist.html
        $language = ($request->language) ? $request->language : 'pt-BR'; // arb | cmn-CN | cy-GB | da-DK | de-DE | en-AU | en-GB | en-GB-WLS | en-IN | en-US | es-ES | es-MX | es-US | fr-CA | fr-FR | is-IS | it-IT | ja-JP | hi-IN | ko-KR | nb-NO | nl-NL | pl-PL | pt-BR | pt-PT | ro-RO | ru-RU | sv-SE | tr-TR
        $engine = ($request->engine) ? $request->engine : 'standard'; // standard | neural


        //Polly Options | All options: https://docs.aws.amazon.com/polly/latest/dg/API_SynthesizeSpeech.html
        try {
            $options = [
                'Engine' => $engine,
                'OutputFormat' => $output, 
                'Text' => '<speak><prosody rate="'.$speed.'">'.$text.'</prosody></speak>', //More tags: https://docs.aws.amazon.com/polly/latest/dg/supportedtags.html
                'TextType' => 'ssml',
                'LanguageCode' => $language,
                'VoiceId' => $voice,
            ];
            
            $result = $client->synthesizeSpeech($options);
            
            $audioContent = $result->get('AudioStream')->getContents();
            $path = public_path('records/audio.mp3');
            $fp = fopen($path, "w+");
            fputs($fp, $audioContent);
            fclose($fp);
            
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage();
            echo "\n";
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
