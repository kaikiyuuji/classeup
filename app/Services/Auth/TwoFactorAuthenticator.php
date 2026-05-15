<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Support\Str;

/**
 * Implementação caseira de TOTP RFC 6238 (Time-based One-Time Password)
 * compatível com Google Authenticator, 1Password, Authy.
 *
 * Sem dependência externa. ~80 linhas, focada e testável.
 */
class TwoFactorAuthenticator
{
    private const PERIOD = 30; // janela de tempo em segundos

    private const DIGITS = 6;

    private const WINDOW_TOLERANCE = 1; // aceita ±1 janela (compensa relógio descalibrado)

    /**
     * Gera um secret base32 de 16 caracteres.
     */
    public function gerarSecret(): string
    {
        return Str::random(16);
    }

    /**
     * Valida um código TOTP contra o secret, considerando tolerância.
     */
    public function validar(string $secret, string $codigo): bool
    {
        $codigo = preg_replace('/\s+/', '', $codigo);
        if (strlen($codigo) !== self::DIGITS || ! ctype_digit($codigo)) {
            return false;
        }

        $contadorAtual = (int) floor(time() / self::PERIOD);

        for ($delta = -self::WINDOW_TOLERANCE; $delta <= self::WINDOW_TOLERANCE; $delta++) {
            if (hash_equals($this->gerarCodigo($secret, $contadorAtual + $delta), $codigo)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gera o URL otpauth:// para o QR code (compatível Google Authenticator).
     */
    public function urlOtpAuth(string $secret, string $accountName, string $issuer): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            rawurlencode($issuer),
            rawurlencode($accountName),
            $this->base32Encode($secret),
            rawurlencode($issuer),
            self::DIGITS,
            self::PERIOD,
        );
    }

    /**
     * Gera 8 códigos de recuperação (cada um 10 chars).
     *
     * @return list<string>
     */
    public function gerarRecoveryCodes(int $quantos = 8): array
    {
        return array_map(static fn () => Str::random(10), range(1, $quantos));
    }

    private function gerarCodigo(string $secret, int $contador): string
    {
        $contadorBinario = pack('N*', 0, $contador);
        $hash = hash_hmac('sha1', $contadorBinario, $secret, true);

        $offset = ord($hash[19]) & 0x0F;
        $valor = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $modulo = 10 ** self::DIGITS;

        return str_pad((string) ($valor % $modulo), self::DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Encode(string $secret): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        foreach (str_split($secret) as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $output = '';
        foreach (str_split($binary, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0');
            $output .= $alphabet[bindec($chunk)];
        }

        return $output;
    }
}
