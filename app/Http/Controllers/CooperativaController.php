<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\UserAppointment;
use App\Models\UserFavorite;
use App\Models\Cooperativa;
use App\Models\CooperativaPhotos;
use App\Models\CooperativaServices;
use App\Models\CooperativaTestimonial;
use App\Models\CooperativaAvailability;

class CooperativaController extends Controller
{
    private $loggedUser;

    public function __construct() {
        $this->loggedUser = auth()->user();
    }
   
    public function createRandom(){
        $array = ['error' => ''];
        for($q=0;$q<15;$q++) {
        $names = ['Coopermiti', 'Coopermape', 'Galpão de Eletrônicos', 'Coopernova Cotia','Cooperzagati', 'Coop de Reci Reluz', 'Coop Cooperfenix', 'Coop de Reci Crescer', 'YouGreen', 'Reciclar Coop', 'Coopamare', 'Eco Ponto','STR Reciclagem', 'Coop Cidade Limpa', 'RCR Ambiental', 'Coop Central'];
        //$lastnames = ['Artes', 'São', 'Guaçu', 'Paulo','Eduardo', 'Redondo', 'Verde', 'Tereza', 'Jardim', 'Marcos', 'Urca', 'Limpo', 'Lourenço', 'Serra', 'Branca', 'Dorado'];

        $servicos = ['Descarte de Eletrônicos', 'Descarte de Resíduos Secos', 'Recuperação e Reciclagem', 'Consultoria e Assessoria', 'Educação Ambiental'];
        $servicos2 = ['Coleta de Vidro', 'Coleta de Papel', 'Coleta de Alumínio', 'Coleta de Lixo Comum', 'Coleta de Plástico', 'Coleta de Pilhas e Baterias', 'Lixo Hospitalares', 'Resíduos Químicos'];

        $depos = [
            'Muito bom o serviço de coleta de resíduos químicos.',
            'Os serviços prestado por essa empresa conseguiu uma pontuação muito boa.',
            'A limpeza deve ser realizada de forma consciênte e é muito bom ver empresas realizando essas ações.',
            'Todos os usuários podem utilizar os recursos do aplicativo, estão de parabéns.',
            'Uma de suas usabilidade é justamente usar o app para melhorar o meio ambiente.',
            'Nenhum serviço é cobrado dos usuários o que vale muito apena usar os recursos do aplicativo.'
        ];

        $newCooperativa = new Cooperativa();
        $newCooperativa->name = $names[rand(0, count($names)-1)];
        //.' '.$lastnames[rand(0, count($lastnames)-1)]
        $newCooperativa->avatar = rand(1,29).'.png';
        $newCooperativa->stars = rand(2, 4).'.'.rand(0,9);
        $newCooperativa->latitude = '-23.6'.rand(0,9).'491';
        $newCooperativa->longitude = '-46.8'.rand(0,9).'526';
        $newCooperativa->save();

        $ns = rand(3, 6);
        
        for($w=0;$w<4;$w++) {
            
            $newCooperativaPhoto = new CooperativaPhotos();
            $newCooperativaPhoto->id_cooperativa = $newCooperativa->id;
            $newCooperativaPhoto->url = rand(1, 50).'.png';
            $newCooperativaPhoto->save();
        }

        for($w=0;$w<$ns;$w++) {

            $newCooperativaService = new CooperativaServices();
            $newCooperativaService->id_cooperativa = $newCooperativa->id;
            $newCooperativaService->name = $servicos[rand(0, count($servicos)-1)].' de '.$servicos2[rand(0, count($servicos2)-1)];
            $newCooperativaService->price = 'Gratuíto';
            $newCooperativaService->save();
        }

        for($w=0;$w<3;$w++) {

            $newCooperativaTestimonial = new CooperativaTestimonial();
            $newCooperativaTestimonial->id_cooperativa = $newCooperativa->id;
            $newCooperativaTestimonial->name = $names[rand(0, count($names)-1)];
            //.' '.$lastnames[rand(0, count($lastnames)-1)]
            $newCooperativaTestimonial->rate = rand(2, 4).'.'.rand(0,9);
            $newCooperativaTestimonial->body = $depos[rand(0, count($depos)-1)];
            $newCooperativaTestimonial->save();
        }
        for($e=0;$e<4;$e++) {
            $rAdd = rand(7, 10);
            $hours = [];
            for($r=0;$r<8;$r++){
                $time = $r + $rAdd;
                if($time < 10) {
                    $time = '0'.$time; 
                }
                $hours[] = $time.':00';
            }
            $newCooperativaAvail = new CooperativaAvailability();
            $newCooperativaAvail->id_cooperativa = $newCooperativa->id;
            $newCooperativaAvail->weekday = $e;
            $newCooperativaAvail->hours = implode(',', $hours);
            $newCooperativaAvail->save();
        }
    }
    return $array;
}
 

