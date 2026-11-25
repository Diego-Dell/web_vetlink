<?php

return [
    'supabase_url' => rtrim((string) env('SUPABASE_URL', ''), '/'),
    'supabase_key' => (string) env('SUPABASE_PUBLISHABLE_KEY', ''),
    'timeout' => (int) env('SUPABASE_TIMEOUT', 20),
];
