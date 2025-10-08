<?php

namespace App\Jobs;

use App\Models\CustomNotification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AskForCommission implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $this->sendCommissionForProviders();
        $this->sendCommissionForClients();

        
    }

    public function sendCommissionForProviders(){
        Log::info('Sending commission notification...');
        try{
            //1) get all provider users 
         $providers  = User::whereHas('roles', function($query){
            $query->where('name', 'provider');
         })->get();
         Log::info('Providers found: ' . $providers->count());
        //2) create db notification for them to ask them about commission 
         $data = [
            'title'=>[
                'ar'=>'هل تم بالبيع من خلال التطبيق؟',
                'en'=>'Did you sell through the app?'
            ],
            'body'=>[
                'ar'=>'نريد أن نعرف ما إذا كنت قد باعت من خلال التطبيق أم لا. يرجى الإجابة على السؤال التالي:',
                'en'=>'We want to know if you sold through the app or not. Please answer the following question:',
            ],
            'metadata'=>[
                'type'=>'ask_for_commission',
            ]
            ];

        Log::info('Creating notification...');
        foreach($providers as $provider){
            $provider->customNotifications()->create($data);
        }
        Log::info('Notification created successfully');
        }catch(\Exception $e){
            Log::error('Error sending commission notification: ' . $e->getMessage());
        }

        Log::info('Commission notification sent successfully');

    }
    public function sendCommissionForClients(){
        Log::info('Sending commission notification...');
        try{
            //1) get all provider users 
         $clients  = User::whereHas('roles', function($query){
            $query->where('name', 'client');
         })->get();
         Log::info('Clients found: ' . $clients->count());
        //2) create db notification for them to ask them about commission 
         $data = [
            'title'=>[
                'ar'=>'هل تم بالشراء من خلال التطبيق؟',
                'en'=>'Did you buy through the app?'
            ],
            'body'=>[
                'ar'=>'نريد أن نعرف ما إذا كنت قد شريت من خلال التطبيق أم لا. يرجى الإجابة على السؤال التالي:',
                'en'=>'We want to know if you buy through the app or not. Please answer the following question:',
            ],
            'metadata'=>[
                'type'=>'ask_for_commission_client',
            ]
            ];

        Log::info('Creating notification...');
        foreach($clients as $client){
            $client->customNotifications()->create($data);
        }
        Log::info('Notification created successfully');
        }catch(\Exception $e){
            Log::error('Error sending commission notification: ' . $e->getMessage());
        }

        Log::info('Commission notification sent successfully');

    }
}
