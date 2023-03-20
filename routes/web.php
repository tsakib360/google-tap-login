<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $data['google_oauth_client_id'] = config('services.google.id');
//    $google_oauth_client_id = config('google.secret');
    return view('welcome', $data);
});

Route::post('/login', function () {
    $google_oauth_client_id = config('services.google.id');
    // create google client object with client ID
    $client = new Google_Client([
        'client_id' => $google_oauth_client_id
    ]);

    // verify the token sent from AJAX
    $id_token = $_POST["id_token"];

    $payload = $client->verifyIdToken($id_token);
    if ($payload && $payload['aud'] == $google_oauth_client_id)
    {
        // get user information from Google
        $user_google_id = $payload['sub'];

        $name = $payload["name"];
        $email = $payload["email"];
        $picture = $payload["picture"];

        // login the user
        $_SESSION["user"] = $user_google_id;

        // send the response back to client side
        return "Successfully logged in. " . $user_google_id . ", " . $name . ", " . $email . ", " . $picture;
    }
    else
    {
        // token is not verified or expired
        return "Failed to login.";
    }
})->name('login.post');
