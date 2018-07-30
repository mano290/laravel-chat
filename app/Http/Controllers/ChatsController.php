<?php namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Message;
use App\Services\UserService;
use Auth;
use Illuminate\Http\Request;

/**
 * Class ChatsController
 * @package App\Http\Controllers
 */
class ChatsController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * ChatsController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth');
        $this->userService = $userService;
    }

    /**
     * Show chats
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->to("/home");
    }

    /**
     * Fetch all messages
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    /**
     * Persist message to database
     *
     * @param Request $request
     * @return array
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        $message = $user->messages()->create([
            'message' => $request->get('message')
        ]);

        broadcast(new MessageSent($user, $message))->toOthers();

        return ['status' => 'Message Sent!'];
    }

    public function createNewChat()
    {
        $to_user = request()->get('user');

        $message = request()->get('message');

        $this->userService->createNewChatUser($to_user, $message);
    }
}
