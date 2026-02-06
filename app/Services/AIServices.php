<?php

namespace App\Services;

use stdClass;


class AIServices {

    public function trimMessagesToTokenLimit(array $messages, int $maxTokens = 3500): array
    {
        $trimmed = [];
        $totalTokens = 0;
        
        // Kreni od kraja (najnovije poruke)
        foreach (array_reverse($messages) as $msg) {
            $msgTokens = $this->estimateTokens($msg->message);
            
            if ($totalTokens + $msgTokens > $maxTokens) {
                break; // Dostignut limit
            }
            
            $totalTokens += $msgTokens;
            array_unshift($trimmed, $msg); // Dodaj na početak
        }
        
        return $trimmed;
    }

    public function estimateTokens(string $text): int
    {
        return (int) ceil(mb_strlen($text) / 4); // ~1 token = 4 karaktera
    }


   public function formatMessagesForAI(array $conversationHistory, stdClass $aiPerson): array
{
    $messages = [];
    
    // Heredoc sintaksa za multi-line stringove
    $systemPrompt = $aiPerson->system_prompt;
    
    $messages[] = [
        'role' => 'system',
        'content' => $systemPrompt
    ];
    
    foreach ($conversationHistory as $msg) {
        $messages[] = [
            'role' => $msg->sender_id == $aiPerson->user_id ? 'assistant' : 'user',
            'content' => $msg->message
        ];
    }
    
    return $messages;
}
}