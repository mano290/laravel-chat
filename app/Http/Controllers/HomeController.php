<?php namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param UserService $userService
     * @return \Illuminate\Http\Response
     */
    public function index(UserService $userService)
    {
        $chats = $userService->getChatsUser();

        return view('home', compact(
            'chats'
        ));
    }
}