    private function searchGeo($address){
        $key= env('MAPS_KEY', null);

        $address = urlencode($address);
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='.$key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true);
    }

    public function list(Request $request) {
        
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $city = $request->input('city');
        $offset = $request->input('offset');
        if(!$offset){
            $offset = 0;
        }

        if(!empty($city)){
            $res = $this->searchGeo($city);

            if(count($res['results']) > 0){
                $lat = $res['results'][0]['geometry']['location']['lat'];
                $lng = $res['results'][0]['geometry']['location']['lng'];
            }
        }elseif(!empty($lat) && !empty($lng)){
            $res = $this->searchGeo($lat.','.$lng);

            if(count($res['results']) > 0){
                $city = $res['results'][0]['formatted_address'];
            }
        } else {
            $lat = '-23.6491';
            $lng = '-46.8526';
            $city = 'Embu das Artes';
        }

        $cooperativas = Cooperativa::select(Cooperativa::raw('*, SQRT(
            POW(69.1 * (-23.6491) * COS( 57.3), 2)) AS distance'))
            ->limit(5)
            ->get();

        foreach($cooperativas as $bkey =>$bvalue) {
            $cooperativas[$bkey]['avatar'] = url('media/avatars/'.$cooperativas[$bkey]['avatar']);
        }

        $array['data'] = $cooperativas;
        $array['loc'] = 'Embu das Artes';
        return $array;
    }

    public function one($id){
        $array = ['error' => ''];

        $cooperativa = Cooperativa::find($id);

        if($cooperativa) {

            $cooperativa['avatar'] = url('media/avatars/'.$cooperativa['avatar']);
            $cooperativa['favorited'] = false;
            $cooperativa['photos'] = [];
            $cooperativa['services']= [];
            $cooperativa['testimonials'] = [];
            $cooperativa['available'] = [];

            //Verificando os favoritos
            $cFavorite = UserFavorite::where('id_user', $this->loggedUser->id)
            //->where('id_cooperativa', $cooperativa->id)
            ->where('id_cooperativa', $id)
            ->count();
            if($cFavorite > 0){
                $cooperativa['favorited'] = true;
            }

            //Pegando as fotos da cooperativa
            $cooperativa['photos'] = CooperativaPhotos::select(['id', 'url'])
            ->where('id_cooperativa', $cooperativa->id)
            ->get();
            foreach($cooperativa['photos'] as $bpkey => $bpvalue){
                $cooperativa['photos'][$bpkey]['url'] = url('media/uploads/'.$cooperativa['photos'][$bpkey]['url']);
            }

            //Pegando os serviços da cooperativa
            $cooperativa['services'] = CooperativaServices::select(['id', 'name', 'price'])
            ->where('id_cooperativa', $cooperativa->id)
            ->get();
            $array['data'] = $cooperativa;

            //Pegando os depoimentos da cooperativa
            $cooperativa['testimonials'] = CooperativaTestimonial::select(['id', 'name', 'rate', 'body'])
            ->where('id_cooperativa', $cooperativa->id)
            ->get();

            //Pegando disponibilidade da cooperativa
            $availability = [];

            // - Pegando a disponibilidade crua
            $avails = CooperativaAvailability::where('id_cooperativa', $cooperativa->id)->get();
            $availWeekdays = [];
            foreach($avails as $item){
                $availWeekdays[$item['weekday']] = explode(',', $item['hours']);
            }

            // - Pegar os agendamentos dos próximos 20 dias
            $appointments = [];
            $appQuery = UserAppointment::where('id_cooperativa', $cooperativa->id)
            ->whereBetween('ap_datetime', [
                date('Y-m-d').' 00:00:00',
                date('Y-m-d', strtotime('+20 days')).' 23:59:59'
            ])
            ->get();
        foreach($appQuery as $appItem) {
            $appointments[] = $appItem['ap_datetime'];
        }

        // - Gerar disponibilidade real
        for($q=0;$q<20;$q++){
            $timeItem = strtotime('+'.$q.' days');
            $weekday = date('w', $timeItem);

            if(in_array($weekday, array_keys($availWeekdays))){
                $hours = [];

                $dayItem = date('Y-m-d', $timeItem);

                foreach($availWeekdays[$weekday] as $hourItem){
                    $dayFormated = $dayItem.' ' .$hourItem.':00';
                    if(!in_array($dayFormated, $appointments)){
                        $hours[]=$hourItem;
                    }
                }
                if(count($hours) > 0) {
                    $availability[] = [
                        'date' => $dayItem,
                        'hours' => $hours
                    ];
                }
            }

        }

            $cooperativa['available'] = $availability;

        }else{
            $array['error'] = 'Cooperativa não existe';
            return $array;
        }

        return $array;
    }

    public function setAppointment($id, Request $request){
        // service, year, month, day, hour
        $array = ['error'=>''];

        $service = $request->input('service');
        $year = intval($request->input('year'));
        $month = intval($request->input('month'));
        $day = intval($request->input('day'));
        $hour = intval($request->input('hour'));

        $month = ($month < 10) ? '0'.$month : $month;
        $day = ($day < 10) ? '0'.$day : $day;
        $hour = ($hour < 10) ? '0'.$hour : $hour;

        // 1- Verificar se o serviço da cooperativa existe
        $cooperativaservice = CooperativaServices::select()
        ->where('id', $service)
        ->where('id_cooperativa', $id)
        ->first();

        if($cooperativaservice){
            // 2- Verificar se a data é real
            $apDate = $year.'-'.$month.'-'.$day.' '.$hour.':00:00';
            if(strtotime($apDate) > 0) {
                // 3- Verificar se a cooperativa já possuí agendamento nesse dia/hora
                $apps = UserAppointment::select()
                ->where('id_cooperativa', $id)
                ->where('ap_datetime', $apDate)
                ->count();
                if($apps === 0){
                    // 4- Verificar se a cooperativa atende nesta data/hora
                    $weekday = date('w', strtotime($apDate));
                    $avail = CooperativaAvailability::select()
                    ->where('id_cooperativa', $id)
                    ->where('weekday', $weekday)
                    ->first();
                    if($avail){
                        // 4.2 - Verificar se a cooperativa atende nesta hora
                        $hours = explode(',', $avail['hours']);
                        if(in_array($hour.':00', $hours)){
                             // 5- Fazer o agendamento
                             $newApp = new UserAppointment();
                             $newApp->id_user = $this->loggedUser->id; 
                             $newApp->id_cooperativa = $id;
                             $newApp->id_service = $service;
                             $newApp->ap_datetime = $apDate;
                             $newApp->save();
                        }else{
                            $array['error'] = 'Cooperativa não atende nesta hora';
                        }
                    }else{
                        $array['error'] = 'Cooperativa não atende neste dia';
                    }
                }else{
                    $array['error'] = 'Cooperativa já possuí agendamento neste dia/hora';
                }
            }else{
                $array['error'] = 'Data inválida';
            }
        }else{
            $array['error'] = 'Serviço inexistente';
        }
        return $array;
    }
    public function search(Request $request){
        $array = ['error'=>'', 'list'=>[]];

        $q = $request->input('q');

        if($q){

            $cooperativas = Cooperativa::select()
            ->where('name', 'LIKE', '%'.$q.'%')
            ->get();

            foreach($cooperativas as $bkey => $cooperativa){
                $cooperativas[$bkey]['avatar'] = url('media/avatars/'.$cooperativas[$bkey]['avatar']);
            }

            $array['list'] = $cooperativas;

        }else{
            $array['error'] = 'Digite algo para realizar a busca';
        }

        return $array;

    }
}
