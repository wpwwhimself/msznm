<?php

namespace App\Http\Controllers;

use App\Mail\QuestAwaitingPayment;
use App\Mail\QuestAwaitingReview;
use App\Models\Quest;
use App\Models\Request;
use App\Models\StatusChange;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class JanitorController extends Controller
{
    public function index(){
        /**
         * constants
         */

        foreach([
            "quest_reminder_time",
            "request_expired_after",
            "quest_expired_after",
            ] as $name){
            $$name = setting($name);
        }

        /**
         * expiring requests
         */
        $requests = Request::where("status_id", 5)
            ->where("updated_at", "<=", Carbon::now()->subDays($request_expired_after)->toDateString())
            ->orWhere("deadline", "<=", Carbon::today()->toDateString())
            ->get();
        foreach($requests as $request){
            $request->update(["status_id", 7]);
            app("App\Http\Controllers\BackController")->statusHistory($request->id, 7, "brak opinii", 1, null);
        }

        /**
         * expiring unreviewed quests
         */
        $quests = Quest::where("status_id", 15)
            ->where("updated_at", "<=", Carbon::now()->subDays($quest_expired_after)->toDateString())
            ->get();
        foreach($quests as $quest){
            $quest->update(["status_id", 17]);
            app("App\Http\Controllers\BackController")->statusHistory($quest->id, 17, "brak opinii", 1, null);
        }

        /**
         * expiring accepted but unpaid quests
         */
        $quests = Quest::where("paid", 0)
            ->where("status_id", 19)
            ->where("updated_at", "<=", Carbon::now()->subDays($quest_expired_after)->toDateString())
            ->get();
        foreach($quests as $quest){
            $quest->update(["status_id", 17]);
            $quest->client->update(["trust" => -1]);
            app("App\Http\Controllers\BackController")->statusHistory($quest->id, 17, "brak wpłaty", 1, null);
        }

        /**
         * reminding clients about unreviewed quests
         */
        $quests = Quest::where("status_id", 15)->get();
        foreach($quests as $quest){
            if(
                $quest->updated_at->diffInDays(Carbon::now()) % $quest_reminder_time == 0
                &&
                !$quest->updated_at->isToday()
            ){
                if($quest->client->email){
                    Mail::to($quest->client->email)->send(new QuestAwaitingReview($quest));
                    StatusChange::where("re_quest_id", $quest->id)->where("new_status_id", 15)->orderByDesc("date")->first()->increment("mail_sent");
                }
            }
        }

        /**
         * reminding clients about accepted but unpaid quests
         */
        $quests = Quest::where("paid", 0)->where("status_id", 19)->get();
        foreach($quests as $quest){
            if(
                $quest->updated_at->diffInDays(Carbon::now()) % $quest_reminder_time == 0
                &&
                !$quest->updated_at->isToday()
            ){
                if($quest->client->email){
                    Mail::to($quest->client->email)->send(new QuestAwaitingPayment($quest));
                    //status
                    $status = StatusChange::where("re_quest_id", $quest->id)->where("new_status_id", 33)->first();
                    if($status){
                        $status->increment("mail_sent");
                    }else{
                        app("App\Http\Controllers\BackController")->statusHistory($quest->id, 33, null, 1, true);
                    }
                }
            }
        }
    }

    public function log(){
        $logs = StatusChange::whereRaw("new_status_id = 15 AND mail_sent > 1 OR new_status_id IN (7,17,33)")
            ->whereDate('date', '>=', now()->subDays(5)->setTime(0,0,0)->toDateTimeString())
            ->get();

        return view(user_role().".janitor-log", array_merge(
            ["title" => "Logi Sprzątacza"],
            compact("logs")
        ));
    }
}