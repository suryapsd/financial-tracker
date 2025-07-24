<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIParserService
{
    public function parseReceiptToJson(string $rawText): string|array
    {
        // $text = $this->preprocessOCR($rawText);
        $prompt = $this->buildPrompt($rawText);

        $response = Http::withHeaders([
            'Content-Type'    => 'application/json',
            'X-goog-api-key'  => env('GEMINI_KEY'),
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent', [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if (! $response->successful()) {
            Log::error('Cohere API request failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return 'ERROR: Failed to get response from AI.';
        }

        $result = $response->json();
        // $aiText = $result['generations'][0]['text'];
        $aiText = $result['candidates'][0]['content']['parts'][0]['text'];
        $parsed = $this->extractJsonFromText($aiText);

        return $parsed;
    }

    protected function buildPrompt(string $text): string
    {
        return <<<PROMPT
            Ubah teks struk belanja berikut menjadi format JSON dengan struktur:

            {
            "shop_name": "",
            "address": "",
            "receipt_number": "",
            "date": "",
            "time": "",
            "cashier": "",
            "items": [
                {
                "name": "",
                "quantity": 0,
                "unit_price": 0,
                "subtotal": 0
                }
            ],
            "discount": 0,
            "tax": 0,
            "total_amount": 0,
            "payment_method": ""
            }

            Ketentuan:
            - Ambil data hanya jika benar-benar terbaca dari teks.
            - Jika discount, tax, atau receipt_number tidak ditemukan, isi dengan 0 atau kosong.
            - Pastikan unit_price * quantity == subtotal, jika tidak cocok, tetap pakai yang terbaca.
            - Format tanggal: YYYY-MM-DD, waktu: HH:MM.
            - Harga seperti "77 400" dibaca sebagai 77400.
            - Jangan mengarang isi.

            Teks struk:
            $text
            PROMPT;
    }

    protected function preprocessOCR(string $text): string
    {
        // Hilangkan simbol aneh, koma, spasi ganda
        $text = preg_replace('/[^\x20-\x7E\n]/', '', $text);
        $text = preg_replace('/\s{2,}/', ' ', $text);
        $text = preg_replace('/,/', '', $text);
        return $text;
    }

    protected function extractJsonFromText(string $responseText): ?array
    {
        // Hapus semua karakter sebelum tanda kurung JSON
        $cleaned = trim(preg_replace('/^[^\{\[]+/', '', $responseText));

        // Temukan penutupan JSON
        $end = strrpos($cleaned, '}');
        if ($end !== false) {
            $cleaned = substr($cleaned, 0, $end + 1);
        }

        try {
            return json_decode($cleaned, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            Log::warning('Gagal parse JSON dari AI response', [
                'error' => $e->getMessage(),
                'response_text' => $responseText,
                'cleaned_text' => $cleaned,
            ]);
            return null;
        }
    }
}
