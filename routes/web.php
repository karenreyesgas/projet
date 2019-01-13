<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->group(['prefix' => 'blog'], function () use ($router) {
    $router->get('/', function () {
        return "hello blog";
    });
    $router->get('/{name}', function ($name) {
        return "hi $name";
    });
});
$router->group(['prefix' => '/admin'], function () use ($router) {
    $router->get('/', function () {
        return "page admin";
    });
    $router->group(['prefix' => '/blog'], function () use ($router) {
        $router->get('/', function () {
            return "admin blog";
        });
        $router->get('/edit/{id:[0-9]+}', function ($id) {
            return view('edit');
        });
		$router->post('/edit/{id:[0-9]+}', function (Request $request) {
			$data = $request->json()->all();
			return response()->json($data);
        });
    });
});




$router->get('/accueil', function () {
    return view('accueil');
});
$router->get('/connexion', function () {
    session_start();
    if(isset($_SESSION['connecte']) && $_SESSION['connecte'] == 1){
        return redirect('infos');
    }else{
        return view('connexion');
    }
});
$router->post('/connexion', function () {
	$query = DB::table('user')
		->select(DB::raw('count(*) as result'))
		->where([['pseudo', $_POST['pseudo']],['password', $_POST['mdp']]])
        ->get();
	if($query[0]->result == 1){
        session_start();
        $query = DB::table('user') //J'ai essayé de récuperer le nom et prénom pour l'afficher au lieu d'afficher le pseudo
        ->select('pseudo', 'name', 'lastName')
        ->where('pseudo',  $_POST['pseudo'])
        ->get();
        
        $_SESSION['connecte'] = 1;
        $_SESSION['pseudo'] = $_POST['pseudo'];
        echo $_SESSION['pseudo'];
		return redirect('infos');
	}else{
		return view('connexion',['error' => 'ERROR! User or password was incorrect']);//Afficher un message d'erreur
	}
});
$router->group(['prefix' => '/infos'], function () use ($router) {
    $router->get('/', function () {
        session_start();
        if(isset($_SESSION['connecte']) && $_SESSION['connecte'] == 1){
            return view('infos');
        }else{
            return redirect('connexion');
        }
    });
    $router->post('/', function(){
        session_start();
        $query = DB::table('route')
        ->join('user', 'route.user_idutilisateur', '=', 'user.idutilisateur')
        ->select('route.idroute', 'route.dateroute', 'route.debutroute', 'route.finroute')
        ->where('user.pseudo',  $_SESSION['pseudo'])
        ->orderBy('route.dateroute', 'desc')
        ->get();
        return response()->json($query);
    });
    $router->post('/infosCourse', function (Request $request) {
        $data = $request->json()->all();
        $query = DB::table('infoRoute')
        ->select('cardio', 'speed', 'dateinfo', 'route_idroute')
        ->where('route_idroute',  $data[0])
        ->get();
        return response()->json($query);
    });
});
$router->get('/deco', function(){
    session_start();
    session_destroy();
    return redirect('accueil');
});