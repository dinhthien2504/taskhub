<?php

namespace App\Services\EmailTemplates;

class ShortcodeParserService
{
    public static function parse(string $template, array $data): string
    {
        return preg_replace_callback('/{{\s*(.*?)\s*}}/', function ($matches) use ($data) {
            $key = trim($matches[1]);
            return $data[$key] ?? $matches[0];
        }, $template);
    }

    public static function extract(string $template): array
    {
        preg_match_all('/{{(.*?)}}/', $template, $matches);
        return array_map('trim', $matches[1] ?? []);
    }

}