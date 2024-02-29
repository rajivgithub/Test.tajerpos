<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChatController extends Controller
{
    public function adminUserList() {
        $role_ids = [2,3];
        $user_details = User::select('id','name','profile_image','store_id')
            ->whereNotIn('id',[Auth::user()->id])
            ->where([
                ['status', '=', 1],
                ['is_deleted', '=', 0]
            ])
            ->whereIn('is_admin',$role_ids)
            ->get()->toArray();
        if(!empty($user_details)) {
            foreach($user_details as $key => $user) {
                $unread_message_count = Chat::selectRaw('COUNT(store_messages.msg_id) AS unread_count')->where('incoming_msg_id',Auth::user()->id)->where('outgoing_msg_id',$user['id'])->where('store_messages.read', '=', 0)->get()->toArray();
                $user_details[$key]['unread_count'] = $unread_message_count[0]['unread_count'];
                $last_message = Chat::select('message')->where([
                    ['incoming_msg_id', '=', Auth::user()->id],
                    ['outgoing_msg_id', '=', $user['id']]
                ])
                ->orWhere([
                    ['outgoing_msg_id', '=', Auth::user()->id],
                    ['incoming_msg_id', '=', $user['id']]
                ])->orderBy('msg_id',"desc")->limit(1)->get()->toArray();
                $user_details[$key]['last_message'] = !empty($last_message) ? $last_message[0]['message'] : '';                
            }
        }
        return response()->json(['user_details'=>$user_details, 'status'=>200]);
    }

    public function insertChat(Request $request) {
        $chat_data = [];
        $chat_data['incoming_msg_id'] = $request->incoming_msg_id;
        $chat_data['outgoing_msg_id'] = Auth::user()->id;
        $chat_data['message'] = $request->message;
        $chat_data['store_id'] = Auth::user()->store_id;
        DB::beginTransaction();
        Chat::create($chat_data);
        DB::commit();
        return response()->json(['status'=>200]);
    }

    public function getChat(Request $request) {
        $outgoing_id = Auth::user()->id;
        $incoming_id = $request->incoming_msg_id;
        $output = "";
        Chat::where('incoming_msg_id', $outgoing_id)
            ->where('outgoing_msg_id', $incoming_id)
            ->where('read', 0)
            ->update(['read' => 1]);
        $get_message_data = Chat::leftJoin('users', 'users.id', '=', 'store_messages.outgoing_msg_id')->select('store_messages.message','outgoing_msg_id','name','store_messages.created_at')->where([
            ['outgoing_msg_id', '=', $outgoing_id],
            ['incoming_msg_id', '=', $incoming_id]
        ])->orWhere([
            ['outgoing_msg_id', '=', $incoming_id],
            ['incoming_msg_id', '=', $outgoing_id]
        ])->orderBy('msg_id','asc')->get()->toArray();
        if(!empty($get_message_data)) {
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            $previousDayTitle = null;
            for($i=0;$i<count($get_message_data);$i++) {
                $messageDate = Carbon::parse($get_message_data[$i]['created_at'])->format('Y-m-d');
                if ($messageDate === $today->format('Y-m-d')) 
                    $dayTitle = 'Today';
                elseif ($messageDate === $yesterday->format('Y-m-d')) 
                    $dayTitle = 'Yesterday';
                else 
                    $dayTitle = Carbon::parse($get_message_data[$i]['created_at'])->format('F d, Y');

                $output .= '<li>';

                if ($dayTitle !== $previousDayTitle) {
                    $output .= '<li><div class="chat-day-title"><span class="title">' . $dayTitle . '</span></div></li>';
                }

                if($get_message_data[$i]['outgoing_msg_id'] === $outgoing_id){            
                    $output .= '<li class="right">
                                    <div class="conversation-list">
                                        <div class="ctext-wrap"> 
                                            <div class="conversation-name">'. $get_message_data[$i]['name'] .'</div>
                                            <p>'. $get_message_data[$i]['message'] .'</p>
                                            <p class="chat-time mb-0"><i class="fa fa-clock-o mr-1"></i> '.date("H:i", strtotime($get_message_data[$i]['created_at'])).'</p>
                                        </div>
                                    </div>
                                </li>';
                } else{
                    $output .= '<li class="">
                                    <div class="conversation-list">
                                        <div class="ctext-wrap">
                                            <div class="conversation-name">'. $get_message_data[$i]['name'] .'</div>
                                            <p>'. $get_message_data[$i]['message'] .'</p>
                                            <p class="chat-time mb-0"><i class="fa fa-clock-o mr-1"></i> '.date("H:i", strtotime($get_message_data[$i]['created_at'])).'</p>
                                        </div>
                                    </div>
                                </li>';
                }
                $previousDayTitle = $dayTitle;
            }
        } else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }
        return response()->json(['chat_data'=>$output, 'status'=>200]);
    }

    public function unreadChatCount() {
        $unread_chat_count = Chat::where('incoming_msg_id', Auth::user()->id)
            ->where('read', 0)
            ->selectRaw('COUNT(msg_id) as unread_message_count')
            ->get();
        return response()->json(['status'=>200,'unread_chat_count'=>$unread_chat_count]);
    }
}
