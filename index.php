<?php
define('API_KEY','1180176060:AAGJJjbPmvOmqXcjN3QfVrNWc6rtZq9caGk');

function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}


$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$cid = $message->chat->id;
$tx = $message->text;



if($tx=="/start"){

    $get = json_decode(file_get_contents("https://coronavirus-19-api.herokuapp.com/countries"));

    $country = $get;

    $er = [];

    for($i=0;$i<sizeof($country);$i++){
        array_push($er, $country[$i]->country);
    }

    sort($er);

    $vb = [];

    for($i=0;$i<sizeof($country);$i+=2){
        array_push($vb, [['text' => $er[$i]],['text' => $er[$i+1]]]);
    }

    $key = json_encode(['keyboard' => $vb]);

    bot('sendMessage',[
        'chat_id'=>$cid,
        'text'=>"
        🇺🇿 O'zbekcha
    *Assalomu alaykum, ushbu botda siz davlatlardagi koronavirus statistikasini bilib olishingiz mumkin.*

🇷🇺 Русский
    *Здравствуйте, в этом боте вы можете найти статистику коронавируса по странам.*

🇬🇧 English
    *Hello, in this bot you can find coronavirus statistics by countries.*",
        'parse_mode'=>"Markdown",
        'reply_markup'=>$key
    ]);



    $getq = json_decode(file_get_contents("https://coronavirus-19-api.herokuapp.com/all"));

    $confirmedq = $getq->cases;
    $recoveredq = $getq->recovered;
    $deathsq = $getq->deaths;
    sleep(2);
    bot('sendMessage',[
        'chat_id'=>$cid,

        'text'=>"
        🇺🇿 O'zbekcha
    *Butun dunyo koronavirus statistikasi*
        🦠*Jami aniqlangan:* $confirmedq
        🦠*Sog'aygan:* $recoveredq
        🦠*Vafot etgan:* $deathsq

🇷🇺 Русский
    *Мировая статистика коронавируса*
        🦠*Всего определено:* $confirmedq
        🦠*Исцеляемое:* $recoveredq
        🦠*Он умер:* $deathsq

🇬🇧 English
    *Worldwide coronavirus statistics*
        🦠*Confirmed:* $confirmedq
        🦠*Recovered:* $recoveredq
        🦠*Deaths:* $deathsq",

        'parse_mode'=>"Markdown",
    ]);


}




if($tx and $tx!="/start"){
    $txw = str_replace(" ","%20",trim($tx));
    $get = json_decode(file_get_contents("https://coronavirus-19-api.herokuapp.com/countries/$txw"));

    if($get){
        $confirmed = $get->cases;
        $confirmedToday = $get->todayCases;
        $recovered = $get->recovered;
        $deaths = $get->deaths;
        $deathsToday = $get->todayDeadhs;
        $active = $get->active;
        $ogir = $get->critical;


        if(!$ogir){
            $ogir=0;
        }if(!$ogir){
            $ogir=0;
        }if(!$deathsToday){
            $deathsToday=0;
        }

        bot('sendMessage',[
            'chat_id'=>$cid,
            'text'=>"
        🇺🇿 O'zbekcha
    *$tx da Koronavirus statistikasi*
        🦠*Jami aniqlangan:* $confirmed
        🦠*Bugun aniqlangan:* $confirmedToday
        🦠*Sog'aygan:* $recovered
        🦠*Vafot etgan:* $deaths
        🦠*Bugun vafot etgan:* $deathsToday
        🦠*Kasallar:* $active
        🦠*Og'ir ahvoldagilar:* $ogir

🇷🇺 Русский
    *Статистика коронавируса в $tx*
        🦠*Всего определено:* $confirmed
        🦠*Выявлено сегодня:* $confirmedToday
        🦠*Исцеляемое:* $recovered
        🦠*Он умер:* $deaths
        🦠*Он умер сегодня:* $deathsToday
        🦠*Пациенты:* $active
        🦠*Те в критическом состоянии:* $ogir

🇬🇧 English
    *Coronavirus statistics in $tx*
        🦠*Confirmed:* $confirmed
        🦠*Confirmed today:* $confirmedToday
        🦠*Recovered:* $recovered
        🦠*Deaths:* $deaths
        🦠*Deaths today:* $deathsToday
        🦠*Patients:* $active
        🦠*Those in critical condition:* $ogir",

            'parse_mode'=>"Markdown",
        ]);


    }
    else{
        bot('sendMessage',[
            'chat_id'=>$cid,
            'text'=>"
        🇺🇿 O'zbekcha
    Iltimos qaytadan /start buyrug'ini tanlang

🇷🇺 Русский
    Пожалуйста, выберите команду перезагрузки /start

🇬🇧 English
    Please select the restart /start command",
        ]);
    }
}
?>