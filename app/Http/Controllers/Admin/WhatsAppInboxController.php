<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class WhatsAppInboxController extends Controller
{
    public function index()
    {
        $tableMissing = !Schema::hasTable('whatsapp_conversations');

        return view('admin.whatsapp.inbox', compact('tableMissing'));
    }
}
