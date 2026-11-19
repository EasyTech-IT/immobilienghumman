<?php

function property_public_extra_data(array $extra): array
{
    unset(
        $extra['address_public_note'],
        $extra['legacy_slugs'],
        $extra['documents'],
        $extra['private_expose'],
        $extra['energy_issue_date'],
        $extra['energy_efficiency_class_not_in_certificate']
    );
    $extra['hide_exact_address'] = true;
    return $extra;
}
