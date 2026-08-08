<?php

class SystemController extends Controller
{
    /** Test route: GET /test/db */
    public function dbTest(): void
    {
        $result = db_ping();
        $this->json([
            'status'  => $result['ok'] ? 'ok' : 'error',
            'message' => $result['message'],
            'time'    => date('c'),
        ], $result['ok'] ? 200 : 503);
    }
}
