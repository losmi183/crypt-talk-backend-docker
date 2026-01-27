<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[
    OA\Info(version: "1.0.0", description: "Messenger backend", title: "Messenger backend api"),
    OA\Schema(format:"https"),
    OA\Server(url: 'http://crypt-talk.test/api', description: "Dev server"),
    OA\Server(url: 'http://95.217.156.195/api', description: "Dev server"),
    OA\SecurityScheme( securityScheme: 'bearerAuth', type: "http", name: "Authorization", in: "header", scheme:"Bearer", description: "Authorize with bearer token"),
    OA\Contact(email: "tcom.developer@gmail.com")
]

abstract class Controller
{
    //
}
