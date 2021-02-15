<?php

namespace Appart\LaravelGoogleChat;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use SjorsO\Gobble\Facades\Gobble;

class GoogleChatHandler extends AbstractProcessingHandler
{
    public function __construct($level = Logger::DEBUG)
    {
        parent::__construct($level);
    }

    protected function write(array $record): void
    {
        $name = $record['level_name'];
        $color = '#ff1100';

        if ($name === 'DEBUG') {
            $color = '#000000';
        } else if ($name === 'INFO') {
            $color = '#48d62f';
        } else if ($name === 'NOTICE') {
            $color = '#00aeff';
        } else if ($name === 'WARNING') {
            $color = '#ffc400';
        }

        $errorMessages = [
            [
                "textParagraph" => [
                    "text" => $record['message']
                ],
            ],
        ];

        //Add extra context form the error message
        if (!empty($record['context'])) {
            foreach ($record['context'] as $name => $value) {
                $errorMessages[] = [
                    "keyValue" => [
                        "topLabel" => $name,
                        "content" => (string)$value
                    ]
                ];
            }
        }

        Gobble::post(env('LOG_GOOGLE_WEBHOOK_URL'), [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                "cards" => [
                    [
                        "sections" => [
                            [
                                "widgets" => [
                                    [
                                        "textParagraph" => [
                                            "text" => "<b><font color='" . $color . "'>" . $name . "</font></b>"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "widgets" => $errorMessages,
                            ],
                        ],
                    ],
                ],
            ]),
        ]);
    }
}
