<?php

namespace App\Enums;

enum ThesisType: string
{
    case SKRIPSI = 'skripsi';
    case TESIS = 'tesis';
    case DISERTASI = 'disertasi';

    public function label(): string
    {
        return match($this) {
            self::SKRIPSI => 'Skripsi',
            self::TESIS => 'Tesis',
            self::DISERTASI => 'Disertasi',
        };
    }

    public function degree(): string
    {
        return match($this) {
            self::SKRIPSI => 'S1',
            self::TESIS => 'S2',
            self::DISERTASI => 'S3',
        };
    }

    public function fullLabel(): string
    {
        return $this->label() . ' (' . $this->degree() . ')';
    }

    public function color(): string
    {
        return match($this) {
            self::SKRIPSI => 'blue',
            self::TESIS => 'purple',
            self::DISERTASI => 'amber',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::SKRIPSI => 'fa-graduation-cap',
            self::TESIS => 'fa-user-graduate',
            self::DISERTASI => 'fa-award',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->fullLabel()
        ])->toArray();
    }

    public static function fromDegree(string $degree): ?self
    {
        return match(strtoupper($degree)) {
            'S1' => self::SKRIPSI,
            'S2' => self::TESIS,
            'S3' => self::DISERTASI,
            default => null,
        };
    }
}
