<?php
/**
 * Иллюстрация машины, окрашенная в цвет автомобиля из БД.
 * @var string $color
 */
$color = preg_match('/^#[0-9a-f]{6}$/i', $color ?? '') ? $color : '#888888';
?>
<svg class="car-svg" viewBox="0 0 240 110" role="img" aria-label="Автомобиль">
    <ellipse cx="120" cy="98" rx="104" ry="6" fill="rgba(0,0,0,.12)"/>
    <path d="M14 72 Q16 54 42 49 L76 46 Q96 27 122 25 L158 25 Q180 27 198 45 L216 49 Q230 53 230 70 L230 82 Q230 87 225 87 L19 87 Q14 87 14 82 Z" fill="<?= e($color) ?>"/>
    <path d="M14 72 Q16 54 42 49 L76 46 Q96 27 122 25 L158 25 Q180 27 198 45 L216 49 Q230 53 230 70 L230 82 Q230 87 225 87 L19 87 Q14 87 14 82 Z" fill="url(#shine)" opacity=".35"/>
    <path d="M86 47 Q101 32 122 31 L138 31 L138 47 Z" fill="#dbe9f1"/>
    <path d="M144 31 L157 31 Q176 33 189 47 L144 47 Z" fill="#dbe9f1"/>
    <rect x="200" y="56" width="16" height="6" rx="3" fill="#ffe9a8"/>
    <rect x="18" y="60" width="10" height="6" rx="3" fill="#ff8a7a"/>
    <circle cx="64" cy="86" r="17" fill="#1d1f24"/><circle cx="64" cy="86" r="7" fill="#9aa0a6"/>
    <circle cx="182" cy="86" r="17" fill="#1d1f24"/><circle cx="182" cy="86" r="7" fill="#9aa0a6"/>
    <defs>
        <linearGradient id="shine" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#fff"/><stop offset=".6" stop-color="#fff" stop-opacity="0"/>
        </linearGradient>
    </defs>
</svg>
