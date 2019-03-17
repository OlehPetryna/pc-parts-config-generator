<?php
declare(strict_types=1);

namespace App\Domain\PickerWizard;

use App\Domain\PcParts\PartsCollection;

class Encoder
{
    private $secret;

    public function __construct()
    {
        $this->secret = getenv('WIZARD_ENCODER_SECRET');
    }

    public function encode(PartsCollection $parts, int $stage): string
    {
        $data = ['parts' => $parts, 'stage' => $stage];

        return base64_encode(json_encode($data));
    }

    public function decode(string $data): array
    {
        return json_decode(base64_decode($data), true);
    }

    public function sign(string $data): string
    {
        return hash_hmac('sha256', $data, $this->secret, true);
    }

    public function checkSign(string $sign, string $data): bool
    {
        return $this->sign($data) === $sign;
    }
}