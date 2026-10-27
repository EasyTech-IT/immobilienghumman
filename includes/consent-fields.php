<?php
/**
 * Einwilligungs-Felder für Kontaktformulare.
 * $consentPrefix – eindeutiges Präfix für die Element-IDs
 */
$consentPrefix = $consentPrefix ?? 'c';
?>
<div class="consent-box">
    <input type="hidden" name="privacy_required" value="1">
    <label class="consent-row" for="<?= $consentPrefix ?>_privacy">
        <input type="checkbox" id="<?= $consentPrefix ?>_privacy" name="privacy_accept" value="1" required>
        <span>Ich habe die <a href="<?= BASE ?>/datenschutz" target="_blank" rel="noopener">Datenschutzerklärung</a> gelesen und bin mit der Verarbeitung meiner Angaben zur Bearbeitung der Anfrage einverstanden. *</span>
    </label>
    <label class="consent-row" for="<?= $consentPrefix ?>_news">
        <input type="checkbox" id="<?= $consentPrefix ?>_news" name="newsletter_optin" value="1">
        <span>Ich möchte per E-Mail über <strong>neue Immobilien</strong> informiert werden (optional, jederzeit abbestellbar). Sie erhalten dafür eine Bestätigungs-Mail.</span>
    </label>
</div>
<style>
    .consent-box {
        display: grid;
        gap: .6rem;
        text-align: left;
    }

    .consent-row {
        display: flex;
        gap: .6rem;
        align-items: flex-start;
        font-size: .85rem;
        line-height: 1.5;
        color: #4b5563;
        cursor: pointer;
        font-weight: 400;
        margin: 0;
    }

    .consent-row input[type=checkbox] {
        flex: none;
        width: 20px;
        height: 20px;
        margin-top: 2px;
        accent-color: #2563eb;
    }

    .consent-row a {
        color: #2563eb;
        text-decoration: underline;
    }
</style>
