<?php

namespace App\Livewire;

use Livewire\Component;

class AiChatbotButton extends Component
{
    public function openChat()
    {
        $this->dispatch('open-ai-chat');
    }

    public function render()
    {
        return view('livewire.ai-chatbot-button');
    }
}
