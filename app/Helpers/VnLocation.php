<?php

namespace App\Helpers;

class VnLocation
{
    protected static array $provinces = [];
    protected static array $wards = [];
    protected static bool $loaded = false;

    protected static function ensureLoaded(): void
    {
        if (self::$loaded) {
            return;
        }

        $provincePath = resource_path('data/provinces.json');
        $wardPath = resource_path('data/wards.json');

        if (is_file($provincePath)) {
            $json = file_get_contents($provincePath);
            self::$provinces = json_decode($json, true) ?? [];
        }

        if (is_file($wardPath)) {
            $json = file_get_contents($wardPath);
            self::$wards = json_decode($json, true) ?? [];
        }

        self::$loaded = true;
    }

    public static function provinces(): array
    {
        self::ensureLoaded();
        return self::$provinces;
    }

    public static function wards(): array
    {
        self::ensureLoaded();
        return self::$wards;
    }

    public static function wardsOfProvince(?string $provinceId): array
    {
        if (!$provinceId) {
            return [];
        }

        self::ensureLoaded();

        return array_values(array_filter(self::$wards, static function (array $ward) use ($provinceId) {
            return isset($ward['province_id']) && (string) $ward['province_id'] === (string) $provinceId;
        }));
    }

    public static function findProvince(?string $provinceId): ?array
    {
        if (!$provinceId) {
            return null;
        }

        self::ensureLoaded();

        foreach (self::$provinces as $province) {
            if ((string) ($province['id'] ?? '') === (string) $provinceId) {
                return $province;
            }
        }

        return null;
    }

    public static function findWard(?string $wardId): ?array
    {
        if (!$wardId) {
            return null;
        }

        self::ensureLoaded();

        foreach (self::$wards as $ward) {
            if ((string) ($ward['id'] ?? '') === (string) $wardId) {
                return $ward;
            }
        }

        return null;
    }

    public static function addressLabel(?string $detail, ?string $wardId, ?string $provinceId): string
    {
        $parts = [];

        if ($detail) {
            $parts[] = trim($detail);
        }

        $ward = self::findWard($wardId);
        if ($ward) {
            $parts[] = $ward['name'] ?? '';
        }

        $province = self::findProvince($provinceId);
        if ($province) {
            $parts[] = $province['name'] ?? '';
        }

        return implode(', ', array_filter($parts));
    }
}
