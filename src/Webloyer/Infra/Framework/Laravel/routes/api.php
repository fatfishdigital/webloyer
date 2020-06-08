<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use JsonRPC\Server as JsonRpcServer;
use Webloyer\Infra\Ui\Api\JsonRpc\Api as JsonRpcApi;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->namespace('V1')->group(function () {
    Route::middleware('auth:api')->post('jsonrpc', function (Request $request) {
        $server = new JsonRpcServer();
        $server->getProcedureHandler()->withObject(App::make(Api::class));
        return $server->execute();
    });
});
