<?php

namespace App\Services;

use JsonException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GeminiAIService
{
    private function postToGeminiAI(string $prompt): string|array
    {
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
            Log::error('Gemini AI API request failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return 'ERROR: Failed to get response from AI.';
        }

        $result = $response->json();
        $aiText = $result['candidates'][0]['content']['parts'][0]['text'];
        $parsed = $this->extractJsonFromText($aiText);

        return $parsed;
    }

    protected function extractJsonFromText(string $responseText): ?array
    {
        // Hapus pembungkus markdown ```json dan ```
        $responseText = preg_replace('/```(?:json)?\s*/', '', $responseText); // hapus pembuka
        $responseText = preg_replace('/\s*```/', '', $responseText); // hapus penutup

        // Hapus karakter sebelum JSON dimulai
        $cleaned = trim(preg_replace('/^[^\{\[]+/', '', $responseText));

        // Cari penutup array/objek JSON terakhir
        $end = strrpos($cleaned, '}');
        $altEnd = strrpos($cleaned, ']');

        if ($end !== false || $altEnd !== false) {
            $lastPos = max($end, $altEnd);
            $cleaned = substr($cleaned, 0, $lastPos + 1);
        }

        try {
            return json_decode($cleaned, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Log::warning('Gagal parse JSON dari AI response', [
                'error' => $e->getMessage(),
                'response_text' => $responseText,
                'cleaned_text' => $cleaned,
            ]);
            return null;
        }
    }

    public function parseReceiptToJson(string $rawText): string|array
    {
        $prompt = <<<PROMPT
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
            $rawText
            PROMPT;

        return $this->postToGeminiAI($prompt);
    }

    public function checkUpFinanceAI(string $financeSituation = ''): string|array
    {
        // Jika tidak diberikan input, gunakan default dummy data
        if (empty($financeSituation)) {
            $user = Auth::user();

            $monthlyIncome = $user->incomes()
                ->whereMonth('received_at', now()->subMonth()->month)
                ->sum('amount');
            $monthlyExpenses = $user->expenses()
                ->whereMonth('expense_date', now()->subMonth()->month)
                ->sum('amount');
            $assetsTotal = $user->assets()->sum('value');
            $debtsTotal = $user->debts()->sum('amount');
            $emergencyFundCurrent = $user->emergencyFunds()->sum('current_amount');
            $emergencyFundTarget = $user->emergencyFunds()->sum('target_amount');

            $data = [
                "monthly_income" => $monthlyIncome,
                "monthly_expenses" => $monthlyExpenses,
                "emergency_fund_current" => $emergencyFundCurrent,
                "emergency_fund_target" => $emergencyFundTarget,
                "assets_total" => $assetsTotal,
                "debts_total" => $debtsTotal,
                "net_worth" => $assetsTotal - $debtsTotal,
                "has_investment" => $user->assets()->exists(),
                "has_retirement_plan" => $user->budgetPlans()->where('category_id', 38)->exists(),
                "has_dependents" => true
            ];

            $financeSituation = json_encode($data, JSON_PRETTY_PRINT);
        }

        $prompt = <<<PROMPT
            Buatlah analisis Check-up Kesehatan Finansial dan Saran Perbaikan Keuangan berdasarkan data finansial pribadi berikut. Gunakan bahasa Indonesia yang jelas, positif, dan edukatif. Hasil akhir hanya berupa struktur JSON dengan format yang sudah ditentukan.

            Berikan penilaian kondisi finansial user dalam bentuk:
            1. Financial Check-up (menilai tabungan darurat, kemampuan memenuhi kebutuhan, kesiapan menghadapi kejadian tak terduga).
            2. Financial Improvement Suggestions (berikan 2–4 saran personal yang actionable dan masuk akal sesuai data).

            Fokuskan saran agar:
            - Menyesuaikan kondisi riil user (misalnya: gaji pas-pasan, utang besar, aset kecil, atau cashflow positif).
            - Bisa dilakukan segera (bukan saran jangka sangat panjang saja).
            - Sampaikan dengan empati dan optimisme, tanpa menggurui.

            Output hanya dalam JSON seperti struktur berikut:
            [
                {
                    "title": "Financial Health Check-Up",
                    "bold_points": [
                    "Has emergency savings",
                    "Income covers daily needs",
                    "Low debt compared to income"
                    ],
                    "recommendations": [
                    "Build an emergency fund equal to 3–6 months of expenses",
                    "Ensure consistent budgeting to manage daily needs",
                    "Pay off high-interest debts as a priority"
                    ]
                },
                {
                    "title": "Improvement Suggestions",
                    "bold_points": [
                    "Increase long-term investments",
                    "Avoid consumer debt",
                    "Plan for financial legacy"
                    ],
                    "recommendations": [
                    "Focus on asset diversification across various instruments",
                    "Provide financial education to potential heirs",
                    "Create a clear estate or inheritance plan"
                    ]
                }
                ]


            Jangan tampilkan penjelasan, disclaimer, atau pengantar tambahan. Langsung JSON saja.

            Berikut adalah situasi finance user:
            $financeSituation
            PROMPT;

        return $this->postToGeminiAI($prompt);
    }
}
