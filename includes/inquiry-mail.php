<?php

/**
 * Kompakte HTML-Benachrichtigung für neue Anfragen (an den Makler).
 *
 * @param array $d  name, email, phone, subject, form_type, message, created_at, ip,
 *                  property_title, property_url, property_id, rows (Label => Wert)
 */
function build_inquiry_email_html(array $d): string
{
    $e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

    $name    = $e($d['name'] ?? '');
    $email   = (string)($d['email'] ?? '');
    $phone   = trim((string)($d['phone'] ?? ''));
    $phoneDigits = preg_replace('/[^\d+]/', '', $phone);
    $waDigits = ltrim(preg_replace('/\D/', '', $phoneDigits), '0');
    if ($phoneDigits !== '' && $phoneDigits[0] !== '+' && strpos($phoneDigits, '0') === 0) {
        $waDigits = '49' . $waDigits;
    }
    $subject = $e($d['subject'] ?? 'Neue Anfrage');
    $type    = $e($d['form_type'] ?? 'Kontaktanfrage');
    $message = nl2br($e($d['message'] ?? ''));
    $time    = $e($d['created_at'] ?? date('d.m.Y H:i'));

    $rowsHtml = '';
    $addRow = static function (string $label, string $valueHtml) use (&$rowsHtml, $e) {
        $rowsHtml .= '<tr><td style="padding:8px 0;color:#64748b;font-size:13px;width:140px;vertical-align:top;border-bottom:1px solid #f1f5f9;">'
            . $e($label) . '</td><td style="padding:8px 0;color:#0f172a;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;">'
            . $valueHtml . '</td></tr>';
    };

    $addRow('E-Mail', '<a href="mailto:' . $e($email) . '" style="color:#2563eb;text-decoration:none;">' . $e($email) . '</a>');
    $addRow('Telefon', $phone !== ''
        ? '<a href="tel:' . $e($phoneDigits) . '" style="color:#2563eb;text-decoration:none;">' . $e($phone) . '</a>'
        : '<span style="color:#94a3b8;font-weight:400;">nicht angegeben</span>');

    if (!empty($d['property_title']) || !empty($d['property_id'])) {
        $label = $e($d['property_title'] ?: ('Immobilie #' . $d['property_id']));
        $val = !empty($d['property_url'])
            ? '<a href="' . $e($d['property_url']) . '" style="color:#2563eb;text-decoration:none;">' . $label . '</a>'
            : $label;
        $addRow('Immobilie', $val);
    }

    foreach (($d['rows'] ?? []) as $label => $value) {
        $addRow((string)$label, $e($value));
    }

    $buttons = '<a href="mailto:' . $e($email) . '?subject=' . rawurlencode('Re: ' . ($d['subject'] ?? 'Ihre Anfrage')) . '" style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;font-weight:700;font-size:14px;padding:11px 20px;border-radius:8px;margin:0 8px 8px 0;">Antworten</a>';
    if ($phone !== '') {
        $buttons .= '<a href="tel:' . $e($phoneDigits) . '" style="display:inline-block;background:#0f172a;color:#fff;text-decoration:none;font-weight:700;font-size:14px;padding:11px 20px;border-radius:8px;margin:0 8px 8px 0;">Anrufen</a>';
        if ($waDigits !== '') {
            $buttons .= '<a href="https://wa.me/' . $e($waDigits) . '" style="display:inline-block;background:#16a34a;color:#fff;text-decoration:none;font-weight:700;font-size:14px;padding:11px 20px;border-radius:8px;margin:0 8px 8px 0;">WhatsApp</a>';
        }
    }

    return '<!DOCTYPE html><html lang="de"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>'
        . '<body style="margin:0;padding:0;background:#f1f5f9;font-family:Helvetica,Arial,sans-serif;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:24px 12px;">'
        . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">'
        . '<tr><td style="background:#1a3a6b;padding:18px 24px;">'
        . '<div style="color:#93c5fd;font-size:11px;letter-spacing:.08em;text-transform:uppercase;">Neue Anfrage · ' . $type . '</div>'
        . '<div style="color:#fff;font-size:20px;font-weight:700;margin-top:4px;">' . $name . '</div>'
        . '<div style="color:#cbd5e1;font-size:13px;margin-top:2px;">' . $subject . '</div></td></tr>'
        . '<tr><td style="padding:20px 24px 4px;"><table role="presentation" width="100%" cellpadding="0" cellspacing="0">' . $rowsHtml . '</table></td></tr>'
        . '<tr><td style="padding:16px 24px 4px;"><div style="color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Nachricht</div>'
        . '<div style="background:#f8fafc;border-left:4px solid #2563eb;border-radius:6px;padding:12px 14px;color:#1e293b;font-size:14px;line-height:1.6;">' . ($message !== '' ? $message : '–') . '</div></td></tr>'
        . '<tr><td style="padding:18px 24px 8px;">' . $buttons . '</td></tr>'
        . '<tr><td style="padding:10px 24px 18px;color:#94a3b8;font-size:11px;">Eingegangen am ' . $time . ' · über www.immobilienghumman.de</td></tr>'
        . '</table></td></tr></table></body></html>';
}
